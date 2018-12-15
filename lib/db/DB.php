<?php
/**
 * 查询构造器
 */
namespace app\lib\db;

use app\cls\anker\Config;

class DB
{
    protected $pdo;
    protected $dsn;
    protected $userName;
    protected $password;
    protected $_table_prefix = "";//表前缀
    protected $_last_query = "";//最后一条查询
    protected $charset = "utf8mb4";

    /**
     * DB constructor.
     * @param bool $is_master 是否是连接主库
     * @param string $resourceName
     */
    public function __construct($is_master=true, $resourceName = '')
    {
        Config::load("db.config", "db");
        $config = Config::item("db", $resourceName);
        if (empty($config["database"])) {
            throw new DbException("数据库连接database不能为空");
        }
        if (empty($config["host"])) {
            throw new DbException("数据库连接host不能为空");
        }
        $dsn = "mysql:dbname={$config['database']};host={$config['host']};port={$config['port']}";

        $this->dsn = $dsn;
        $this->userName = $config["user"];
        $this->password = $config["password"];

        $this->pdo = new \PDO($dsn, $config["user"], $config["password"]);

        if(isset($config["db_prefix"])) {
            $this->_table_prefix = $config["db_prefix"];
        }
        $this->init();
    }

    private function init()
    {
        if($this->charset) {
            $this->query("set names {$this->charset}");
        }
    }

    public function query($sql, $bindValue = [], $isReset = true)
    {
        $sql = trim($sql);
        $this->_last_query = $sql;

        $statement = $this->pdo->query($sql);
        if ($statement === FALSE) {
            $this->throwPdoError($this->pdo);
        }
        return new DB_Result($statement, $this);
    }

    private function throwPdoError(\PDO $pdo)
    {
        $errorInfo = $pdo->errorInfo();
        if ($errorInfo[0] === "00000") {
            return [];
        }
        throw new DbException("执行sql语言产生错误错误码:==>{$errorInfo[0]};错误消息:==>{$errorInfo[2]};");
    }
}