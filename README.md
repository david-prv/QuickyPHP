# QuickyPHP
A php micro-framework for simple and quick web-applications

## Work in progress...
This project is still in *heavy* development. Methods will change or be removed entirely. Please do not implement any production systems with QuickyPHP yet! Wait until the first alpha releases are published and stable enough for production software. In any other case, feel free to play around with QuickyPHP!

## Motivation
I started this project because I wanted to procrastinate important work for university. No joke. But it turned into a slight obsession that has been with me for a few days now. I found developing my own PHP micro-framework so interesting that I kept reading up on documentation and articles and watching tutorial after tutorial.

The framework has the sense to be structured as simple as possible, to be easily customizable by anyone to their needs. I also experimented with technologies that I had never used before but found in other projects or got to know at university (e.g. method dispatcher or reflection classes). Also, the project was partly done in collaboration with ChatGPT (OpenAI), which was also a memorable experiment.

I got the idea of how a simple PHP framework works from other open source projects. Here is a selection (if you read it carefully, you will quickly see parallels to my framework and its structure):
- [FlightPHP](https://flightphp.com/)
- [SlimFramework](https://www.slimframework.com/)
- [CakePHP](https://cakephp.org/)

## Sneak Peak
A simple web application powered by this framework:
```php
require __DIR__ . "/../vendor/autoload.php";

use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();

Quicky::route("GET", "/", function(Request $request, Response $response) {
    $response->send("Hello World");
});

$app->run();
```

## Requirements
QuickyPHP requires PHP 7.4 or higher and a webserver that supports Rewrite Rules.  
Note: Composer 2+ is required to find the package.

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

### Code Examples:
I have written some basic examples [here](https://gist.github.com/david-prv/9e322ccaa67eb1698ed35551233aee47).

## Contributing
Please feel free to contribute to this project. I am always happy to see new and fresh ideas.  
If you contribute to QuickyPHP, make sure, that you follow the rules as stated in our [code of conduct](https://github.com/david-prv/QuickyPHP/blob/main/CODE_OF_CONDUCT.md).

## Support
If you like what I do, feel free to buy me a coffee for my work.  
Programming early in the morning is hard without a good cup of this magical liquid.
  
Click here to support me:

<a href="https://www.buymeacoffee.com/david.dewes">
    <img src="https://media3.giphy.com/media/TDQOtnWgsBx99cNoyH/giphy.gif" height="80" alt="buy me a coffee!"/>
</a>

## License
Quicky is released under the [MIT](https://en.m.wikipedia.org/wiki/MIT_License) license.
