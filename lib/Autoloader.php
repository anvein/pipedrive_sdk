<?php

namespace anvein\pipedrive_sdk;

use Exception;

class Autoloader
{
    private static $arPaths = [];

    /**
     * Запускает автозагрузку перечисленными способами.
     *
     * @throw Exception - в случае, если класс не найден
     */
    public static function init()
    {
        spl_autoload_register(function ($className) {
            $pos = strrpos($className, '\\');
            if ($pos !== false) {
                $className = substr($className, $pos + 1);
            }
            $pathToClassFile = '';
            $findClass = false;
            foreach (self::$arPaths as $path) {
                $pathToClassFile = $path . $className . '.php';
                if (file_exists($pathToClassFile)) {
                    $findClass = true;
                    break;
                }
            }
            if ($findClass) {
                require_once $pathToClassFile;
            } else {
                throw new Exception("Класс {$className} не найден");
            }
        });
    }

    /**
     * Добавление путей для поиска файлов с классами.
     *
     * @return array $paths - пути поиска файлов
     * @return array $arPaths - пути поиска файлов с классами
     *
     * @throws Exception - в случае, если пути поиска не заданы или один из них не существует
     */
    public static function addPaths(array $paths)
    {
        if (empty($paths)) {
            throw new \Exception('Не переданы пути поиска файлов с классми $paths');
        }
        foreach ($paths as $path) {
            self::addPath($path);
        }

        return self::$arPaths;
    }

    /**
     * Добавление путей для поиска файлов с классами.
     *
     * @param string $path - путь поиска файлов
     *
     * @return array $arPaths - пути поиска файлов с классами
     *
     * @throws Exception - в случае, если путь поиска не задан или не существует
     */
    public static function addPath($path)
    {
        if (empty($path)) {
            throw new \Exception('Не передан путь поиска файлов с классми $path');
        }
        if (is_dir($path)) {
            self::$arPaths[] = $path;

            return self::$arPaths;
        } else {
            throw new Exception("Путь для поиска файлов с классами не существует {$path}");
        }
    }
}
