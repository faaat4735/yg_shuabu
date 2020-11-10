<?php

define('ROOT_DIR', dirname(__DIR__)  . '/');
define('CONFIG_DIR', ROOT_DIR  . 'config/');
define('LOG_DIR', ROOT_DIR . 'log/');
define('VENDOR_DIR', ROOT_DIR . 'vendor/');


/**
 * load the private configure
 */
if (file_exists(CONFIG_DIR . 'config.private.php')) {
    include CONFIG_DIR . "config.private.php";
}


!defined('DB_HOST') && define('DB_HOST', 'localhost');
!defined('DB_PORT') && define('DB_PORT', 3306);
!defined('DB_USERNAME') && define('DB_USERNAME', 'root');
!defined('DB_PASSWORD') && define('DB_PASSWORD', '123456');
!defined('DB_DATABASE') && define('DB_DATABASE', 'rz_walk');

!defined('ENV_PRODUCTION') && define('ENV_PRODUCTION', FALSE);
!defined('DEBUG_MODE') && define('DEBUG_MODE', FALSE);

require VENDOR_DIR . 'autoload.php';