<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit407b1a1d21c575271f9d39c547c3e7b4
{
    public static $prefixLengthsPsr4 = array (
        'N' => 
        array (
            'NoahBuscher\\Macaw\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'NoahBuscher\\Macaw\\' => 
        array (
            0 => __DIR__ . '/..' . '/noahbuscher/macaw',
        ),
    );

    public static $classMap = array (
        'Admin\\BaseController' => __DIR__ . '/../..' . '/app/admin/BaseController.php',
        'Admin\\UserController' => __DIR__ . '/../..' . '/app/admin/UserController.php',
        'Admin\\VersionController' => __DIR__ . '/../..' . '/app/admin/VersionController.php',
        'Admin\\WithdrawController' => __DIR__ . '/../..' . '/app/admin/WithdrawController.php',
        'Controller\\ActionController' => __DIR__ . '/../..' . '/app/controllers/ActionController.php',
        'Controller\\ApiController' => __DIR__ . '/../..' . '/app/controllers/ApiController.php',
        'Controller\\InfoController' => __DIR__ . '/../..' . '/app/controllers/InfoController.php',
        'Controller\\UserController' => __DIR__ . '/../..' . '/app/controllers/UserController.php',
        'Core\\Ad' => __DIR__ . '/../..' . '/app/core/Ad.php',
        'Core\\Ad\\StartBot' => __DIR__ . '/../..' . '/app/core/Ad/StartBot.php',
        'Core\\Ad\\StartMid' => __DIR__ . '/../..' . '/app/core/Ad/StartMid.php',
        'Core\\Ad\\TaskDaily' => __DIR__ . '/../..' . '/app/core/Ad/TaskDaily.php',
        'Core\\Ad\\TaskNewer' => __DIR__ . '/../..' . '/app/core/Ad/TaskNewer.php',
        'Core\\Ad\\TaskTop' => __DIR__ . '/../..' . '/app/core/Ad/TaskTop.php',
        'Core\\Controller' => __DIR__ . '/../..' . '/app/core/Controller.php',
        'Core\\DB\\Pdo' => __DIR__ . '/../..' . '/app/core/DB/Pdo.php',
        'Core\\Db' => __DIR__ . '/../..' . '/app/core/Db.php',
        'Core\\Model' => __DIR__ . '/../..' . '/app/core/Model.php',
        'Core\\Oss' => __DIR__ . '/../..' . '/app/core/Oss.php',
        'Core\\Task' => __DIR__ . '/../..' . '/app/core/Task.php',
        'Core\\Task\\Box' => __DIR__ . '/../..' . '/app/core/Task/Box.php',
        'Core\\Task\\Drink' => __DIR__ . '/../..' . '/app/core/Task/Drink.php',
        'Core\\Task\\Sign' => __DIR__ . '/../..' . '/app/core/Task/Sign.php',
        'Core\\Task\\Walk' => __DIR__ . '/../..' . '/app/core/Task/Walk.php',
        'Core\\Task\\WalkStage' => __DIR__ . '/../..' . '/app/core/Task/WalkStage.php',
        'Core\\Wxpay' => __DIR__ . '/../..' . '/app/core/Wxpay.php',
        'Model\\ActivityModel' => __DIR__ . '/../..' . '/app/models/ActivityModel.php',
        'Model\\GoldModel' => __DIR__ . '/../..' . '/app/models/GoldModel.php',
        'Model\\UserModel' => __DIR__ . '/../..' . '/app/models/UserModel.php',
        'Model\\WalkModel' => __DIR__ . '/../..' . '/app/models/WalkModel.php',
        'Model\\WalkStageModel' => __DIR__ . '/../..' . '/app/models/WalkStageModel.php',
        'Model\\WithdrawModel' => __DIR__ . '/../..' . '/app/models/WithdrawModel.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit407b1a1d21c575271f9d39c547c3e7b4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit407b1a1d21c575271f9d39c547c3e7b4::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit407b1a1d21c575271f9d39c547c3e7b4::$classMap;

        }, null, ClassLoader::class);
    }
}
