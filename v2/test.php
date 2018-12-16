<?php
require_once dirname(__DIR__). '/common.php';

use \app\cls\anker\Response;
use app\cls\anker\Config;
use app\cls\anker\SysResource;

try {
    $db =SysResource::getDB(false);
    $db->select("job_id,file_size,file_name")->from("upload_record")->limit(10);
    $db->where(["file_size" => [12267801,20478510,125501444]]);
    $query = $db->get();
    $res = $query->all();
    var_dump($res);
} catch (\Exception $e){
    Response::output(-1, $e->getMessage());
}
