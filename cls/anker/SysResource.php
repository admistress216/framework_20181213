<?php
namespace app\cls\anker;

use app\lib\db\DB;

class SysResource
{
    private static $sysResources = [];

    public static function getDB($isMaster = true, $dbname='')
    {
        if ($dbname) {
            $resourceName = $dbname;
        } else {
            if ($isMaster) {
                $resourceName = DB_MASTER_NAME;
            } else {
                $resourceName = DB_SLAVE_NAME;
            }
        }

        if (array_key_exists($resourceName, static::$sysResources)) {
            return static::$sysResources[$resourceName];
        }
        $db = new DB($isMaster, $resourceName);
        static::$sysResources[$resourceName] = $db;
        return $db;
    }
}