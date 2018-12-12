<?php
//配置文件
defined("PRODUCT_MODE") || define("PRODUCT_MODE", "production");
if (PRODUCT_MODE == "development") {
    ini_set("display_errors", "on");
    error_reporting(E_ALL & ~E_NOTICE);
    include_once __DIR__. "/config/develop.php";
} elseif (PRODUCT_MODE == "production") {
    ini_set("display_errors", "off");
    include_once __DIR__."/config/product.php";
}
require_once __DIR__. "/autoload.php";