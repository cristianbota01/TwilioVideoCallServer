<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc0c3a05a110cb10d43087441687cd871
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twilio\\' => 7,
        ),
        'C' => 
        array (
            'CristianbotaIfortec\\TwilioVideoCallServer\\' => 42,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twilio\\' => 
        array (
            0 => __DIR__ . '/..' . '/twilio/sdk/src/Twilio',
        ),
        'CristianbotaIfortec\\TwilioVideoCallServer\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInitc0c3a05a110cb10d43087441687cd871::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc0c3a05a110cb10d43087441687cd871::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc0c3a05a110cb10d43087441687cd871::$classMap;

        }, null, ClassLoader::class);
    }
}