<?php
/*
 * +----------------------------------------------------------------------
 * | Presty Framework
 * +----------------------------------------------------------------------
 * | Copyright (c) 20021~2022 Tomanday All rights reserved.
 * +----------------------------------------------------------------------
 * | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 * | Email: 790455692@qq.com
 * +----------------------------------------------------------------------
 */

namespace presty;

use PDO;
use presty\exception\database\DatabaseException;

class Database
{

    //数据表主键
    protected $primaryKey = "";

    //PDO驱动
    protected $db = "";

    //操作的数据表
    protected $dbtable = "";

    //数据表格式
    protected $ts = [];

    //where子句储存
    protected $whereClause = "";

    //操作结果储存
    protected $operationResults = true;

    //数据库类型
    protected $dbtype = "mysql";

    //数据库主机地址
    protected $dbhost = "localhost";

    //数据库名
    protected $dbname = "";

    //数据库用户名
    protected $dbuser = "";

    //数据库用户密码
    protected $dbpass = "";

    //数据库端口
    protected $dbport = "3306";

    //数据表命名前缀
    protected $dbprefix = "";

    //本地数据库文件
    protected $dbfile = "";

    //数据库驱动程序
    protected $dsn = "";


    function __construct ($dbtype = "mysql", $dbhost = "localhost", $dbname = "", $dbuser = "", $dbpass = "", $dbport = 3306, $dbprefix = "", $dbfile = "", $dbtable = "")
    {
        app()->setArrayVar("hasBeenRun","database"," - Database_Init");
        $this->dbtype = $dbtype;
        $this->dbhost = $dbhost;
        $this->dbname = $dbname;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        $this->dbport = $dbport;
        $this->dbprefix = $dbprefix;
        $this->dbfile = $dbfile;
        $this->dbtable = $dbtable;
        if (get_config('database_auto_load',false)) {
            $this->init ();
        }
        return $this;
    }

    public function init ()
    {
        try {
            $this->dsn = $this->dbtype . ":host=" . $this->dbhost . ";dbname=" . $this->dbname;
            $hasBeenRun['database'] = " - Database_Init";
            $this->db = $dbh = new PDO($this->dsn, $this->dbuser, $this->dbpass, [PDO::ATTR_PERSISTENT => true]); //初始化一个PDO对象
            $this->db->setAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);                                  //设置错误模式
        } catch (PDOException $e) {
            new DatabaseException("Database Connect Error: " . $e->getMessage (),__FILE__,__LINE__);
        }
        $sth = $this->db->prepare ("SELECT * FROM " . $this->dbtable);
        $sth->execute ();

