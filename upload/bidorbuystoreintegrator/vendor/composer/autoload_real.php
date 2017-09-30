<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitf2b7e19622c75c9e964bdacbe2fed909
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitf2b7e19622c75c9e964bdacbe2fed909', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInitf2b7e19622c75c9e964bdacbe2fed909', 'loadClassLoader'));

        $useStaticLoader = PHP_VERSION_ID >= 50600 && !defined('HHVM_VERSION') && (!function_exists('zend_loader_file_encoded') || !zend_loader_file_encoded());
        if ($useStaticLoader) {
            require_once __DIR__ . '/autoload_static.php';

            call_user_func(\Composer\Autoload\ComposerStaticInitf2b7e19622c75c9e964bdacbe2fed909::getInitializer($loader));
        } else {
            $classMap = require __DIR__ . '/autoload_classmap.php';
            if ($classMap) {
                $loader->addClassMap($classMap);
            }
        }

        $loader->setClassMapAuthoritative(true);
        if (method_exists($loader, 'setApcuPrefix')) { $loader->setApcuPrefix('1YEpMjd/O1H3eds6bL7x3'); }
        $loader->register(true);

        return $loader;
    }
}
