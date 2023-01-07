<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace App\Core;

use App\Utils\MethodSearchTree;
use ArgumentCountError;
use DirectoryIterator;
use ReflectionClass;
use ReflectionException;

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
     * @var false|string
     */
    private string $workingDir;

    /**
     * The list of instances
     *
     * @var array
     */
    private array $instances;

    /**
     * All directories in the project folder
     *
     * @var array
     */
    private array $locations;

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
     * All already included classes
     *
     * @var array
     */
    private array $loaded;

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
        $this->locations = array();
        $this->classes = array();
        $this->loaded = array(self::class);
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
     * Creates or returns the loader
     *
     * @param string|null $override
     * @return static
     */
    public static function getLoader(?string $override = null): self
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
        if (!isset($this->instances[$className])) $this->instances[$className] = $instance;
    }

    /**
     * Creates or returns an instance
     * of a certain class by name
     *
     * @param string $className
     * @param array|null $params
     * @return object|null
     */
    public function getInstance(string $className, ?array $params = null): ?object
    {
        // if it is not an existing class...
        if (!in_array($className, $this->classes)) return null;

        // if the instance is already instantiated...
        if (isset($this->instances[$className])) return $this->instances[$className];
        else {
            try {
                // instantiate the class either with instance args
                // or without any arguments
                $instance = (is_null($params))
                    ? new $className()
                    : (new ReflectionClass($className))->newInstanceArgs($params);

                // add it to the local list
                $this->instances[$className] = $instance;
                return $this->instances[$className];
            } catch (ArgumentCountError $e) {
                echo $e->getMessage();
            } catch (ReflectionException $e) {
                echo $e->getMessage();
            }
            return null;
        }
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
        // add the current location to the locations list
        array_push($this->locations, $current);

        // we use directory iterators to do the job
        $iterator = new DirectoryIterator($this->workingDir . $current);

        // for all file-infos
        foreach ($iterator as $info) {

            // it is is a file
            if ($info->isFile()) {
                $file = $info->getFilename();
                $temp = explode(".", $file);
                $ext = $temp[count($temp) - 1];
                $namespace = str_replace('/', "\\",
                    substr(str_replace("app", "App", $current), 1));
                $name = $namespace . "\\" . $temp[0];
                if ($ext === "php" && $name !== "autoload" && $name !== "index") array_push($this->classes, $name);
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
            if (!Dispatcher::canDispatch($class)) continue;

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
