<?php

declare(strict_types=1);

class DynamicLoader
{
    private static ?DynamicLoader $instance = null;
    private string $workingDir;
    private array $instances = array();
    private array $locations;
    private array $classes;

    /**
     * DynamicLoader constructor.
     * @param string|null $override
     */
    private function __construct(?string $override = null)
    {
        $this->workingDir = $override ?? getcwd();
        $this->locations = [];
        $this->classes = [];
        $this->scan();
    }

    /**
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
     * @param string $className
     */
    public function load(string $className): void
    {
        array_push($this->classes, $className);
        foreach ($this->locations as $loc) {
            $fileName = $this->workingDir . $loc . "/" . $className . ".php";
            if (is_file($fileName)) {
                require $fileName . "";
            }
        }
    }

    /**
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
            } catch (Exception $e) {
                return null;
            }
        }
    }

    /**
     * @param string $current
     */
    public function scan(string $current = ""): void
    {
        array_push($this->locations, $current);
        $dir = new DirectoryIterator($this->workingDir . $current);
        foreach ($dir as $info) {
            if ($info->isDir() && !$info->isDot()) {
                $this->scan($current . "/" . $info->getFilename());
            }
        }
    }

    /**
     * @param string $className
     * @return bool
     */
    public function hasClass(string $className): bool
    {
        return isset($this->classes[$className]);
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function findMethod(string $name): ?string
    {
        foreach ($this->classes as $class) {
            echo "searching in $class <br>";
            if (method_exists($class, $name)) return $class;
        }
        return null;
    }
}