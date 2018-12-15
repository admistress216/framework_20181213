<?php
require_once dirname(__DIR__). '/common.php';

use \app\cls\anker\Response;
use app\cls\anker\Config;
use app\cls\anker\SysResource;

try {
    $db =SysResource::getDB(false);
} catch (\Exception $e){
    Response::output(-1, $e->getMessage());
}
