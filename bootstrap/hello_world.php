<?php

Quicky::route("GET", "/", function(Request $request, Response $response) {
    $response->send("<h3>Hello World</h3>");
});

Quicky::route("GET", "/{name}", function(Request $request, Response $response) {
   $response->send("<h3>Hello %s, you are my world!</h3>", $request->getArg("name"));
});