        $data = $sth->fetchAll ();
        if (count ($data, 0) >= 0) {
            $key = array_values ($data);
            $stmt = $this->db->prepare ("DESC " . $this->dbtable);
            $stmt->execute ();
            $this->ts = array_flip ($stmt->fetchAll (PDO::FETCH_COLUMN));
        }
        return $this;
    }

    public function setVars ($varName, $value)
    {
        eval("\$this->$varName = \"$value\";");
        return $this;
    }

    public function getVars ($varName)
    {
        eval("\$returnText = \"$varName\";");
        /** @var TYPE_NAME $returnText */
        return $returnText;
    }

    /*
    *执行一条原生SQL语句
    *@param data SQL语句（String）
    *返回操作结果（True|False）
    */
    public function query ($stmt): bool
    {
        $state = $this->db->prepare ($stmt);
        $this->operationResults = $state->execute ();
        return $this->operationResults;
    }

    /*
    *设置默认查询的数据主键
    *@param key 主键名（String）
    *返回自身或操作结果（This|False）
    */
    public function setKey ($key)
    {
        if (isset($this->ts[$key])) {
            $this->primaryKey = $key;
            return $this;
        } else {
            new DatabaseException("指定的主键字段".$key."不存在",__FILE__,__LINE__);
            return false;
        }
    }

    /*
    *设置默认查询的数据表
    *@param dbtable 数据表名（String）
    *返回自身或操作结果（This|False）
    */
    public function setTable ($dbtable)
    {
        $this->query ("SHOW TABLES LIKE '" . $dbtable . "'");
        if ($this->operationResults) {
            $this->dbtable = $dbtable;
            return ($this);
        } else {
            new DatabaseException("指定的数据表".$this->dbtable."不存在",__FILE__,__LINE__);
        }
    }

    /*
    *展开子句
    *@param where 要解析的句式（Array）
    *返回完整句式（String）
    */
    protected function implodeWhere ($data): string
    {
        $result = [];
        $condition = 'AND';
        $relation = "=";
        if (count ($data) != count ($data, COUNT_RECURSIVE)) {
            $condition = " " . array_search (current ($data), $data) . " ";
            $data = current ($data);
        }
        foreach ($data as $key => $value) {
            if (is_string ($value)) $value = $this->db->quote ($value);
            $isMatched = preg_match ('/\[.+?\]/', $key, $matches);
            if ($isMatched != 0) $relation = preg_replace ('/(\[)(.+?)(\])/', '$2', $matches)[0];
            $key = str_replace ("[" . $relation . "]", "", $key);
            $result[count ($result)] = $key . $relation . $value;
        }
        $clause = "WHERE " . implode ($condition, $result) . " ";
        return $clause;
    }

    /*
    *解析子句
    *@param where 要解析的句式（Array）
    *返回完整句式（String）
    */
    protected function parseClause ($where): string
    {
        $clause = '';
        $whereresult = $groupbyresult = $orderresult = $havingresult = $limitresult = $likeresult = $matchresult = $varsresult = "";
        $conditions = array_diff_key ($where, array_flip (
            ['GROUPBY', 'ORDER', 'HAVING', 'LIMIT', 'LIKE', 'MATCH', 'VARS']
        ));
        if (!empty($conditions)) {
            if (isset($conditions['WHERE'])) {
                $whereresult = $this->implodeWhere ($conditions['WHERE']);
            } else {
                $whereresult = $this->implodeWhere ($conditions);
            }
        }
        if (isset($where['GROUPBY'])) {
            $groupbyresult = ' GROUP BY ' . $where['GROUPBY'] . " ";
        }
        if (isset($where['VARS'])) {
            $varsresult = implode (" ", $where['VARS']) . " ";
        }
        $result = $whereresult . $groupbyresult . $orderresult . $havingresult . $limitresult . $likeresult . $matchresult . $varsresult;
        return $result;
    }

    /*
    *插入一条新数据
    *@param table 数据表名（String|Array）
    *@param fieldName 字段名（String|Array）
    *@param fieldValue 字段值（Any）
    *返回操作结果（True|False）
    */
    public function insert (?array $fieldName, ?array $fieldValue, $table = ""): bool
    {
        foreach ($fieldValue as $key => $value) {
            if (is_string ($fieldValue)) {
                $fieldValue[$key] = $this->db->quote ($value);
            }
        }
        if (empty($table)) $table = $this->dbtable;
        if (is_array ($fieldValue)) {
            $fieldValue = implode (', ', $fieldValue);
        }
        if (is_array ($fieldName)) {
            $fieldName = implode (', ', $fieldName);
        }
        $this->query ("INSERT INTO " . $table . "(" . $fieldName . ") VALUES(" . $fieldValue . ")");
        return $this->operationResults;
    }

    /*
    *删除一条数据
    *@param table 数据表名（String）
    *@param where 受影响的行数（Any）
    *返回操作结果（True|False）
    */
    public function delete ($where = "", $table = ""): bool
    {
        if (empty($table)) $table = $this->dbtable;
        if (empty($where)) $table = $this->whereClause;
        if (!empty($where)) $where = $this->parseClause ($where);
        $this->query ("DELETE FROM $table $where");
        return $this->operationResults;
    }

    /*
    *更新一条数据
    *@param key 要更新的主键（Array|String）
    *@param value 要更新的键值（Array|String）
    *@param table 数据表名（String）
    *@param where 受影响的行数（Any）
    *返回操作结果（True|False）
    */
    public function update (?array $key, ?array $value, $where = "", $table = ""): bool
    {
        $update = "UPDATE ";
        if (empty($table)) $table = $this->dbtable;
        if (empty($where)) $table = $this->whereClause;
        $update .= "$table ";
        if (is_array ($key)) {
            foreach ($key as $k => $v) {
                $condition[$k] = $v . "=" . $value[$k];
            }
            $main = implode (",", $condition);
        } else {
            $main = $key . "=" . $value;
        }
        if (!empty($where)) {
            $update .= "SET $main " . $this->parseClause ($where);
        } else {
            $update .= "SET $main";
        }
        $this->query ($update);
        return $this->operationResults;
    }

    /*
    *查找一条数据
    *@param column 要查询的键名（String）
    *@param where 要查找的键值（Any）
    *@param table 数据表名（String）
    *返回查询结果（array）
    */
    public function select ($column, $where = "", $table = ""): array
    {
        $select = "";
        if (empty($table)) $table = $this->dbtable;
        if (empty($table)) $table = $this->whereClause;
        if (!empty($where)) {
            $select = "SELECT $column FROM $table " . $this->parseClause ($where);
        } else {
            $select = "SELECT $column FROM $table";
        }
        $state = $this->db->query ($select);
        $query = $state->fetch (PDO::FETCH_ASSOC);
        return $query;
    }

    /*
    *定义Where子句
    *@param where 要查找的键值（Any）
    *返回自身或操作结果（This|False）
    */
    public function where ($where)
    {
        $this->whereClause = $this->parseClause ($where);
        return $this;
    }
}