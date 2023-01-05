<?php

/*
 * Default page of QuickyPHP,
 * which measures the loading time.
 */

Quicky::route("GET", "/", function (Request $request, Response $response) {
    $delay = number_format(microtime(true) - Quicky::session()->getCreatedAt(), 4);
    $response->render("index", array("P_TIME_S" => $delay));

    return $response;
});