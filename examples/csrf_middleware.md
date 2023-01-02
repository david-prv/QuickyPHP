# Use CSRF middleware

```php
require __DIR__ . "/../../vendor/autoload.php";

$app = Quicky::create();
Quicky::session()->start();

/* A basic authentication */

Quicky::route("GET", "/login", function(Request $request, Response $response) {
    $response->render('login', array(
        "CSRF_TOKEN" => Quicky::session()->generateCSRFToken() 
    ));
});

Quicky::route("POST", "/login", function(Request $request, Response $response) {
    $loggedIn = AuthController::logIn($request->getData());
    if ($loggedIn) {
        $response->redirect('/your-account');
    } else {
        $response->forbidden("Wrong credentials");
    }
}, new CSRFMiddleware());

$app->run();
```