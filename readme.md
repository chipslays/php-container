# PHP Container

Simple Dependency Injection Container.

## Installation
```bash
composer require aethletic/php-container
```

## Example
```php
use Container\Container;

require 'vendor/autoload.php';

Container::set('name', 'Aethletic');
echo Container::get('name') . PHP_EOL; // Aethletic

$app = Container::getInstance();
echo $app->name . PHP_EOL; // Aethletic
// Container::$name; // Uncaught Error: Access to undeclared static property

echo Container::get('age', 'default age is: 17') . PHP_EOL; // default age is: 17

Container::has('name'); // true
Container::has('age'); // false

Container::remove('name'); // remove data for `name` key
Container::clear(); // clear all save data, except mapped methods
```

```php
// map your own methods
$app = Container::getInstance();
$app->map('sum', fn(...$args) => array_sum($args));
echo $app->sum(1000, 300, 30, 5, 2) . PHP_EOL; // 1337
echo Container::sum(1000, 900, 90, 7) . PHP_EOL; // 1997
```

```php
// to execute the function once
$app = Container::getInstance();
$app::mapOnce('timestamp', fn() => time());
echo Container::timestamp() . PHP_EOL; // 1607881889
sleep(3);
echo $app->timestamp() . PHP_EOL; // 1607881889
```

### Use cases

```php
use Container\Container as App;
use Illuminate\Database\Capsule\Manager as Capsule;

$app = App::getInstance();

$app->mapOnce('db', function () use ($app) {
    $capsule = new Capsule;
    $capsule->addConnection(require __DIR__ . '/config/database.php');
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
});

// use
$app->db()->table(...)->insert(...);
```

```php
use Container\Container as App;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ChromePHPHandler;

$app = App::getInstance();

$app->mapOnce('logger', function () {
    $logConfig = require __DIR__ . '/config/log.php';
    $logger = new Logger('app');
    $logger->pushHandler(new StreamHandler($logConfig['log_dir'], Logger::DEBUG));
    $logger->pushHandler(new ChromePHPHandler);
    return $logger;
});

$app->logger()->error(...);
```

```php
$app->mapOnce('router', fn () => new Router([
    'base_folder' => __DIR__,
    'main_method ' => 'index',
    'paths' => [
        'controllers' => '/app/Controllers',
        'middlewares' => '/app/Middlewares',
    ],
    'namespaces' => [
        'controllers' => '\App\Controllers',
        'middlewares' => '\App\Middlewares',
    ]
]));

$app->map('run', fn () => $app->router()->run());

// use
$app->router()->get('/', fn() => ...);
$app->run();
```
