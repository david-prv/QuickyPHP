<?php

/*
 * Allows common folders.
 *
 * To use them create:
 *      /public/css
 *      /public/js
 *      /public/assets
 *      /public/includes
 *
 * Now, you can put your files in there and
 * reference them in your project as
 *      http://your-domain.tld/css/filename.css
 *      http://your-domain.tld/js/filename.js
 *      ...
 */

Quicky::pass("/css/*");
Quicky::pass("/js/*");
Quicky::pass("/assets/*");
Quicky::pass("/includes/*");