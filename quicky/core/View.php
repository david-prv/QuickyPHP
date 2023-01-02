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
 * Class View
 */
class View implements IDispatching
{
    /**
     * Dispatching methods
     *
     * @var array
     */
    private array $dispatching;

    /**
     * View constructor.
     */
    public function __construct()
    {
        $this->dispatching = array("view", "render");
    }

    /**
     * Displays a view file
     *
     * @param string $viewName
     * @param array|null $variables
     * @param string|null $override
     * @throws ViewNotFoundException
     * @throws ReflectionException
     */
    public static function render(string $viewName, ?array $variables = null, ?string $override = null)
    {
        $config = DynamicLoader::getLoader()->getInstance(Config::class);
        $workingDir = $override ?? getcwd() . $config->getViewsPath();
        $viewFile = "$workingDir/$viewName.html";

        if (!is_dir($workingDir) || !is_file($viewFile)) throw new ViewNotFoundException($viewName);

        $html = file_get_contents($viewFile);

        if (!is_null($variables)) {
            foreach ($variables as $key => $value) {
                $html = str_replace("%$key%", $value, $html);
            }
        }

        print $html;
    }

    /**
     * Render a basic error message.
     *
     * @param $errNo
     * @param $errStr
     */
    public static function error(string $errNo, string $errStr): void
    {
        echo "<strong>Error</strong>: $errNo - $errStr" . PHP_EOL;
    }

    /**
     * Render a basic exception message
     *
     * @param string $message
     */
    public static function except(string $message): void
    {
        echo "<strong>Exception:</strong> $message" . PHP_EOL;
    }

    /**
     * Return view instance
     *
     * @return object|View|null
     * @throws CoreException
     */
    public function view()
    {
        $instance = DynamicLoader::getLoader()->getInstance(View::class);

        if ($instance instanceof View) return $instance;
        else throw new CoreException();
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