<?php
require_once dirname(__DIR__). '/common.php';

use \app\cls\anker\Response;
use app\cls\anker\Config;

try {
    $config = Config::load("db.config");
} catch (\Exception $e){
    Response::output(-1, $e->getMessage());
}
