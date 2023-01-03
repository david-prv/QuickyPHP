<?php

Quicky::route("GET", "/", function (Request $request, Response $response) {
    $delay = round(microtime(true) - Quicky::session()->getCreatedAt(), 5);
    $response->render("index", array("P_MS" => $delay));

    return $response;
});