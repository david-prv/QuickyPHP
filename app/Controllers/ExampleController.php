<?php

namespace Quicky\Controllers;

use Quicky\App;

/**
 * Class ExampleController
 */
class ExampleController extends BaseController
{
    /**
     * Dummy Session for development environments
     *
     * @var string[]
     */
    private array $dummySession;

    /**
     * ExampleController constructor.
     */
    public function __construct()
    {
        $this->dummySession = [
            "user.loggedIn" => "yes",
            "user.perm.change_password" => "granted",
            "user.perm.change_username" => "granted",
            "user.perm.change_email" => "denied"
        ];

        parent::__construct();
    }

    /**
     * Setup routine, setup information can be
     * passed directly from application before
     * main core ignition
     *
     * @param mixed ...$params
     * @return void
     */
    public function setup(...$params): void
    {
        if ($this->config->isDev()) {
            App::session()->setRange($this->dummySession);
        }
    }

    /**
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        return App::session()->get("user.loggedIn") === "yes";
    }

    /**
     * @param string $permissionId
     * @return bool
     */
    public static function hasPermission(string $permissionId): bool
    {
        return App::session()->get("user.perm.$permissionId") === "granted";
    }

    /**
     * @return void
     */
    public static function invalidateSession(): void
    {
        App::session()->destroy();
    }
}
