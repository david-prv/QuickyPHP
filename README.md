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

## TODO
- [x] ~~Find a project name~~  
- [ ] Create a logo  
- [x] ~~Buy domain: quickyphp.de~~
- [ ] Support .env files
- [ ] Simple Middleware
- [ ] Response->toString() method  
- [ ] In-built Sessions  
- [ ] In-built CSRF Tokens  
- [ ] In-built Global Variables  
- [ ] In-built Error Handling  
- [ ] In-built Event Logging  
- [x] ~~In-built Caching~~
- [x] ~~Response->sendFile method~~  
- [ ] Config Parser  
- [x] ~~Route Wildcards~~ 
- [x] ~~Route RegEx~~ 
- [ ] Write Tests!
- [ ] Write Docs / simple webpage!
- [ ] Add support for DELETE, UPDATE, PUT methods

## Requirements
QuickyPHP requires PHP 7.4 or higher and a webserver that supports Rewrite Rules.

## Installation
TBD

## Get Started
TBD

## Contributing
TBD

## Support
TBD

## License
Flight is released under the [MIT](http://flightphp.com/license) license.
