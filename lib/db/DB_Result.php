<?php
namespace app\lib\db;

class DB_Result
{
    protected $pdo_result;
    protected $db;

    public function __construct($result, DB $db)
    {
        $this->pdo_result = $result;
        $this->db = $db;
    }
}