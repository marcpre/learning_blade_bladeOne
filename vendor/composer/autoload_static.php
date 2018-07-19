<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb9a54d4249cb279a1d37c1277432aaec
{
    public static $prefixLengthsPsr4 = array (
        'e' => 
        array (
            'eftec\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'eftec\\' => 
        array (
            0 => __DIR__ . '/..' . '/eftec/bladeone/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb9a54d4249cb279a1d37c1277432aaec::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb9a54d4249cb279a1d37c1277432aaec::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}