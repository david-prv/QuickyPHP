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
        $this->workingDir = $override ?? getcwd();
        $this->instances = array();
        $this->locations = array();
        $this->classes = array();
        $this->loaded = array(self::class);
        $this->registerInstance(DynamicLoader::class, $this);
        $this->scan();
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
     * Load a class by classname
     * using the classmap
     *
     * @param string $className
     * @throws InvalidClassException
     */
    public function load(string $className): void
    {
        if (!in_array($className, $this->classes)) throw new InvalidClassException($className);
        if (in_array($className, $this->loaded)) return;

        foreach ($this->locations as $loc) {
            $fileName = $this->workingDir . $loc . "/" . $className . ".php";
            if (is_file($fileName)) {
                array_push($this->loaded, $className);
                require $fileName . "";
            }
        }
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
        if (isset($this->instances[$className])) return $this->instances[$className];
        else {
            try {
                $instance = (is_null($params))
                    ? new $className()
                    : (new ReflectionClass($className))->newInstanceArgs($params);

                $this->instances[$className] = $instance;
                return $this->instances[$className];
            } catch (ArgumentCountError $e) {
            } catch (ReflectionException $e) {}
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
                if ($ext === "php" && $name !== "autoload") array_push($this->classes, $name);
            }

            if ($dir->isDir() && !$dir->isDot()) {
                $this->scan($current . "/" . $dir->getFilename());
            }

        }
    }

    /**
     * Searches a certain method.
     * The method has to be dispatch-able
     * and should be named uniquely.
     *
     * @param string $name
     * @return string|null
     */
    public function findMethod(string $name): ?string
    {
        foreach ($this->classes as $class) {
            if (method_exists($class, $name)
                && Dispatcher::canDispatchMethod($class, $name)) return $class;
        }
        return null;
    }
}
