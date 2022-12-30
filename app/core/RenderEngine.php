<?php
/**
 * A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

/**
 * Class RenderEngine
 */
class RenderEngine
{
    /**
     * Displays a view file
     *
     * @param string $viewName
     * @param array|null $variables
     * @param string|null $override
     * @throws ViewNotFoundException
     */
    public static function display(string $viewName, ?array $variables = null, ?string $override = null)
    {
        $workingDir = $override ?? getcwd() . "/app/views";
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
}