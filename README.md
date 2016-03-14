# phossa-route

Introduction
---

**phossa-route** is a *fast*, *full-fledged* and *feature-rich* application
level routing library for PHP. It dispatches requests base on URLs, HTTP
headers, session informations etc. It requires PHP 5.4 and supports PHP 7.0+,
HHVM. It is compliant with [PSR-1][PSR-1], [PSR-2][PSR-2], [PSR-4][PSR-4].

[PSR-1]: http://www.php-fig.org/psr/psr-1/ "PSR-1: Basic Coding Standard"
[PSR-2]: http://www.php-fig.org/psr/psr-2/ "PSR-2: Coding Style Guide"
[PSR-4]: http://www.php-fig.org/psr/psr-4/ "PSR-4: Autoloader"

Why another routing library ?
---


- [Super fast](#performance) ! If it matters to you.

- Support different [routing strategies](#strategy) and combinations of these
  strategies.

- Support different [regular expression routing algorithms](#algorithm)
  including the [fastRoute algorithm](http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html)

- [Concise route syntax](#syntax). Route parameters and optional route segments.

- [Multiple routing collections](#collector) allowed.

- Different level of [default handlers](#default).

- Fine control of routing process by [multiple level of extensions](#extension).

- Dependency injection ready. Support third-party Di libraries

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

  // default regex route collector (collection of routes)
  $collector = new Route\Collector\Collector();

  // add routes
  $collector->addGet(
                '/blog/{action:xd}[/{year:d}[/{month:d}[/{date:d}]]]',
                function($result) {
                    echo "action is " . $result->getParameter('action');
                })
            ->addPost('/blog/post', 'handler2')
            ->addRoute(new Route\Route(
                'GET,HEAD', // support multiple methods
                '/blog/read[/{id:d}]',
                'handler3',
                ['id' => '1'])); // default $id value

  // route dispatcher
  $dispatcher = new Route\Dispatcher($collector, new Route\Handler\ResolverAbstract());

  // legacy query parameter based routing
  $collQpr = new Route\Collector\\CollectorQPR();
  $dispatcher->addCollector($collQpr);

  // route thru first collector
  // $dispatcher->dispatchUrl('GET', '/blog/list/2016');

  // old style routes still supported
  // $dispatcher->dispatchUrl('GET', '/blog?r=blog-list-year-2016');
  ```

<a name="syntax"></a>Route syntax
---

- **Placeholders**

  A route pattern syntax is used where `{foo}` specifies a placeholder or
  parameter with name `foo` and default pattern `[^/]++`. In order to match
  more specific types, you may specify a custom regex pattern like
  `{foo:[0-9]+}`.

  ```php
  $pattern = '/user/{action:[^0-9/][^/]*}/{id:[0-9]+}';
  ```

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
- **Optional segments**

  Optional segments in the route can be specified with `[]` like the following,

  ```php
  $pattern = '/blog[/{action:xd}][/{year:d}[/{month:d}[/{date:d}]]]';
  ```

  where optional segments can be **nested**. Unlike other libraries, optional
  segments are not limited to the end of the pattern, as long as it is a valid
  pattern like the `[/{action:xd}]` in the example.

- **Syntax issues**

  - Use of `[]` outside placeholders is not allowed

    `[]` can not be used outside placeholders, *IF YOU DO NEED* to use them
    as part of the pattern, please include them inside a placeholder.

  - Use of `()` inside placeholders is not allowed

    Capturing groups `()` can not be used inside placeholders. For example
    `{user:(root|phossa)}` is not valid. Instead, you can use either use
    `{user:root|phossa}` or `{user:(?:root|phossa)}`.

<a name="routes"></a>Defining routes
---

- **Defining routes with `Phossa\Route\Collector\CollectorInterface`**

  Since multiple collectors (route collections) are supported in the dispatcher,
  routes are defined with collectors, but not with the dispatcher.

- **Defining routes**

  ```php
  // define a GET route
  $collector->addGet($routePattern, $handler, $defaultValues);

  // define a POST route
  $collector->addPost($routePattern, $handler, $defaultValues);

  // multiple methods wanted
  $collector->addRoute(new Phossa\Route\Route('GET,HEAD', $routePattern, $handler, $defaultValues));
  ```

  `addGet()` and `addPost()` are wrappers of `addRoute(RouteInterface)`.

- **<a name="collector"></a>Multiple routing collections**

  Routes can be grouped into different collections by using multiple collectors.

  ```php
  // '/user' related
  $collector_user = (new Route\Collector\Collector())
      ->addGet('/user/list/{id:d}', 'handler1')
      ->addGet('/user/view/{id:d}', 'handler2')
      ->addPost('/user/new', 'handler3');

  // '/blog' related
  $collector_blog = (new Route\Collector\Collector())
      ->addGet('/blog/list/{user_id:d}', 'handler4')
      ->addGet('/blog/read/{blog_id:d}', 'handler5');

  $dispatcher->addCollector($collector_user)
             ->addCollector($collector_blog);
  ```

<a name="dispatch"></a>Dispatching
---

- **Dispatch with `dispatch()`**

  In the script `index.php`, the dispatcher is normally the last line.

  ```php
  // index.php
  // ...

  // dispatch base on request info
  $dispatcher->dispatch();
  ```

  `dispatch()` takes no arguments, it will collect informations from super
  globals like `$_SERVER` and `$_REQUEST` and dispatches to the right routine
  or callable base on route definition.

- **Dispatch with an Url**

  Inside script, user may redirect to a new url by,

  ```php
  $dispatcher->dispatchUrl('GET', '/error404');
  ```
- **Match instead of dispatching**

  Instead of executing handler, more control if using `match()`

  ```php
  // use info from $_SERVER etc.
  if ($dispatcher->match()) {
      switch($result->getStatus()) {
          case 200:
            // ...
            break;
          case 404:
            // ...
            break;
          default:
            // ...
            break;
      }
  } else {
      // no match found
      // ...
  }
  ```

  `matchUrl()` is also provided.

<a name="handler"></a>Handlers and default handlers
---

- **Multiple handlers**

  Handlers are supported not only for the `200 OK` status, but also for other
  matching status.

  ```php
  use Phossa\Route\Route;
  use Phossa\Route\Status;

  $route = (new Route('GET', '/user/{action:xd}/{id:d}',
              // handler for Status::OK
              function($result) {
                  $user_id = $result->getParameter('id');
                  // ...
              }
          )
          // add handler for METHOD_NOT_ALLOWED
          ->addHandler(Status::METHOD_NOT_ALLOWED, 'handler1');
  ```

  Handler `handler1` will be executed if route matches but method is not right.

- **<a name="default"></a>Default handlers**

  Like routes have different handlers, dispatcher and collectors can have
  default handlers if no handler set in the result.

  Dispatcher level handlers,

  ```php
  use Phossa\Route\Status;
  // ...

  $dispatcher
    ->addHandler(
        Status::MOVED_PERMANENTLY, function($result) {
        // ...
        })
    ->addHandler(
        Status::SERVICE_UNAVAILABLE, function($result) {
        });
    // ...
  ```

  Same thing applies to the collectors.

- **Handler resolving**

  Most of the time, routes returns a handler like `[ 'className', 'method' ]`.
  The handler resolver is used to resolving this pseudo handler into real
  callable.

  ```php
  use Phossa\Route;

  // dispatcher with default resolver
  $dispatcher = new Route\Dispatcher(
      new Route\Collector\Collector(),
      new Route\Handler\ResolverAbstract()
  );
  ```

  Users may write their own handler resolver by extending `ResolverAbstract`
  class.

<a name="strategy"></a>Extensions
---


<a name="strategy"></a>Routing strategies
---

There are a couple of URL based routing strategies supported by default in this
library. Different strategies can be combined together in one dispatcher.

- **Query Parameter Routing (QPR)**

  The routing info is directly embedded in the URL query. The advantage of this
  scheme is fast and clear.

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

  **RER** is the routing strategy of `Phossa\Route\Collector\Collector` class.

<a name="algorithm"></a>Regex matching algorithms
---

- **FastRoute algorithm**

  This *Group Count Based algorithm* is implemented in
  `Phossa\Route\Regex\ParserGcb` class and explained in  detail in this
  [article](http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html).

  **phossa-route** uses this algorithm by default.

-- **Standard algorithm**

  This algorithm is developed by phossa-route and a little bit slower than the
  fastRoute GCB algorithm. It is implemented in `Phossa\Route\Regex\ParserStd`
  class.

- **Comments on routing algorithm**

  - It does **NOT** matter that much as you may think.

    If you are using routing library in your application, different algorithms
    may differes only 0.1 - 0.2ms which is meaningless for a big application.

  - If you **DO** care about routing speed

    Use different routing strategy like *Parameter Pairs Routing (PPR)* which
    is much [faster](#performance) than the regex based routing.

    Also by carefully design your routes, you may achieve better results even
    if you are using a slower algorithm.

  - Try [network routing or server routing](#issue) if you just can NOT help
    it.


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



Dependencies
---

- PHP >= 5.4.0

- phossa/phossa-shared >= 1.0.6

License
---

[MIT License](http://mit-license.org/)

Appendix
---

- <a name="performance"></a>Performance

  ### Worst-case matching

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

  ### First route matching

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

- <a name="issue"></a>Routing issues

  Base on the request informations, such as request device, source ip, request
  method etc., service provider may direct request to different hosts, servers,
  app modules or handlers.

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

  - *App level routing*

    It solves much more complicated issues, and much more flexible.

    Usually, routing is done at a single point `index.php`. All the requests
    are configured to be handled by this script first and routed to different
    routines.
