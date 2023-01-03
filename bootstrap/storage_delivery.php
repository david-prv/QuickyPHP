<?php

Quicky::route("GET", "/", function(Request $request, Response $response) {
    $response->send("Usage: /static/FILENAME");
});

Quicky::route("GET", "/static/{name}", function(Request $request, Response $response) {
    $response->sendFile($request->getArg("name"));
});