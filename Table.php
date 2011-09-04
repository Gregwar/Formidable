<?php

namespace Gregwar\DSD;

class Table
{
	private $table;
	private $datas = array();
	private $cond;
	
    public function __construct($tableName)
    {
		$this->table = $tableName;
	}

    public function __set($var, $value)
    {
		$this->datas[$var] = $value;
	}

    public function __get($var)
    {
		return $this->datas[$var];
	}

    public function __isset($var)
    {
		return isset($this->datas[$var]);
	}

    public function __unset($var)
    {
		unset($this->datas[$var]);
	}

    public function get($cond)
    {
		$query = mysql_query("SELECT * FROM `".$this->table."` WHERE $cond LIMIT 1");
		$this->cond=$cond;
		$this->datas = mysql_fetch_assoc($query);
	}

    public function insert()
    {
		$sql = "INSERT INTO `".$this->table."` ";
		$keys = array_keys($this->datas);
		foreach ($keys as $i => $v) {
			$keys[$i]="`$v`";
		}
		$sql .= "(".implode(",",$keys).") VALUES ";
		$values = array_values($this->datas);
		foreach ($values as $i => $v) {
			if (is_null($v))
				$values[$i]="NULL";
			else
				$values[$i]="\"".mysql_real_escape_string($v)."\"";
		}
		$sql .= " (".implode(",",$values).")";
		mysql_query($sql) or die(self::error($sql, mysql_error()));
		return mysql_insert_id();
	}

    public function update($cond="")
    {
		if (!$cond) {
			$cond=$this->cond;
		}
		if ($cond)
			$cond=" WHERE $cond";

		$sql = "UPDATE `".$this->table."` SET ";
		$sets = array();
		foreach($this->datas as $k => $v) {
			if (is_null($v))
				$val="NULL";
			else	$val ="\"".mysql_real_escape_string($v)."\"";
			$sets[] = "`".$k."`=$val";
		}
		$sql .= implode(",", $sets);
		$sql .= $cond;
		mysql_query($sql) or die(self::error($sql, mysql_error()));
	}

    public static function error($sql, $msg)
    {
		echo "<span style='font-family: Courier;'>";
		echo "<b>Table Error:</b> ($sql): $msg";
		echo "</span>";
		exit(0);
	}
}

