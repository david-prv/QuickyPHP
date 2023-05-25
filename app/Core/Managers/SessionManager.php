<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky\Core\Managers;

use Quicky\Core\DynamicLoader;
use Quicky\Interfaces\DispatchingInterface;
use Quicky\Interfaces\ManagerInterface;
use Quicky\App;
use Quicky\Utils\Exceptions\InvalidSessionException;
use Exception;

/**
 * Class SessionManager
 */
class SessionManager implements DispatchingInterface, ManagerInterface
{
    /**
     * In-built sessionId
     *
     * @var string|null
     */
    private ?string $id = null;

    /**
     * Creation timestamp
     *
     * @var float|null
     */
    private ?float $createdAt = null;

    /**
     * Secure flag
     *
     * @var bool
     */
    private bool $secure;

    /**
     * SessionManager is active
     *
     * @var bool
     */
    private bool $active;

    /**
     * Dispatching methods
     *
     * @var array
     */
    private array $dispatching;

    /**
     * SessionManager constructor.
     */
    public function __construct()
    {
        $this->secure = true;
        $this->active = false;
        $this->dispatching = array("session");
    }

    /**
     * Return session instance
     *
     * @return SessionManager
     * @throws InvalidSessionException
     */
    public static function session(): SessionManager
    {
        $instance = DynamicLoader::getLoader()->getInstance(SessionManager::class);

        if ($instance instanceof SessionManager) {
            return $instance;
        } else {
            throw new InvalidSessionException();
        }
    }

    /**
     * Start a session
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @param bool $secure
     */
    public function start(bool $secure = true): void
    {
        if ($this->active) {
            return;
        }

        session_start();

        $this->id = uniqid();
        $this->active = true;
        $this->secure = $secure;
        $this->createdAt = microtime(true);

        $_SESSION[App::__SESSION_ID] = $this->id;
        $_SESSION[App::__SESSION_CREATED_AT] = $this->createdAt;
    }

    /**
     * Is this session secure against
     * SessionManager Hijacking?
     *
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * Destroys the session
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function destroy(): void
    {
        if (!$this->active) {
            return;
        }

        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }

        $this->active = false;
        $this->id = null;
        $this->createdAt = null;
        session_destroy();
    }

    /**
     * Regenerate session id
     */
    public function regenerateId(): void
    {
        if (!$this->active) {
            return;
        }

        session_regenerate_id();
    }

    /**
     * Set a session variable
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @param string $name
     * @param string $value
     */
    public function set(string $name, string $value): void
    {
        if (!$this->active) {
            return;
        }
        if ($this->secure) {
            $this->regenerateId();
        }

        if (strtolower($name) !== App::__SESSION_ID
            && strtolower($name) !== App::__SESSION_CREATED_AT
            && strtolower($name) !== App::__SESSION_CSRF) {
            $_SESSION[$name] = $value;
        }
    }

    /**
     * Set multiple session variables
     *
     * @param array $assoc
     */
    public function setRange(array $assoc): void
    {
        if (!$this->active) {
            return;
        }

        foreach ($assoc as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Get a session variable
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @param string $name
     * @return string
     */
    public function get(string $name): string
    {
        if (!$this->active) {
            return "";
        }
        if ($this->secure) {
            $this->regenerateId();
        }

        return (isset($_SESSION[$name])) ? $_SESSION[$name] : "";
    }

    /**
     * Unset a variable
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @param string $name
     */
    public function unset(string $name): void
    {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
    }

    /**
     * Returns the activation state
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Returns app-in-built sessionId
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Returns app-in-built creation time
     *
     * @return float|null
     */
    public function getCreatedAt(): ?float
    {
        return $this->createdAt;
    }

    /**
     * Checks if class is dispatching
     *
     * @param string $method
     * @return bool
     */
    public function dispatches(string $method): bool
    {
        return in_array($method, $this->dispatching);
    }

    /**
     * Generates a Cross-Site-Request-Forgery (CSRF)
     * Token for current session
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return string
     * @throws Exception
     */
    public function generateCSRFToken(): string
    {
        if (!$this->isActive()) {
            return "";
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION[App::__SESSION_CSRF] = $token;
        return $token;
    }

    /**
     * Verification of a CSRF token
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @param string $token
     * @return bool
     */
    public function verifyCSRF(string $token): bool
    {
        if (!$this->isActive()) {
            return false;
        }
        return $_SESSION[App::__SESSION_CSRF] === $token;
    }
}
