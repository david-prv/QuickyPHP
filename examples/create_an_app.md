# Create an application
```php
require __DIR__ . "/../../vendor/autoload.php";

$app = Quicky::create();

/* A basic app example */

Quicky::route("GET", "/profile/{id}", function(Request $request, Response $response) {
    $userData = UsersController::findUserById($request->getArg("id"));
    $response->render('profile', array(
        "USER_NAME" => $userData["userName"],
        "USER_NICK" => $userData["userNick"],
        "USER_ID" => $userData["userID"],
        "USER_PROFILE_PIC" => $userData["userProfilePic"]
    ));
});

$app->run();
```