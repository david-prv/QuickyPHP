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
use Quicky\Utils\Exceptions\CoreException;

/**
 * Class CookieManager
 */
class CookieManager implements DispatchingInterface, ManagerInterface
{
    /**
     * Dispatching methods
     *
     * @var array
     */
    private array $dispatching;

    /**
     * CookieManager constructor.
     */
    public function __construct()
    {
        $this->dispatching = array("cookies");
    }

    /**
     * Return session instance
     *
     * @return CookieManager
     * @throws CoreException
     */
    public static function cookies(): CookieManager
    {
        $instance = DynamicLoader::getLoader()->getInstance(CookieManager::class);

        if ($instance instanceof CookieManager) {
            return $instance;
        } else {
            throw new CoreException();
        }
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
     * Get a cookie by name
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @param string $name
     * @return string|null
     */
    public function get(string $name): ?string
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        return null;
    }

    /**
     * Set a cookie
     *
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     */
    public function set($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false): void
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * Set a range of key => value
     * paired cookies
     *
     * @param array $assoc
     */
    public function setRange(array $assoc): void
    {
        foreach ($assoc as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Unset a cookie
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     */
    public function unset(string $name, $path = '/', $domain = ''): void
    {
        setcookie($name, '', time() - 3600, $path, $domain);
    }
}
