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
use Quicky\Http\Request;
use Quicky\Interfaces\DispatchingInterface;
use Quicky\Utils\Exceptions\CoreException;
use Quicky\Utils\Exceptions\ViewNotFoundException;

/**
 * Class View
 */
class View implements DispatchingInterface
{
    /**
     * Dispatching methods
     *
     * @var array
     */
    private array $dispatching;

    /**
     * Globally assigned placeholders
     *
     * @var array
     */
    private array $globalPlaceholders;

    /**
     * View constructor.
     */
    public function __construct()
    {
        $this->dispatching = array("view", "render");
        $this->globalPlaceholders = array();
    }

    /**
     * Sets new globally applied placeholders
     *
     * @param array $placeholders
     */
    public function usePlaceholders(array $placeholders): void
    {
        $this->globalPlaceholders = $placeholders;
    }

    /**
     * Returns the currently saved globally
     * applied placeholders
     *
     * @return array
     */
    public function getGlobalPlaceholders(): array
    {
        return $this->globalPlaceholders;
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
        $view = DynamicLoader::getLoader()->getInstance(View::class);

        $workingDir = $override ?? getcwd() . $config->getViewsPath();
        $viewFile = "$workingDir/$viewName.html";
        $variables = ($variables !== null) ? array_merge($variables, $view->getGlobalPlaceholders())
            : $view->getGlobalPlaceholders();

        if (!is_dir($workingDir) || !is_file($viewFile)) {
            throw new ViewNotFoundException($viewName);
        }

        $html = file_get_contents($viewFile);

        if (!is_null($variables)) {
            foreach ($variables as $key => $value) {
                $html = str_replace("{{" . $key . "}}", $value, $html);
            }
        }

        print $html;
    }

    /**
     * Render a basic error message.
     * The default QuickyPHP handler passes a bunch of
     * useful information to the view:
     * - Error title
     * - Error message
     * - The logging ref-ID
     * - Error banner visibility flag
     * - Error banner priority level
     *
     * @param string $errorLevel
     * @param string $errorMessage
     * @param string $errorFile
     * @param string $errorLine
     * @param Request $request
     */
    public static function error(
        string $errorLevel,
        string $errorMessage,
        string $errorFile,
        string $errorLine,
        Request $request
    ): void {
        $isNotice = (int)$errorLevel === E_USER_NOTICE;
        try {
            View::render("index", array(
                "ERROR_TITLE" => ($isNotice) ? "Notice!" : "Error!",
                "ERROR_MESSAGE" => "$errorMessage ($errorLevel) in $errorFile (line $errorLine)",
                "REQ_REF_ID" => $request->getID(),
                "APP_VERSION" => App::config()->getProject("version"),
                "SHOW_BANNER" => "show",
                "BANNER_PRIORITY" => ($isNotice) ? "warning" : "danger"
            ));
        } catch (ViewNotFoundException $e) {
        }
        die();
    }

    /**
     * Render a basic exception message.
     * The default QuickyPHP handler passes a bunch of
     * useful information to the view:
     * - Exception title
     * - Exception message
     * - The logging ref-ID
     * - Error banner visibility flag
     * - Error banner priority level
     *
     * @param string $message
     * @param Request $request
     */
    public static function except(string $message, Request $request): void
    {
        try {
            View::render("index", array(
                "ERROR_TITLE" => "Error!",
                "ERROR_MESSAGE" => $message,
                "REQ_REF_ID" => $request->getID(),
                "APP_VERSION" => App::config()->getProject("version"),
                "SHOW_BANNER" => "show",
                "BANNER_PRIORITY" => "danger"
            ));
        } catch (ViewNotFoundException $e) {
        }
        die();
    }

    /**
     * Return view instance
     *
     * @return View
     * @throws CoreException
     */
    public static function view(): View
    {
        $instance = DynamicLoader::getLoader()->getInstance(View::class);

        if ($instance instanceof View) {
            return $instance;
        } else {
            throw new CoreException();
        }
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
