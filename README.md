<div align="center">
<p>
  <img alt="" width="350" src="https://upload.david-dewes.de/Logo-Crop-Without-Slogan.png">
</p>
</div>

------------------------------------

[![PHPStan](https://github.com/david-prv/QuickyPHP/actions/workflows/phpstan.yml/badge.svg)](https://github.com/david-prv/QuickyPHP/actions/workflows/phpstan.yml) [![PHPMD](https://github.com/david-prv/QuickyPHP/actions/workflows/phpmd.yml/badge.svg)](https://github.com/david-prv/QuickyPHP/actions/workflows/phpmd.yml)  [![PHPCS](https://github.com/david-prv/QuickyPHP/actions/workflows/phpcs.yml/badge.svg)](https://github.com/david-prv/QuickyPHP/actions/workflows/phpcs.yml)

A php micro-framework for simple and quick web-applications

> [!IMPORTANT]
> This project will not receive any major updates in the near future. I have paused development indefinitely as I no longer have enough freetime. QuickyPHP has not been discontinued, development is just delayed.

👉 Moved to [https://codeberg.org/david-prv/QuickyPHP](https://codeberg.org/david-prv/QuickyPHP)

## Motivation
I started this project because I wanted to procrastinate important work for university. No joke. But it turned into a slight obsession that has been with me for a few days now. I found developing my own PHP micro-framework so interesting that I kept reading up on documentation and articles and watching tutorial after tutorial.

The framework has the sense to be structured as simple as possible, to be easily customizable by anyone to their needs. I also experimented with technologies that I had never used before but found in other projects or got to know at university (e.g. method dispatcher or reflection classes). Also, the project was partly done in collaboration with ChatGPT (OpenAI), which was also a memorable experiment.

I got the idea of how a simple PHP framework works from other open source projects. Here is a selection (if you read it carefully, you will quickly see parallels to my framework and its structure):
- [FlightPHP](https://flightphp.com/)
- [SlimFramework](https://www.slimframework.com/)
- [CakePHP](https://cakephp.org/)
- [Laravel Lumen](https://lumen.laravel.com/docs/10.x)

## Example Application
A simple web application powered by this framework:
```php
require __DIR__ . "/../vendor/autoload.php";

use Quicky\Http\Request;
use Quicky\Http\Response;
use Quicky\App;

$app = App::create();

App::route("GET", "/", function(Request $request, Response $response) {
    $response->write("Hello World");
    return $response;
});

$app->run();
```

## More Features
### Flexible Factory
You can build complex application configurations with the in-built AppFactory very easily!
```php
$app = AppFactory::empty()
  ->catch("exception", function (Throwable $exception) { ... })
  ->state("development")
  ->middleware(RateLimitMiddleware::class, 1, 5)
  ->alias("sayHello", function () { echo "Hello World"; })
  ->build();
```

### Automatic Dispatching
This framework will automatically search for the correct method to dispatch, for any static invocation.
```php
use Quicky\Interfaces\DispatchingInterface;

class MyTest implements DispatchingInterface
{
  private array $dispatching;

  public function __construct()
  {
    $this->dispatching = array("test");
  }

  public function dispatches(string $method): bool
  {
    return in_array($method, $this->dispatching);
  }

  public function test(): void
  {
    echo "I'm a test";
  }
}
```

### Secure Routing
Protect your application with security sensitive middleware to prevent basic attack patterns.
```php
use Quicky\Middlewares\RateLimitMiddleware;
use Quicky\Middlewares\CORSMiddleware;
use Quicky\Middlewares\LoggingMiddleware;

// Routes can be accessed once every 5 seconds
App::use("middleware", new RateLimitMiddleware(1, 5));

// This route additionally sets special CORS headers & enables logging
App::route("GET", "/admin", function (Request $request, Response $response) {
  $response->render("admin.dashboard");
  return $response;
})
->middleware(new CORSMiddleware())
->middleware(new LoggingMiddleware());
```

## Requirements
QuickyPHP requires PHP 7.4+ or PHP 8 ([check compatibility](https://github.com/david-prv/QuickyPHP/blob/main/COMPATIBILITY.md)) and a webserver that supports Rewrite Rules.  
Note: Composer Version 2 is required to find and install the package.

## Installation
### Download files:
#### Via Composer
Install the project via command-line:
```bash
composer create-project david-prv/quickyphp
```

#### Via GitHub

Create a project folder:
Download git repository:
```bash
git clone https://github.com/david-prv/QuickyPHP.git
```
### Install requirements
Install without development requirements:
```bash
composer install
```

Use the `--no-dev` tag, if you want to skip the development dependencies. If you also want to skip the platform requirement check, use the tag `--ignore-platform-reqs`, which is not recommended.

## CLI Usage
Start local PHP development server
```bash
php quicky-cli start [<address> [<port>]]

e.g. php quicky-cli start localhost 3000
```
Clear logs
```bash
php quicky-cli clear logs
```
Clear cache
```bash
php quicky-cli clear cache
```
Restore default configuration
```bash
php quicky-cli config restore
```
Update configuration
```bash
php quicky-cli config set <field> <value>

e.g. php quicky-cli config set project.env production
```

## Contributing
Please feel free to contribute to this project. I am always happy to see new and fresh ideas.  
[Learn more](https://github.com/david-prv/QuickyPHP/blob/main/CONTRIBUTING.md)

## Support
If you like what I do, feel free to buy me a coffee for my work.  
Programming early in the morning is hard without a good cup of this magical liquid.

Click here to support me:

<a href="https://www.buymeacoffee.com/david.dewes">
    <img src="https://media3.giphy.com/media/TDQOtnWgsBx99cNoyH/giphy.gif" height="80" alt="buy me a coffee!"/>
</a>

## License
Released under [GPL](/LICENSE) by [@david-prv](https://github.com/david-prv).  

![image](https://github.com/david-prv/scanner-bundle/assets/66866223/385b8bb1-4dc1-48f9-bfc7-e58be51823f1)
