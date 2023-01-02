# Routing Types

```php
require __DIR__ . "/../../vendor/autoload.php";

$app = Quicky::create();

Quicky::route("YOUR_METHOD", "YOUR_PATTERN", CALLBACK, ...MIDDLEWARE);

/* Or use router directly instead of dispatching it: */

$router = Quicky::router();
$router->route("YOUR_METHOD", "YOUR_PATTERN", CALLBACK, ...MIDDLEWARE);

$app->run();
```

## Methods:
- GET
- POST
- PUT
- UPDATE
- DELETE
- PATCH

## Patterns

### Standard
``/your/path/to/something``  

**Allows**: Exactly the path "/your/path/to/something"

### RegEx
**NOTE**: Regex patterns have to be surrounded by parentheses: "`(`" and "`)`"

``/something/(\d+)/cool/``  

**Allows**: At least one to arbitrary many numbers between "/something/" and "/cool".
For instance: "/something/1337/cool/" or "/something/42/cool/"

### Variables
**NOTE**: Names variables have to be surrounded by brackets: "`{`" and "`}`"

``/api/v2/users/{userName}/game/{gameID}``    

**Allows**: Providing an username and a game-ID, which then can be accessed throught the Request object
in the callback method: ``$request->getArg("userName");``, ... . They have to be present, otherwise
the route will not match the requested url.

### Wildcards
``/question/*/report``

**Allows**: An universal report page for questions, by just appending "/report" to *any* question
url, like "/question/874729/report" or "/question/136725/report". Be aware, that with a wildcard
asterix, you are not able to get the passed question-ID here via the Request object.

