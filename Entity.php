<?php

namespace Gregwar\DSD;

/**
 * Mapping très basique avec la base de données
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Entity
{
    /**
     * Table SQL
     */
    protected $table;

    /**
     * Champs
     */
    protected $datas = array();

    /**
     * Conditions
     */
    protected $conditions;

    /**
     * Construction et accès aux données
     */
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

    /**
     * Obtenir un enregistrement sous conditions
     */
    public function get($conditions)
    {
        $query = mysql_query('SELECT * FROM `'.$this->table.'` WHERE '.$condition.' LIMIT 1');
        $this->conditions = $conditions;
        $this->datas = mysql_fetch_assoc($query);
    }

    /**
     * Insérer l'enregistrement
     */
    public function insert()
    {
        $sql = 'INSERT INTO `'.$this->table.'` ';

        $keys = array_keys($this->datas);
        foreach ($keys as $i => $v) {
            $keys[$i]="`$v`";
        }

        $sql .= '('.implode(',',$keys).') VALUES ';

        $values = array_values($this->datas);
        foreach ($values as $i => $v) {
            if (is_null($v)) {
                $values[$i] = 'NULL';
            } else {
                $values[$i]='"'.mysql_real_escape_string($v).'"';
            }
        }
        $sql .= ' ('.implode(',', $values).')';

        mysql_query($sql);

        return mysql_insert_id();
    }

    /**
     * Mise à jour d'un enregistrement
     */
    public function update($conditions = '')
    {
        if (!$conditions) {
            $conditions = $this->conditions;
        }
        if ($conditions)
            $conditions = ' WHERE '.$cond;

        $sql = 'UPDATE `'.$this->table.'` SET ';
        $sets = array();
        foreach($this->datas as $k => $v) {
            if (is_null($v)) {
                $val = 'NULL';
            } else {
                $val ='"'.mysql_real_escape_string($v).'"';
            }
            $sets[] = '`'.$k.'`='.$val;
        }
        $sql .= implode(',', $sets);
        $sql .= $conditions;

        mysql_query($sql);
    }

    public function dump()
    {
        $string = '';
        
        foreach ($this->datas as $k => $v) {
            $string.= $k.': '.$v."\n";
        }

        return $string;
    }

    public static function error($sql, $msg)
    {
        echo "<span style='font-family: Courier;'>";
        echo "<b>Table Error:</b> ($sql): $msg";
        echo "</span>";
        exit(0);
    }
}

