<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

namespace Quicky\Console;

/**
 * Class Console
 */
class Console
{
    private static ?Console $instance = null;

    private string $configDir;
    private string $logsDir;
    private string $cacheDir;
    private string $command;
    private string $subCommand;
    private int $argc;
    private array $argv;

    private function __construct(int $argc, array $argv)
    {
        if ($argc < 2) {
            $this->shutdown();
        }

        $this->configDir = "./resources/config";
        $this->logsDir = ".".$this->getPath("logs");
        $this->cacheDir = ".".$this->getPath("cache");
        $this->argc = $argc;
        $this->argv = $argv;

        $this->command = $this->argv[1];
        $this->subCommand = "";
    }

    public static function kernel(int $argc, array $argv): Console
    {
        if (self::$instance === null) {
            self::$instance = new Console($argc, $argv);
        }
        return self::$instance;
    }

    private function shutdown(): void
    {
        exit("Usage: quicky-cli <command> [<arg1> [<arg2> [ ... ]]]");
    }

    private function getPath($of): string
    {
        $configFile = $this->configDir . "/config.json";
        $configData = file_get_contents($configFile);
        $configArray = json_decode($configData, true);

        switch ($of) {
            case 'logs':
                return $configArray['logs'];
            default:
            case 'cache':
                return $configArray['cache']['path'];
        }
    }

    public function run(): void
    {
        switch ($this->command) {
            case 'start':
                $address = $this->argv[2] ?? 'localhost';
                $port = $this->argv[3] ?? '8080';
                $this->startServer($address, $port);
                break;
            case 'clear':
                $this->subCommand = $this->argv[2] ?? '';
                $this->clear();
                break;
            case 'config':
                $this->subCommand = $this->argv[2] ?? '';
                $field = $this->argv[3] ?? '';
                $value = $this->argv[4] ?? '';
                $this->config($field, $value);
                break;
            case 'debug':
                $this->debug();
                break;
            default:
                exit("Unknown command: $this->command\n");
        }
        exit("Terminated without errors.");
    }

    private function clear()
    {
        switch ($this->subCommand) {
            case 'cache':
                echo "Clearing cache...\n";
                $this->deleteFiles($this->cacheDir);
                break;
            case 'logs':
                echo "Clearing logs...\n";
                $this->deleteFiles($this->logsDir);
                break;
            default:
                exit("Missing or unknown argument: clear $this->subCommand\n");
        }
    }

    private function config($field, $value)
    {
        switch ($this->subCommand) {
            case 'restore':
                echo "Restoring config...\n";
                $this->restoreConfig();
                break;
            case 'set':
                if (empty($field) || empty($value)) {
                    exit("Missing field or value for 'set' command.\n");
                }
                echo "Setting config field: $field = $value\n";
                $this->updateConfig($field, $value);
                break;
            default:
                exit("Missing or unknown argument: config $this->subCommand\n");
        }
    }

    private function startServer($address, $port): void
    {
        echo "Starting PHP Development Server at $address:$port...\n";
        exec("cd ./public && php -S $address:$port");
    }

    private function updateConfig($field, $value): void
    {
        $configFile = $this->configDir . "/config.json";
        $configData = file_get_contents($configFile);

        $configArray = json_decode($configData, true);
        $this->setConfigField($configArray, $field, $value);

        $configData = json_encode($configArray, JSON_PRETTY_PRINT);
        if (!file_put_contents($configFile, $configData)) {
            exit("FATAL: Could not override configuration file!");
        }
    }

    private function setConfigField(&$array, $field, $value): void
    {
        $keys = explode('.', $field);

        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array = $value;
    }

    private function restoreConfig(): void
    {
        $configFile = $this->configDir . "/config.json";
        $defaultFile = $this->configDir . "/default.json";

        if (is_file($configFile) && is_file($defaultFile)) {
            if (!unlink($configFile)) {
                exit("FATAL: Could not delete $configFile");
            }

            if (!copy($defaultFile, $configFile)) {
                exit("FATAL: Could not restore configuration file. Please check file permissions.");
            }
        }
    }

    private function deleteFiles($folder): void
    {
        if (is_dir($folder)) {
            $files = scandir($folder);

            foreach ($files as $file) {
                if ($file === ".gitkeep") {
                    continue;
                }

                if (is_file($folder . "/" . $file)) {
                    if (unlink($folder . "/" . $file)) {
                        echo "Deleted $file\n";
                    } else {
                        echo "FATAL: Can not delete $file\n";
                    }
                }
            }
        } else {
            echo "Unknown directory\n";
        }
    }

    private function debug(): void
    {
        echo "cmd: $this->command\n";
        echo "argc: $this->argc\n";
        echo "argv: " . json_encode($this->argv) . "\n";
        echo "config path: $this->configDir\n";
        echo "logs path: $this->logsDir\n";
        echo "cache path: $this->cacheDir\n";
        echo "verifying... config (" . is_dir($this->configDir) . "), logs ("
            . is_dir($this->logsDir) . "), cache (" . is_dir($this->cacheDir) . ")\n";
        echo "default.json is present: " .  is_file($this->configDir . "/default.json") . "\n";
    }
}
