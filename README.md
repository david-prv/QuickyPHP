# PHP Micro-Framework
A project name needs to be determined soon!

## Motivation
I started this project because I wanted to procrastinate important work for university. No joke. But it turned into a slight obsession that has been with me for a few days now. I found developing my own PHP micro-framework so interesting that I kept reading up on documentation and articles and watching tutorial after tutorial.

The framework has the sense to be structured as simple as possible, to be easily customizable by anyone to their needs. I also experimented with technologies that I had never used before but found in other projects or got to know at university (e.g. method dispatcher or reflection classes). Also, the project was partly done in collaboration with ChatGPT (OpenAI), which was also a memorable experiment.

## Sneak Peak
A simple web application powered by this framework:
```php
require __DIR__ . "/app/autoload.php";

$app = App::getInstance();

App::get("/", function(Request $request, Response $response) {
    $response->send("Hello World");
});

$app->run();
```

## TODO
[ ] Find a project name  
[ ] Create a logo  
[ ] Simple Middleware  
[ ] In-built Sessions  
[ ] In-built CSRF Tokens  
[ ] In-built Global Variables  
[ ] In-built Error Handling  
[ ] In-built Event Logging  
[ ] Response->sendFile method  
[ ] Config Parser  
[ ] Route Wildcards  

## Installation
TBD

## Get Started
TBD
