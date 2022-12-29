<?php

declare(strict_types=1);

class DynamicLoader
{
    private static ?DynamicLoader $instance = null;
    private string $workingDir;
    private array $instances = array();
    private array $locations;
    private array $classes;
    private array $loaded;

    /**
     * DynamicLoader constructor.
     * @param string|null $override
     */
    private function __construct(?string $override = null)
    {
        $this->workingDir = $override ?? getcwd();
        $this->locations = [];
        $this->classes = [];
        $this->loaded = [];
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
     * @throws InvalidClassException
     */
    public function load(string $className): void
    {
        $temp = explode('\\', $className);
        $className = end($temp);

        if (!in_array($className, $this->classes)) throw new InvalidClassException("$className is not a class");
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
    public function scan(string $current = "/app"): void
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
     * @param string $name
     * @return string|null
     */
    public function findMethod(string $name): ?string
    {
        foreach ($this->classes as $class) {
            if (method_exists($class, $name)) return $class;
        }
        return null;
    }
}