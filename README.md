# phossa-route

Introduction
---

phossa-route is a HTTP response management libraray for PHP. It routes base on
HTTP request including URL, cookies, HTTP headers (such as `Accept-Language`)
etc. It responses with HTTP redirecting or transparent URL rewriting. Also it
gives suggestions for HTTP response headers (cache related etc.)

It requires PHP 5.4 and supports PHP 7.0+, HHVM. It is compliant with
[PSR-1][PSR-1], [PSR-2][PSR-2], [PSR-4][PSR-4].

[PSR-1]: http://www.php-fig.org/psr/psr-1/ "PSR-1: Basic Coding Standard"
[PSR-2]: http://www.php-fig.org/psr/psr-2/ "PSR-2: Coding Style Guide"
[PSR-4]: http://www.php-fig.org/psr/psr-4/ "PSR-4: Autoloader"

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

- Setup URL rewriting to do routing with `index.php`

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

- Pick a routing scheme

  Pick a [routing scheme][scheme] which is good enough for you.

Routing issues
---

Base on request informations, such as request device, source ip, request method
etc., service provider may direct request to different hosts, app servers, app
modules or handlers.

- **Diffrent routing strategies**

  - *Network level routing*

    Common case like routing base on request's source ip, route the request to
    a *NEAREST* server, this is common in content distribution network (CDN),
    and is done at network level.

  - **Web server routing**

    For performance reason, some of the simple routing can be done at web
    server level, such as using apache or ngix configs to do simple routing.

  - *App server routing*

    App server routing is what we are focusing on here. It solves much more
    complicated issues, and much more flexible. High performance can be
    achieved by using effective algorithms.

    Usually, routing is done at a single point `index.php`. All the requests
    are configured to be handled by this script first and routed to different
    routines.

- **App routing & utilities**

  - *Routing: pick the right handler*

    The core target of routing library is to pick a right handler for the given
    URL or other infomations.

  - *Routing utilities*

    - Parse input parameters

      Usually, the by-product of URL matching is to parse input parameters and
      pass over to the handler.

    - Parameter validations

      Usually this is done in the routine, but can be extracted out to do some
      simple validations.

    - Execution of common routines

      Some URLs can be configured to execute some routines, such as
      authentication routine, before handling control over to the handler.

<a name="scheme"></a>URL routing schemes
---

There are different routings schemes base on server setup and the way user
prefers for their URLs.

- Query parameter routing (QPR)

  The routing info is directly embedded in the URL query

  ```
  http://servername/path/index.php?c=controller&a=action&id=1&name=nick
  ```

  Or

  ```
  http://servername/path/?r=controller-action-id-1-name-nick
  ```

- Predefined parameter pairs (PPP)

  Using predefined parameter and value pairs like the following

  ```
  http://servername/path/index.php/controller/action/id/1/name/nick
  ```

  Parameter orders can be arbitary, but have to appear in pairs. Advantage of
  this scheme is fast and clean heirachy for static file caching. If URL
  rewriting is used, the above can be written into the following,

  ```
  http://servername/path/controller/action/id/1/name/nick
  ```

  Instead of using '/' as the parameter seperator, any URL valid characters
  except for the '?' and '&' can be used as a seperator.

  ```
  http://servername/path/controller-action-id-1-name-nick
  ```

Usage
---

- **Simple usage**

Features
---

- Support internal routing

- Parameters & placeholders

- In addition to URL, routing base on HTTP headers

- Support PHP 5.4+

- PHP7 ready for return type declarations and argument type declarations.

Public APIs
--

Dependencies
---

- PHP >= 5.4.0

- phossa/phossa-shared >= 1.0.6

License
---

[MIT License](http://mit-license.org/)
