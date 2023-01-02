# QuickyPHP
A php micro-framework for simple and quick web-applications

## Motivation
I started this project because I wanted to procrastinate important work for university. No joke. But it turned into a slight obsession that has been with me for a few days now. I found developing my own PHP micro-framework so interesting that I kept reading up on documentation and articles and watching tutorial after tutorial.

The framework has the sense to be structured as simple as possible, to be easily customizable by anyone to their needs. I also experimented with technologies that I had never used before but found in other projects or got to know at university (e.g. method dispatcher or reflection classes). Also, the project was partly done in collaboration with ChatGPT (OpenAI), which was also a memorable experiment.

I got the idea of how a simple PHP framework works from other open source projects. Here is a selection (if you read it carefully, you will quickly see parallels to my framework and its structure):
- [FightPHP](https://flightphp.com/)
- [SlimFramework](https://www.slimframework.com/)
- [CakePHP](https://cakephp.org/)

## Sneak Peak
A simple web application powered by this framework:
```php
require __DIR__ . "/quicky/autoload.php";

$app = Quicky::create();

Quicky::get("/", function(Request $request, Response $response) {
    $response->send("Hello World");
});

$app->run();
```

## Work in progress
#### ToDo:
- [ ] Create a logo  
- [ ] Write tests
- [ ] Write documentation
- [ ] Host a website
- [ ] Implement DynamicLoader->findMethod() as BST
#### Done:
- [x] ~~Find a project name~~  
- [x] ~~Buy domain: quickyphp.de~~
- [x] ~~In-built Sessions~~  
- [x] ~~In-built Global Variables~~  
- [x] ~~In-built Caching~~
- [x] ~~Response->sendFile method~~  
- [x] ~~Config Parser~~  
- [x] ~~Route Wildcards~~ 
- [x] ~~Route RegEx~~ 
- [x] ~~Support .env files~~
- [x] ~~Add support for DELETE, UPDATE, PUT, ... methods~~
- [x] ~~Response->toString() method~~  
- [x] ~~Create composer package~~
- [x] ~~Simple Middleware~~
- [x] ~~In-built CSRF Tokens~~  
- [x] ~~In-built Access Logging~~  
- [x] ~~In-built Error Handling~~  

## Requirements
QuickyPHP requires PHP 7.4 or higher and a webserver that supports Rewrite Rules.  
Note: Composer 2+ is required to find the package.

## Installation
### Composer:

Install the project via command-line:
```bash
composer create-project david-prv/quickyphp
```
  
### Manual

Create a project folder:
```bash
mkdir myProject && cd myProject
```
Download git repository:
```bash
git clone https://github.com/david-prv/QuickyPHP.git
```
If you want to use composer autoloader or unit-tests:
```bash
composer install
```

## Get Started

### First application

First things first. Let's define a basic application:
```php
require __DIR__ . "/quicky/autoload.php";
// require __DIR__ . "/vendor/autoload.php";

// The basic application
$app = Quicky::create();

// Run it
$app->run();
```
By now, no route will be accepted. Let's start with the index page.  
I will use the in-built autoloader in the following snippets.
```php
require __DIR__ . "/quicky/autoload.php";

$app = Quicky::create();

// Define the basic route "/" which will be displayed by default
App::get("/", function(Request $request, Response $response) {
    $response->send("Welcome to this page!");
});

$app->run();
```
You can access the main components alternatively directly:
```php
require __DIR__ . "/quicky/autoload.php";

$app = Quicky::create();
$router = Quicky::router();

$router->get("/", function(Request $request, Response $response) {
    $response->send("Welcome to this page!");
});

$app->run();
```

### More on components

Here are all (currently) directly accessible components: 
```php
Quicky::view(); // Returns the view engine
Quicky::config(); // Returns the config controller
Quicky::router(); // Returns the routing engine
Quicky::session(); // Returns the session manager
Quicky::cookies(); // Returns the cookie manager
```

### Render a view

To make the response of the server more beautiful, you could use html pages.  
Add a html file to `/quicky/views` and name it following the scheme `[VIEW_NAME].html`.  
In the html file, you can write usual html code. Quicky offers dynamic placeholders, which are  
just variables that will be replaced by the callback function of a route. You denote a placeholder  
by surrounding the placeholder name with `%`-symbols, e.g. `%USER_NAME%`.
```php
require __DIR__ . "/quicky/autoload.php";

$app = Quicky::create();

// The following code will start a Quicky-SessionManager and
// set the variables "userName" and "userNick"
Quicky::session()->start();
Quicky::session()->setRange(array("userName" => "User123", "userNick" => "Unicorn"));

Quicky::get("/", function(Request $request, Response $response) {
    // The following code will render the view "name_of_your_view.html"
    // and replace "%USER_NAME%" by the session variable "userName"
    $response->render("name_of_your_view", array("USER_NAME", Quicky::session()->get("userName")));
});

$app->run();
```

### Simple middleware 

To add a valid middleware, navigate to `/quicky/middleware` and create a new class.  
We will create and implement the Middleware "GreetingMiddleware", which will just greet  
every visitor of our website, so we create the file `/quicky/middleware/GreetingMiddleware.php`.
```php
class GreetingMiddleware implements IMiddleware
{
    /**
     * Run middleware
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response|null
     */
    public function run(Request $request, Response $response, callable $next): ?Response
    {
        $response->send("<h3>Greetings, stranger!</h3>");
        return $next($request, $response);
    }
}
```
Pay attention, that your class implements the `IMiddleware` interface, which is important for it  
to work. Now, let's use the middleware in a route:
```php
Quicky::get("/greetings/{name}", function(Request $request, Response $response) {
    // Here, we send a default formatted text as response, which will be displayed
    // after the greetings sent by our middleware. The middleware is always be executed first!
    
    $response->send("Oh, now I know who you are... You are %s!", $request->getArg("name"));
},
new GreetingMiddleware(), // will be executed first
new GreetingMiddleware()); // this will be executed after that
```
As you can see, you can add as many middlewares to the route as you want. Now, the headline "Greetings, stranger!"  
will be displayed twice, before the default route answer is rendered.  
Now what? Let's greet everyone, everywhere... ok?
```php
require __DIR__ . "/quicky/autoload.php";

$app = Quicky::create();

Quicky::useMiddleware(new GreetingMiddleware(), new GreetingMiddleware());

Quicky::get("/", function(Request $request, Response $response) {
    $response->send("You were greeted... TWICE!");
});

$app->run();
```

## Contributing
TBD

## Support
TBD

## License
Quicky is released under the [MIT](https://en.m.wikipedia.org/wiki/MIT_License) license.
