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
    protected $_select = "SELECT *";//sql的select
    protected $_from = "";//sql的from
    protected $_where = []; //where条件的key
    protected $_where_value = [];//where条件需要绑定的值
    protected $_orderBy = [];//排序
    protected $_limit = "";//sql的limit
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

    /**
     * 初始化工作
     */
    private function init()
    {
        if($this->charset) {
            $this->query("set names {$this->charset}");
        }
    }

    /**
     * @desc 构造select条件
     * @param $select
     * @return $this
     * @example $this->db->select("id, name as name1")
     */
    public function select($select)
    {
        $this->_select = "SELECT ".$select;
        return $this;
    }

    /**
     * @desc 构造from条件
     * @param $from
     * @return $this
     */
    public function from($from)
    {
        $from = trim($from);
        $from = "FROM ".$this->_table_prefix.$from;

        $this->_from = $from;
        return $this;
    }

    /**
     * @dsec 构造where条件
     * @param array | string $where
     * @return $this
     * @example1 $this->db->where(["name"=>"tom","age"=>11])  or $this->db->where(["id"=>[1,2,3,4]]);
     * @example2 $this->db->where("name=? and age=?",['dxp',2]);
     */
    public function where($where, array $bindValues = [])
    {
        if (is_array($where)) {
            foreach($where as $key => $val) {
                if (!is_array($val)) {
                    $this->_where[] = $key;
                    $this->_where_value[] = $val;
                } else {
                    $str = $key. " IN(";
                    $marks = [];
                    foreach($val as $k=>$v) {
                        array_push($marks, "?");
                        array_push($this->_where_value, $v);
                    }
                    $markStr = implode(",", $marks);
                    $this->_where[] = $str.$markStr.")";
                }
            }
        }
        if (is_string($where)) {
            $this->_where[] = $where;
            $this->_where_value = array_merge($this->_where_value, $bindValues);
        }
        return $this;
    }

    public function order_by(array $orders)
    {
        foreach($orders as $key => $val) {
            $order = strtolower($val);
            if ($order != "asc" && $order != "desc") {
                continue;
            }
            $this->_orderBy[$key] = $val;
        }
        return $this;
    }

    public function query($sql, $bindValue = [], $isReset = true)
    {
        $sql = trim($sql);

        if($bindValue) {
            $n_sql = $sql;
            $last_query = "";
            $n_bindValue = $this->getSqlVal($bindValue);
            foreach($n_bindValue as $v) {
                if(($pos = strpos($n_sql, "?")) !== false) {
                    $last_query .= substr($n_sql, 0, $pos).$v;
                    $n_sql = substr($n_sql, $pos + 1);
                }
            }
            $this->_last_query = $last_query.$n_sql;
        } else {
            $this->_last_query = $sql;
        }

        if ($bindValue) {
            $statement = $this->pdo->prepare($sql);
            if ($statement === FALSE) {
                $this->throwPdoError($this->pdo);
            }
            if ($statement->execute($bindValue) === FALSE) {
                $this->throwResultError($statement);
            }
            return new DB_Result($statement, $this);
        } else {
            $statement = $this->pdo->query($sql);
            if ($statement === FALSE) {
                $this->throwPdoError($this->pdo);
            }
            return new DB_Result($statement, $this);
        }
    }

    public function get()
    {
        $sql = $this->getPreSQL();
        $val = $this->whVals();
        return $this->query($sql, $val);
    }

    protected function getCompileWh()
    {
        $temp = [];
        foreach($this->_where as $v) {
            if (preg_match('/\band\b|\bor\b/i', $v) == 0) {
                if (preg_match('/<|>|<=|>=|\!=|\blike\b|\bin\b/i', $v) == 0) {
                    array_push($temp, $v."=?");
                } elseif (preg_match('/\bin\b/i', $v)) {
                    array_push($temp, $v);
                }
            } else {
                array_push($temp, "($v)");
            }
        }
        $andStr = implode(" AND ", $temp);
        return $andStr ? "WHERE ".$andStr : $andStr;
    }

    public function limit($limit, $offset=0)
    {
        $limit = intval($limit);
        $offset = intval($offset);

        $limit = "LIMIT $limit";
        $this->_limit = $offset ? $limit." OFFSET $offset" : $limit;
        return $this;
    }

    protected function whVals() {
        return $this->_where_value;
    }

    //得到预编译sql
    protected function getPreSQL($sel=NULL)
    {
        if($sel) {
            $select = $sel;
        } else {
            $select = empty($this->_select) ? "SELECT *" : $this->_select;
        }
        $from = $this->_from;
        if (empty($from)) {
            throw new \Exception("未指明表名");
        }
        $where = $this->getCompileWh();

        $orderArr = [];
        foreach($this->_orderBy as $key=> $order) {
            array_push($orderArr, $key." ".$order);
        }
        $orderBy = !empty($orderArr) ? " ORDER BY ".implode(",", $orderArr) : "";
        $limit = $this->_limit;
        $sql = $select." ".$from;
        $sql .= $where ? " $where" : "";
        $sql .= $orderBy ? $orderBy : "";
        $sql .= $limit ? " $limit" : "";
        return $sql;
    }

    private function getSqlVal(array $val)
    {
        foreach($val as &$v){
            $v = is_string($v) ? "'{$v}'" : $v;
        }
        return $val;
    }

    private function throwPdoError(\PDO $pdo)
    {
        $errorInfo = $pdo->errorInfo();
        if ($errorInfo[0] === "00000") {
            return [];
        }
        throw new DbException("执行sql语言产生错误错误码:==>{$errorInfo[0]};错误消息:==>{$errorInfo[2]};");
    }
    private function throwResultError(\PDOStatement $query)
    {
        $errorInfo = $query->errorInfo();
        if($errorInfo[0] === "00000"){
            return [];
        }
        throw new DbException("获取结果集错误:错误码:==>{$errorInfo[0]};错误消息:==>{$errorInfo[2]};");
    }
}