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
class View
{
    /**
     * View constructor.
     */
    public function __construct()
    {
    }

    /**
     * Displays a view file
     *
     * @param string $viewName
     * @param array|null $variables
     * @param string|null $override
     * @throws ViewNotFoundException
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

    /** @dispatch */
    public function view()
    {
        $instance = DynamicLoader::getLoader()->getInstance(View::class);

        if ($instance instanceof View) return $instance;
        else throw new CoreException();
    }
}