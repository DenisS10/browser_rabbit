<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit56b681680d071b19a9a83bc6818a27c2
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PhpAmqpLib\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PhpAmqpLib\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-amqplib/php-amqplib/PhpAmqpLib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit56b681680d071b19a9a83bc6818a27c2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit56b681680d071b19a9a83bc6818a27c2::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}