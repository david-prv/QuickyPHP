<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

/**
 * Class Sessions
 *
 * @dispatch session
 */
class Sessions
{
    /**
     * In-built sessionId
     *
     * @var string
     */
    private string $id;

    /**
     * Secure flag
     *
     * @var bool
     */
    private bool $secure;

    /**
     * Session is active
     *
     * @var bool
     */
    private bool $active;

    /**
     * Field names for in-built session fields
     */
    const QUICKY_SESSION_ID = "quicky_session_id";
    const QUICKY_SESSION_CREATED_AT = "quicky_created_at";

    /**
     * Sessions constructor.
     */
    public function __construct()
    {
        $this->id = uniqid();
        $this->secure = true;
        $this->active = false;
    }

    /**
     * Session destructor.
     */
    public function __destruct()
    {
        if ($this->active) $this->destroy();
    }

    /**
     * Getter for session instance
     *
     * @throws InvalidSessionException
     */
    public static function session()
    {
        $instance = DynamicLoader::getLoader()->getInstance(Sessions::class);

        if ($instance instanceof Sessions) return $instance;
        else throw new InvalidSessionException();
    }

    /**
     * Start a session
     *
     * @param bool $secure
     */
    public function start(bool $secure = true): void
    {
        if ($this->active) return;

        session_start();

        $this->active = true;
        $this->secure = $secure;

        $_SESSION[$this::QUICKY_SESSION_ID] = $this->id;
        $_SESSION[$this::QUICKY_SESSION_CREATED_AT] = time();
    }

    /**
     * Is this session secure against
     * Session Hijacking?
     *
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * Destroys the session
     */
    public function destroy(): void
    {
        if (!$this->active) return;

        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }

        $this->active = false;
        session_destroy();
    }

    /**
     * Regenerate session id
     */
    public function regenerateId(): void
    {
        if (!$this->active) return;

        session_regenerate_id();
    }

    /**
     * Set a session variable
     *
     * @param string $name
     * @param string $value
     */
    public function set(string $name, string $value): void
    {
        if (!$this->active) return;
        if ($this->secure) $this->regenerateId();

        if (strtolower($name) !== $this::QUICKY_SESSION_ID
                && strtolower($name) !== $this::QUICKY_SESSION_CREATED_AT) {
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
        if (!$this->active) return;

        foreach ($assoc as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Get a session variable
     *
     * @param string $name
     * @return string
     */
    public function get(string $name): string
    {
        if (!$this->active) return "";
        if ($this->secure) $this->regenerateId();

        return (isset($_SESSION[$name])) ? $_SESSION[$name] : "";
    }

    /**
     * Returns quicky-in-built sessionId
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}