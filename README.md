# phossa-route

Introduction
---

phossa-route is XXXXX libraray for PHP.

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
        "phossa/phossa-route": "^1.0.1"
      }
  }
  ```

- **Simple usage**

  You might have serveral simple classes like these or third party libraries,
  and want to make avaiable as services.

  ```php
  class MyCache
  {
      private $driver;

      public function __construct(MyCacheDriver $driver)
      {
          $this->driver = $driver;
      }

      // ...
  }
  ```

Features
---

- <a name="auto"></a>**Auto wiring**

  *Auto wiring* is the ability of container instantiating objects and resolving
  its dependencies automatically. The base for auto wiring is the PHP function
  parameter *type-hinting*.

- <a name="php"></a>**Required php versions**

  - Support PHP 5.4+

  - PHP7 ready for return type declarations and argument type declarations.

Public APIs
--

- [PSR-11][PSR-11] compliant APIs

  - `get(string $id): object`

    Getting the named service from the container.

Version
---

1.0.1

Dependencies
---

- PHP >= 5.4.0

- phossa/phossa-shared >= 1.0.6


License
---

[MIT License](http://mit-license.org/)
