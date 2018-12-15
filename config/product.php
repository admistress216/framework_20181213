<?php
define("APP_ROOT", dirname(__DIR__));

define("DB_MASTER_NAME", "log_master"); //主库名称
define("DB_MASTER_DB_NAME", "log");
define("DB_MASTER_HOST", "localhost");
define("DB_MASTER_PORT", "3306");
define("DB_MASTER_USER", "root");
define("DB_MASTER_PASSWORD", '');
define("DB_MASTER_PREFIX", 'cctvnewsplatform_');

define("DB_SLAVE_NAME", "log_slave"); //主库名称
define("DB_SLAVE_DB_NAME","log");
define("DB_SLAVE_HOST","localhost");
define("DB_SLAVE_PORT","3306");
define("DB_SLAVE_USER","root");
define("DB_SLAVE_PASSWORD",'');
define("DB_SLAVE_PREFIX", 'cctvnewsplatform_');
