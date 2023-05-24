<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

namespace Quicky\Core;

use Quicky\Interfaces\DispatchingInterface;
use Quicky\Utils\Exceptions\CoreException;

/**
 * Class EventHook
 */
class EventHook implements DispatchingInterface
{
    /**
     * Dispatching methods
     *
     * @var array
     */
    private array $dispatching;

    /**
     * Listening methods/classes
     *
     * @var array
     */
    private array $listeners;

    /**
     * Registered events
     *
     * @var array
     */
    private array $events;

    /**
     * EventHook constructor.
     */
    public function __construct()
    {
        $this->dispatching = array("hook", "listen");
        $this->listeners = array();
        $this->events = array();
    }

    /**
     * Return EventHook instance
     *
     * @return EventHook
     * @throws CoreException
     */
    public static function hook(): EventHook
    {
        $instance = DynamicLoader::getLoader()->getInstance(EventHook::class);

        if ($instance instanceof EventHook) {
            return $instance;
        } else {
            throw new CoreException();
        }
    }

    /**
     * Get an array of registered listeners
     *
     * @param string $eventName
     * @return array
     */
    private function getListenersOf(string $eventName): array
    {
        if (!in_array($eventName, $this->events) || count($this->listeners) === 0) {
            return [];
        }

        $listeners = [];
        foreach ($this->listeners as $listener) {
            if (strtoupper($listener[0]) === strtoupper($eventName)) {
                $listeners[] = $listener;
            }
        }

        return $listeners;
    }

    /**
     * Register a new event (or many)
     *
     * @param ...$eventNames
     * @return bool
     */
    public function register(...$eventNames): bool
    {
        foreach ($eventNames as $eventName) {
            if (in_array($eventName, $this->events)) {
                continue;
            }
            $this->events[] = $eventName;
        }
        return true;
    }

    /**
     * Unregister an event (or many)
     *
     * @param ...$eventNames
     * @return bool
     */
    public function unregister(...$eventNames): bool
    {
        foreach ($eventNames as $eventName) {
            if (!in_array($eventName, $this->events)) {
                continue;
            }
            unset($this->events[$eventName]);
        }
        return true;
    }

    /**
     * Fire an event with parameters
     *
     * @param string $eventName
     * @param ...$args
     * @return bool
     */
    public function fire(string $eventName, ...$args): bool
    {
        $listeners = $this->getListenersOf($eventName);

        if (count($listeners) === 0) {
            return false;
        }

        foreach ($listeners as $listener) {
            call_user_func($listener[1], ...$args);
        }
        return true;
    }

    /**
     * Adds a new listener for an event
     *
     * @param string $eventName
     * @param $callback
     * @return bool
     */
    public function listen(string $eventName, $callback): bool
    {
        $tuple = [$eventName, $callback];
        if (in_array($tuple, $this->listeners)) {
            return false;
        }
        $this->listeners[] = $tuple;
        return true;
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
}
