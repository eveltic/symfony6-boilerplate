<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf314224469239a491ec4c82bec7a6042
{
    public static $prefixLengthsPsr4 = array (
        'E' => 
        array (
            'Eveltic\\FormUpload\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Eveltic\\FormUpload\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf314224469239a491ec4c82bec7a6042::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf314224469239a491ec4c82bec7a6042::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf314224469239a491ec4c82bec7a6042::$classMap;

        }, null, ClassLoader::class);
    }
}
