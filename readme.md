# PHP Container

Simple Dependency Injection Container.

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

// map your own methods
$app = Container::getInstance();
$app->map('sum', fn(...$args) => array_sum($args));
echo $app->sum(1000, 300, 30, 5, 2) . PHP_EOL; // 1337
echo $app->sum(1000, 900, 90, 7) . PHP_EOL; // 1997

// to execute the function once
$app = Container::getInstance();
$app::mapOnce('timestamp', fn() => time());
echo Container::timestamp() . PHP_EOL; // 1607881889
sleep(3);
echo $app->timestamp() . PHP_EOL; // 1607881889
```