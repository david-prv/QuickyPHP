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
     * @var BinarySearchTree
     */
    private BinarySearchTree $methods;

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
     * @throws ReflectionException
     */
    private function __construct(?string $override = null)
    {
        chdir(getcwd() . "/../../");

        $this->workingDir = $override ?? getcwd();
        $this->instances = array();
        $this->locations = array();
        $this->classes = array();
        $this->loaded = array(self::class);
        $this->methods = new BinarySearchTree();
        $this->registerInstance(DynamicLoader::class, $this);
        $this->scan();
        $this->buildBST();
    }

    /**
     * Creates or returns the loader
     *
     * @param string|null $override
     * @return static
     * @throws ReflectionException
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
        if (!in_array($className, $this->classes)) return null;
        if (isset($this->instances[$className])) return $this->instances[$className];
        else {
            try {
                $instance = (is_null($params))
                    ? new $className()
                    : (new ReflectionClass($className))->newInstanceArgs($params);

                $this->instances[$className] = $instance;
                return $this->instances[$className];
            } catch (ArgumentCountError $e) {
            } catch (ReflectionException $e) {
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
     * @throws ReflectionException
     */
    private function scan(string $current = "/quicky"): void
    {
        array_push($this->locations, $current);

        $dirs = new DirectoryIterator($this->workingDir . $current);
        foreach ($dirs as $dir) {
            if ($dir->isFile()) {
                $file = $dir->getFilename();
                $temp = explode(".", $file);
                $ext = $temp[count($temp) - 1];
                $name = $temp[0];
                if ($ext === "php" && $name !== "autoload" && $name !== "index") array_push($this->classes, $name);
            }

            if ($dir->isDir() && !$dir->isDot()) {
                $this->scan($current . "/" . $dir->getFilename());
            }
        }
    }

    /**
     * Build method BST
     *
     * @throws ReflectionException
     */
    private function buildBST(): void
    {
        // Initialize a new binary search tree to store the methods
        $methodTree = $this->methods;

        // For each class in $this->classes...
        foreach ($this->classes as $class) {
            if (!method_exists($class, "dispatches") || $class[0] === "I") continue;
            // Use reflection to get a list of the class's methods
            $reflectionClass = new ReflectionClass($class);
            $methods = $reflectionClass->getMethods();

            // For each method...
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
        // Search the method tree for a string matching the format "MethodName.OriginClass"
        $className = $this->methods->find($methodName);
        if (!is_null($className)) {
            // Extract the class name from the string and return it
            return $className;
        } else {
            // If no matching string is found, return null
            return null;
        }
    }
}
