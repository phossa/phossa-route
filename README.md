# phossa-route

Introduction
---

**Phossa-route** is a *fast*, *full-fledged* and *feature-rich* application
level routing library for PHP. It dispatches requests base on URLs, HTTP
headers, session informations etc.

It requires PHP 5.4 and supports PHP 7.0+, HHVM. It is compliant with
[PSR-1][PSR-1], [PSR-2][PSR-2], [PSR-4][PSR-4].

[PSR-1]: http://www.php-fig.org/psr/psr-1/ "PSR-1: Basic Coding Standard"
[PSR-2]: http://www.php-fig.org/psr/psr-2/ "PSR-2: Coding Style Guide"
[PSR-4]: http://www.php-fig.org/psr/psr-4/ "PSR-4: Autoloader"

Why another routing library ?
---

- Supports different [routing strategies](#strategy).

- Supports different [regular expression algorithms](#algorithm).

- Concise [route syntax](#syntax). Route parameters and optional route segments.

- Multiple routing [collections](#collector) allowed

- Miltiple level [extensions](#extension) supported, namely dispatcher level,
  routing collection level, single route level. Able to fine control of routing
  process.

- Dependency injection ready. Support third-party Di libraries

- [Fast](#performance) ! If it does matters to you.

Getting started
---

- **Installation**

  Install via the `composer` utility.

  ```
  composer require "phossa/phossa-route=1.*"
  ```

  or add the following lines to your `composer.json`

  ```json
  {
      "require": {
        "phossa/phossa-route": "^1.0.0"
      }
  }
  ```

- **Simple usage**

  ```php
  use Phossa\Route;

  // route collector
  $collector  = new Route\Collector\Collector();

  // add routes
  $collector->addGet(
                '/blog/{action:xd}[/{year:d}[/{month:d}[/{date:d}]]]',
                function($result) {
                    echo "action is " . $result->getParameter('action');
                })
            ->addPost('/blog/post', 'handler2')
            ->addRoute(new Route\Route(
                'GET,HEAD', // support these methods
                '/blog/read[/{id:d}]',
                'handler3',
                ['id' => '1'] // default $id value
            ));

  // route dispatcher
  $dispatcher = new Route\Dispatcher($collector, new Route\ResolverAbstract());

  // query parameter based routing
  $collQpr = new Route\Collector\\CollectorQPR();
  $dispatcher->addCollector($collQpr);

  // route thru first collector
  // $dispatcher->dispatchUrl('GET', '/blog/list/2016');

  // old style route still supported
  // $dispatcher->dispatchUrl('GET', '/blog?r=blog-list-uear-2016');
  ```

<a name="syntax"></a>Route syntax
---

- **Placeholders**

  A route pattern syntax is used where `{foo}` specifies a placeholder or
  parameter with name `foo` and matching the string `[^/]++`. In order to match
  more specific types, you can specify a custom regex pattern by writing
  `{foo:[0-9]+}`.

  Predefined shortcuts for placeholder patterns as follows,

  ```php
  ':d}'   => ':[0-9]++}',             // digit only
  ':l}'   => ':[a-z]++}',             // lower case
  ':u}'   => ':[A-Z]++}',             // upper case
  ':a}'   => ':[0-9a-zA-Z]++}',       // alphanumeric
  ':c}'   => ':[0-9a-zA-Z+_\-\.]++}', // common chars
  ':nd}'  => ':[^0-9/]++}',           // not digits
  ':xd}'  => ':[^0-9/][^/]*+}',       // no leading digits
  ```

- **Pick a routing scheme**

  Pick a [routing scheme](#scheme) which is good enough for you.

Routing issues
---

Base on the request informations, such as request device, source ip, request
method etc., service provider may direct request to different hosts, servers,
app modules or handlers.

- **Diffrent routing strategies**

  - *Network level routing*

    Common case, such as routing based on request's source ip, routes the
    request to a *NEAREST* server, this is common in content distribution
    network (CDN), and is done at network level.

  - *Web server routing*

    For performance reason, some of the simple routing can be done at web
    server level, such as using apache or ngix configs to do simple routing.

    For example, if your server goes down for maintenance, you may replace
    the `.htaccess` file as follows,

    ```
    DirectorySlash Off
    Options -MultiViews
    DirectoryIndex maintenance.php
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-l
    RewriteRule ^ maintenance.php [QSA,L]
    ```

  - *App server routing*

    App server routing is what we are focusing on here. It solves much more
    complicated issues, and much more flexible. High performance can be
    achieved by using effective algorithms.

    Usually, routing is done at a single point `index.php`. All the requests
    are configured to be handled by this script first and routed to different
    routines.

- **App routing & utilities**

  - *Routing*

    - Pick a right handler

      The core target of app routing library is to pick a right handler for the
      given URL or other infomations.

    - Pick a right host

      Sometimes, redirecting is wanted. For example, redirecting to a mobile
      content server or a secure (https) server.

  - *Routing utilities*

    - Parse input parameters

      Usually, the by-product of URL matching is to parse input parameters and
      pass over to the handler.

    - Parameter validations

      Usually this is done in the handler but can be extracted out to do some
      common and simple validations.

    - Execution of common routines

      Some URLs can be configured to execute some routines, such as
      authentication routine, before handling control over to the handler.

<a name="scheme"></a>URL routing schemes
---

There are couple of URL based routing schemes supported in this library.

- **Query Parameter Routing (QPR)**

  The routing info is directly embedded in the URL query. The advantage of this
  scheme is fast and clear.

  ```
  http://servername/path/index.php?c=controller&a=action&id=1&name=nick
  ```

  Or use a single parameter

  ```
  http://servername/path/?r=controller-action-id-1-name-nick
  ```

- **Parameter Pairs Routing (PPR)**

  Using parameter and value pairs like the following

  ```
  http://servername/path/index.php/controller/action/id/1/name/nick
  ```

  Parameters order can be arbitary, but have to appear in pairs. Advantage of
  this scheme is fast, web crawler friendly and easy for static file caching.
  If URL rewriting is used, the above can be written into the following,

  ```
  http://servername/path/controller/action/id/1/name/nick
  ```

  Instead of using '/' as the parameter seperator, any URL valid characters
  except for the '?' and '&' can be used as a seperator.

  ```
  http://servername/path/controller-action-id-1-name-nick
  ```

- **Regular Expression Routing (RER)**

  Regular expression based routing. Pattern

  ```
  $pattern = '/user/{method}[/{id:\d+}[/{name}]]';
  ```

  To match the following urls,

  ```
  // user id provided
  http://servername/path/user/list/20162

  // user id & name
  http://servername/path/user/list/20162/phossa
  ```

Usage
---

- **Simple usage**

Features
---

- Parameters & placeholders

- In addition to URL, routing base on HTTP headers

- Support PHP 5.4+

- PHP7 ready for return type declarations and argument type declarations.

Public APIs
--
- `Phossa\Route\RouteCollector` APIs

  - `__construt(RouteInterpolator, HandlerResolver $handlerResolver = null)`

  - `addRoute(string|callable $pattern, string|callable $methodOrCallable = 'GET,HEAD', array $defauls = []): this`

    Add one route to the collector.

    If `$pattern` is a string, it will be interpreted as a pattern to match
    against `$_SERVER['PATH_INFO']`. If it is a callable, the signature is
    `function(Request|string $requestOrUri): bool {}`.

    The last parameter specifies the HTTP method in upper case, either 'GET',
    'POST' or 'GET,POST' etc. If it is a callable, then it will be executed
    and the signature is `function(Request $request): bool {}`.

  - `loadData(array|string $fileOrArray): array`

    Load the routes data.

  - `getData(): array`

    Get the routes data.

- `Phossa\Route\Dispatcher` APIs

  - `__construt(array $routeData)`

    Instantiation of the dispatcher with one `RouteCollector`.

    ```php
    $dispatcher = new Dispatcher(
        (new RouteCollector)
            ->addRoute('user', '/user')
            ->addRoute('post', '/posts/[id:\d+]')
            ->getData()
    );
    ```

  - `match(string|Request $uriOriRequest, string $httpMethod = 'GET,HEAD'): array`

    Match base on request object or given uri. Returns result array

  - `dispatch(string|Request $uriOrRequest, string $httpMethod = 'GET,HEAD'): static`

    Match and dispatch the result array to corresponding handler.

Performance
---

#### Worst-case matching

This benchmark matches the last route and unknown route. It generates a
randomly prefixed and suffixed route in an attempt to thwart any optimization.
1,000 routes each with 8 arguments.

This benchmark consists of 14 tests. Each test is executed 1,000 times, the
results pruned, and then averaged. Values that fall outside of 3 standard
deviations of the mean are discarded.

Test Name | Results | Time | + Interval | Change
--------- | ------- | ---- | ---------- | ------
Phossa PPR - unknown route (1000 routes) | 998 | 0.0000724551 | +0.0000000000 | baseline
Phossa PPR - last route (1000 routes) | 993 | 0.0000925307 | +0.0000200755 | 28% slower
Symfony2 Dumped - unknown route (1000 routes) | 998 | 0.0004353616 | +0.0003629065 | 501% slower
Phroute - last route (1000 routes) | 999 | 0.0006205601 | +0.0005481050 | 756% slower
Phossa - unknown route (1000 routes) | 998 | 0.0006903790 | +0.0006179239 | 853% slower
FastRoute - unknown route (1000 routes) | 1,000 | 0.0006911943 | +0.0006187392 | 854% slower
FastRoute - last route (1000 routes) | 999 | 0.0006962751 | +0.0006238200 | 861% slower
Phroute - unknown route (1000 routes) | 998 | 0.0007134676 | +0.0006410125 | 885% slower
Symfony2 Dumped - last route (1000 routes) | 993 | 0.0008066097 | +0.0007341545 | 1013% slower
Phossa - last route (1000 routes) | 998 | 0.0009104498 | +0.0008379947 | 1157% slower
Symfony2 - unknown route (1000 routes) | 989 | 0.0023998006 | +0.0023273455 | 3212% slower
Symfony2 - last route (1000 routes) | 999 | 0.0025880890 | +0.0025156339 | 3472% slower
Aura v2 - last route (1000 routes) | 981 | 0.0966411463 | +0.0965686912 | 133281% slower
Aura v2 - unknown route (1000 routes) | 992 | 0.1070026719 | +0.1069302168 | 147581% slower


#### First route matching

This benchmark tests how quickly each router can match the first route. 1,000
routes each with 8 arguments.

This benchmark consists of 7 tests. Each test is executed 1,000 times, the
results pruned, and then averaged. Values that fall outside of 3 standard
deviations of the mean are discarded.

Test Name | Results | Time | + Interval | Change
--------- | ------- | ---- | ---------- | ------
FastRoute - first route | 999 | 0.0000403543 | +0.0000000000 | baseline
Phroute - first route | 998 | 0.0000405911 | +0.0000002368 | 1% slower
Symfony2 Dumped - first route | 999 | 0.0000590617 | +0.0000187074 | 46% slower
Phossa PPR - first route | 977 | 0.0000678727 | +0.0000275184 | 68% slower
Phossa - first route | 999 | 0.0000898475 | +0.0000494932 | 123% slower
Symfony2 - first route | 998 | 0.0003983802 | +0.0003580259 | 887% slower
Aura v2 - first route | 986 | 0.0004391784 | +0.0003988241 | 988% slower

Dependencies
---

- PHP >= 5.4.0

- phossa/phossa-shared >= 1.0.6

License
---

[MIT License](http://mit-license.org/)

Appendix
---

- **URL rewrite**

  Setup URL rewriting to do routing with `index.php`

  - Apache `.htaccess` with `mod_rewrite` engine is on

    ```
    DirectorySlash Off
    Options -MultiViews
    DirectoryIndex index.php
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-l
    RewriteRule ^ index.php [QSA,L]
    ```

    and in your `httpd.conf` file to enable using of `.htaccess`

    ```
    <VirtualHost *:80>
      ServerAdmin me@mysite.com
      DocumentRoot "/path/www.mysite.com/public"
      ServerName mysite.com
      ServerAlias www.mysite.com

      <Directory "/path/www.mysite.com/public">
        Options -Indexes +FollowSymLinks +Includes
        AllowOverride All
        Order allow,deny
        Allow from all
      </Directory>
    </VirtualHost>
    ```

  - Nginx configration in `nginx.conf`

    ```
    server {
        listen       80;
        server_name  www.mysite.com mysite.com;
        root         /path/www.mysite.com/public;

        try_files $uri /index.php;

        # this will only pass index.php to the fastcgi process
        location /index.php {
            fastcgi_connect_timeout 3s;
            fastcgi_read_timeout 10s;
            include fastcgi_params;
            fastcgi_pass 127.0.0.1:9000;
        }
    }
    ```