<?php

/*
 * Demonstration on how the QuickyPHP
 * storage system works. Put some files into /app/storage.
 * They won't be accessible from outside by default.
 * Only QuickyPHP is able to deliver these files via the sendFile
 * method.
 *
 * To try it out, open:
 *      http://your-domain.tld/static/{filename}/
 *      where "filename" could be any file, you've put into the storage folder.
 *
 * QuickyPHP will then instantly deliver it with the correct content-type.
 */

Quicky::route("GET", "/", function(Request $request, Response $response) {
    $response->send("Usage: /static/FILENAME");
});

Quicky::route("GET", "/static/{name}", function(Request $request, Response $response) {
    $response->sendFile($request->getArg("name"));
});