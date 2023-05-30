<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky\Core;

use Quicky\App;
use Quicky\Utils\MethodSearchTree;
use ArgumentCountError;
use DirectoryIterator;
use ReflectionClass;
use ReflectionException;
use stdClass;

/**
 * Class DynamicLoader
 */
class DynamicLoader
{
    /**
     * Singleton instance
     *
     * @var DynamicLoader|null
     */
    private static ?DynamicLoader $instance = null;

    /**
     * The current working directory
     *
     * @var string
     */
    private string $workingDir;

    /**
     * The list of instances
     *
     * @var array
     */
    private array $instances;

    /**
     * All available classes
     * Read more about the required naming
     * standards:
     *
     * @link https://pear.php.net/manual/en/standards.naming.php
     *
     * @var array
     */
    private array $classes;

    /**
     * Methods represented as BST
     *
     * @var MethodSearchTree
     */
    private MethodSearchTree $methods;

    /**
     * DynamicLoader constructor.
     *
     * @param string|null $override
     */
    private function __construct(?string $override = null)
    {
        // current working directory should be
        // the project's ROOT folder, not the HTTP docs folder
        chdir(getcwd() . "/../");

        // load internal vars
        $this->workingDir = $override ?? getcwd();
        $this->instances = array();
        $this->classes = array();
        $this->methods = new MethodSearchTree();

        // since this class has already an instance
        $this->registerInstance(DynamicLoader::class, $this);

        // scan the project structure + classes
        $this->scan();

        try {
            // build the MST (binary search tree with methods)
            $this->buildMST();
        } catch (ReflectionException $e) {
        }
    }

    /**
     * Fails if application was not initialized
     * correctly or if boot-up failed bitterly
     */
    public function failIfAppIsUnavailable(): void
    {
        if (!$this->isInstantiated(App::class)) {
            die("You can not interact with an uninstantiated application. Aborted!");
        }
    }

    /**
     * Creates or returns the loader
     *
     * @param string|null $override
     * @return DynamicLoader
     */
    public static function getLoader(?string $override = null): DynamicLoader
    {
        if (self::$instance === null) {
            self::$instance = new DynamicLoader($override ?? null);
        }
        return self::$instance;
    }

    /**
     * Registers an existing instance
     *
     * @param string $className
     * @param object $instance
     */
    public function registerInstance(string $className, object $instance): void
    {
        if (!$this->isInstantiated($className)) {
            $this->instances[$className] = $instance;
        }
    }

    /**
     * Creates or returns an instance
     * of a certain class by name
     *
     * @param string $className
     * @param array|null $params
     * @return mixed
     */
    public function getInstance(string $className, ?array $params = null)
    {
        // since the dynamic loader is responsible
        // for Quicky to manage all available instances and routes all interactions,
        // this is a good place to again enforce our precondition
        $this->failIfAppIsUnavailable();

        // if it is not an existing class...
        if (!in_array($className, $this->classes)) {
            // return standard object
            return new stdClass();
        }

        // if the instance is already instantiated...
        if (!$this->isInstantiated($className) || !is_a($this->instances[$className], $className)) {
            try {
                // instantiate the class either with instance args
                // or without any arguments
                $instance = (is_null($params))
                    ? new $className()
                    : (new ReflectionClass($className))->newInstanceArgs($params);

                // add it to the local list
                $this->instances[$className] = $instance;
            } catch (ArgumentCountError|ReflectionException $e) {
            }
        }
        return $this->instances[$className];
    }

    /**
     * Checks whether a certain class was
     * instantiated once before
     *
     * @param string $className
     * @return bool
     */
    public function isInstantiated(string $className): bool
    {
        return isset($this->instances[$className]);
    }

    /**
     * Get all (dispatch-able) methods
     * as MST object
     *
     * @return MethodSearchTree
     */
    public function getMethods(): MethodSearchTree
    {
        return $this->methods ?? new MethodSearchTree();
    }

    /**
     * Scans the framework working directory
     * to get an overview over all existing files
     * an classes.
     *
     * NOTE:    It is necessary that all files are named by
     *          their class names.
     *
     * @param string $current
     */
    private function scan(string $current = "/app"): void
    {
        // we use directory iterators to do the job
        $iterator = new DirectoryIterator($this->workingDir . $current);

        // for all file-infos
        foreach ($iterator as $info) {
            // it is a file
            if ($info->isFile()) {
                $file = $info->getFilename();
                $temp = explode(".", $file);
                $ext = $temp[count($temp) - 1];
                $namespace = str_replace(
                    '/',
                    "\\",
                    substr(str_replace("app", "Quicky", $current), 1)
                );
                $name = $namespace . "\\" . $temp[0];
                if ($ext === "php" && $name !== "autoload" && $name !== "index") {
                    $this->classes[] = $name;
                }
            }

            // if it is a (visible) directory
            if ($info->isDir() && !$info->isDot()) {
                $this->scan($current . "/" . $info->getFilename());
            }
        }
    }

    /**
     * Build method search tree
     *
     * @throws ReflectionException
     */
    private function buildMST(): void
    {
        $methodTree = $this->methods;
        foreach ($this->classes as $class) {
            // skip non-dispatching classes, interfaces
            if (!Dispatcher::canDispatch($class)) {
                continue;
            }

            // Use reflection to get a list of the class's methods
            $reflectionClass = new ReflectionClass($class);
            $methods = $reflectionClass->getMethods();

            foreach ($methods as $method) {
                // Insert a string "MethodName.OriginClass" into the method tree
                $methodTree->insert($method->getName() . '.' . $class);
            }
        }
    }

    /**
     * Searches a certain method.
     * The method has to be dispatch-able
     * and should be named uniquely.
     *
     * @param string $methodName
     * @return string|null
     */
    public function findMethod(string $methodName): ?string
    {
        // use MST to find the method very quickly
        return $this->methods->find($methodName);
    }
}
