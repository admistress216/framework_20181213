<?php
require_once dirname(__DIR__). '/common.php';

use \app\cls\anker\Response;
use app\cls\anker\Config;
use app\cls\anker\SysResource;

try {
    $db =SysResource::getDB(false);
    $db->select("job_id,file_size,file_name")->from("upload_record")->limit(10);
    $query = $db->get();
    $res = $query->all();
    var_dump($res);
    $db->select("job_id,file_size")->from("upload_record")->limit(10);
    $query = $db->get();
    $res1 = $query->one();
    var_dump($res1);
} catch (\Exception $e){
    Response::output(-1, $e->getMessage());
}
