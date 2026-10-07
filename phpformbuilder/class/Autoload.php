<?php

namespace phpformbuilder;

class Autoload
{
    public static function load(string $class): void
    {
        /* Define the paths to the directories holding class files */

        $paths = array(
            __DIR__ . \DIRECTORY_SEPARATOR,
            dirname(__DIR__, 2) . \DIRECTORY_SEPARATOR . 'drag-n-drop-form-builder' . \DIRECTORY_SEPARATOR . 'class' . \DIRECTORY_SEPARATOR,
            dirname(__DIR__) . \DIRECTORY_SEPARATOR . 'plugins' . \DIRECTORY_SEPARATOR
        );
        foreach ($paths as $path) {
            $file = $path . str_replace('\\', \DIRECTORY_SEPARATOR, $class) . '.php';
            if (file_exists($file) === true) {
                require_once $file;
                break;
            }
        }
    }
}
