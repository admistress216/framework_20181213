<?php
$config[DB_MASTER_NAME] = [
    "host"=>DB_MASTER_HOST,
    "database"=>DB_MASTER_DB_NAME,
    "user"=>DB_MASTER_USER,
    "port"=>DB_MASTER_PORT,
    "password"=>DB_MASTER_PASSWORD,
    "db_prefix"=>DB_MASTER_PREFIX,
];
$config[DB_SLAVE_NAME] = [
    "host"=>DB_SLAVE_HOST,
    "database"=>DB_SLAVE_DB_NAME,
    "user"=>DB_SLAVE_USER,
    "port"=>DB_SLAVE_PORT,
    "password"=>DB_SLAVE_PASSWORD,
    "db_prefix"=>DB_SLAVE_PASSWORD,
];
return $config;