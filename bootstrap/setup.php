<?php

/**
 * Events
 */
const E_ERRORS = "error";
const E_EXCEPTIONS = "exception";

/**
 * States
 */
const S_PRODUCTION = "production";
const S_DEVELOPMENT = "development";

/**
 * Modes
 */
const M_ENV = "env";
const M_JSON = "json";
const M_DEFAULT = "default";

/**
 * Verifies all necessary pre-conditions
 * before the application can run flawlessly
 *
 * @param $app
 * @return bool
 */
function verify_pre_condition($app): bool
{
    return !is_null($app) && version_compare(phpversion(), "7.4.0", "ge");
}

/**
 * Performs a hard abortion
 * of the application during boot-up
 * sequence
 *
 * @return void
 */
function perform_boot_abort(): void
{
    die("The current running php version is not supported. Aborted!");
}