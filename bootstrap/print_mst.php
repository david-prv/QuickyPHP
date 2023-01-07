<?php

use App\Http\Request;
use App\Http\Response;
use App\Quicky;

$app = Quicky::create();
Quicky::session()->start();

Quicky::route("GET", "/", function() {
    \App\Core\DynamicLoader::getLoader()->getMethods()->dump();
});