<?php

/**
 * @return select
 */
function sel(){
	$args = func_get_args();
	$s = new select();
	call_user_func_array(array($s, '__construct'), $args);
	return $s;
}

class select
{
	public $statement;
	public $params;
	protected $whereModes;
	protected $result;
	protected $filtersNeeded = array();

	function __construct()
	{
		$args = func_get_args();
		if(count($args) > 0)
		{
			$this->statement = $args[0];
		}

		if(count($args) > 1)
		{
			if($args[1] instanceof params)
			{
				$this->params = $args[1];
			}
			else
			{
				$this->params = new params($args[1]);
			}
		}
		else
		{
			$this->params = new params();
		}
		$this->whereModes = array();
		$this->whereModes[] = "AND";
	}

	function OrAppend(select $select)
	{
		$this->whereModes[] = "OR";
		$this->addConjunction();
		array_pop($this->whereModes);
		return $this->append($select);
	}

	function andAppend(select $select)
	{
		$this->whereModes[] = "AND";
		$this->addConjunction();
		array_pop($this->whereModes);
		return $this->append($select);
	}

	function append(select $select)
	{
		$this->statement .= " ";
		$this->statement .= $select->statement;
		$this->params->combine($select->params);
		return $this;
	}

	function Q($query)
	{
		$this->statement = rtrim(ltrim($this->statement)) . " " . $query;
		return $this;
	}

	function AndQ($where)
	{
		$this->whereModes[] = "AND";
		$this->addConjunction();
		$this->Q($where);
		array_pop($this->whereModes);
		return $this;
	}

	function OrQ($where)
	{
		$this->whereModes[] = "OR";
		$this->addConjunction();
		$this->Q($where);
		array_pop($this->whereModes);
		return $this;
	}

	function StartOr()
	{
		$this->addConjunction();
		$this->statement .= " (";
		$this->whereModes[] = "OR";
		return $this;
	}

	function endOr()
	{
		return $this->endConjunction();
	}

	function startAnd()
	{
		$this->addConjunction();
		$this->statement .= " (";
		$this->whereModes[] = "AND";
		return $this;
	}

	function endAnd()
	{
		return $this->endConjunction();
	}

	function endConjunction()
	{
		//by the end of this call, we need to be able to add conjunctions.
		if($this->canAddConjunction())
		{
			$this->statement .= ") ";
		}
		else //back up one conjunction and try again
		{
			while((substr($this->statement, -1) == " " || substr($this->statement, -1) == "(") && strlen($this->statement) > 0)
				$this->chop(1);
			if(strtolower(substr($this->statement,-3)) == "and")
				$this->chop(3);
			elseif(strtolower(substr($this->statement,-2)) == "or")
				$this->chop(2);
			$this->statement .= " ";
		}
		array_pop($this->whereModes);
		return $this;
	}

	function a($arg)
	{
		$this->statement .= " " . $this->params->add($arg);
		return $this;
	}

	function notLike($column, $arg)
	{
		return $this->like($column, $arg, true);
	}

	function like($column, $arg, $not="", $asSpecified=false)
	{
		if($not != "")
			$not = "NOT";

		if(strpos($arg, "%") !== false)
			$asSpecified = true;

		if(trim($arg) != "")
		{
			if($asSpecified == false)
				$arg = "%" . $arg . "%";
			$this->addConjunction();
			$this->statement .= " " . $column . " " . $not . " LIKE " . $this->params->add($arg);
		}
		return $this;
	}

	function dieEq($column, $arg)
	{
		$this->addConjunction();
		if(trim($arg) !== "")
			$this->statement .= " " . $column . " = " . $this->params->add($arg);
		else
			$this->statement .= " 1 = 0 ";
		return $this;
	}

	function eq($column, $arg)
	{
		if(trim($arg) !== "")
		{
			$this->addConjunction();
			$this->statement .= " " . $column . " = " . $this->params->add($arg);
		}
		return $this;
	}

	function dateEq($column, $arg)
	{
		if(trim($arg) !== "" && strtotime($arg) != false)
		{
			$this->addConjunction();
			$arg = date("m/d/Y g:i a", strtotime($arg));
			$this->statement .= " " . $column . " = " . $this->params->add($arg);
		}
		return $this;
	}

	function gt($column, $arg)
	{
		if(trim($arg) !== "")
		{
			$this->addConjunction();
			$this->statement .= " " . $column . " > " . $this->params->add($arg);
		}
		return $this;
	}

	function Lt($column, $arg)
	{
		if(trim($arg) !== "")
		{
			$this->addConjunction();
			$this->statement .= " " . $column . " < " . $this->params->add($arg);
		}
		return $this;
	}

	function gte($column, $arg)
	{
		if(trim($arg) !== "")
		{
			$this->addConjunction();
			$this->statement .= " " . $column . " >= " . $this->params->add($arg);
		}
		return $this;
	}

	function lte($column, $arg)
	{
		if(trim($arg) !== "")
		{
			$this->addConjunction();
			$this->statement .= " " . $column . " <= " . $this->params->add($arg);
		}
		return $this;
	}

	function notEq($column, $arg)
	{
		if(trim($arg) !== "")
		{
			$this->addConjunction();
			$this->statement .= " " . $column . " <> " . $this->params->add($arg);
		}
		return $this;
	}

	function isNull($column)
	{
		$this->addConjunction();
		$this->statement .= " " . $column . " IS NULL ";
		return $this;
	}

	function isNotNull($column)
	{
		$this->addConjunction();
		$this->statement .= " " . $column . " IS NOT NULL ";
		return $this;
	}

	function search($column, $arg) //, $delimiter
	{
		if(trim($arg) !== "")
		{
			$this->startAnd();
			$delimiter = " ";
			if(func_num_args() > 2)
				$delimiter = func_get_arg(2);
			$args = explode($delimiter, $arg);
			foreach($args as $a)
			{
				$this->like($column, $a);
			}
			$this->endAnd();
		}
		return $this;
	}

	function dateEqRange($column, $min, $max = "")
	{
		if(trim($min) != "" && strtotime($min) !== false
			&& (trim($max) == "" || strtotime($max) === false))
		{
			return $this->dateEq($column, $min);
		}
		return $this->dateRange($column, $min, $max);
	}

	function dateRange($column, $min) //, $max
	{
		if(trim($min) != "" && strtotime($min) != false)
		{
			$this->addConjunction();
			$min = date("m/d/Y g:i a", strtotime($min));
			$this->statement .= " " . $column . " >= " . $this->params->add($min);
		}
		$max = "";
		if(func_num_args() > 2)
			$max = func_get_arg(2);
		if(trim($max) != "" && strtotime($max) != false)
		{
			$this->addConjunction();
			$max = date("m/d/Y g:i a", strtotime($max));
			$this->statement .= " " . $column . " < " . $this->params->add($max);
		}
		return $this;
	}

	function notIn($column, $items)
	{
		return $this->in($column, $items, true);
	}

	function in($column, $items) //$not
	{
		$not = "";
		if(func_num_args() > 2)
			$not = func_get_arg(2) ? "NOT" : "";
		if(is_array($items) && count($items) > 0)
		{
			$this->addConjunction();
			$this->statement .= " " . $column . " " . $not . " IN (";
			foreach($items as $i)
			{
				$this->statement .= $this->params->add($i) . ", ";
			}
			$this->statement = substr($this->statement, 0,  strlen($this->statement) -2);
			$this->statement .= " ) ";
		}
		return $this;
	}

	function chop($amount)
	{
		$this->statement = substr($this->statement, 0, ($amount * -1));
		return $this;
	}

	protected function addConjunction()
	{
		if($this->canAddConjunction())
		{
			$this->statement .= " " . end($this->whereModes) . " ";
		}
		return $this;
	}

	protected function canAddConjunction()
	{
		$keywords = array("or","and","where","on");
		$q = strtolower(rtrim($this->statement));
		while((substr($q, -1) == " " || substr($q, -1) == "(") && strlen($q) > 0)
			$q = substr($q, 0, strlen($q) -1);
		if(strlen($q) <= 0)
			return false;
		foreach($keywords as $kw)
		{
			$len = strlen($kw);
			if(substr($q, $len * -1) == $kw && trim(substr($q, ($len + 1) * -1)) == $kw) //trim to ensure a space
				return false;
		}
		return true;
	}

	function startWhere()
	{
		$this->statement .= " WHERE ";
		return $this;
	}

	function endWhere()
	{
		while(substr($this->statement, -1) == " " && strlen($this->statement) > 0)
			$this->statement = substr($this->statement, 0, strlen($this->statement) -1);
		if(substr(strtolower($this->statement), -5) == "where")
			$this->Chop(5);
		$this->statement .= " ";
		return $this;
	}

	function orderBy($columns)
	{
		$this->statement .= " ORDER BY " . $columns;
		return $this;
	}

	function groupBy($columns)
	{
		$this->statement .= " GROUP BY " . $columns;
		return $this;
	}

	function execute()
	{
		$db = db::instance();
		return $db->execute($this->statement, $this->params->vals);
	}

	function executeSingleRow()
	{
		$db = db::instance();
		$values = $db->execute($this->statement, $this->params->vals);
		if(count($values) > 0){
			return $values[0];
		}
		return null;
	}

	function executeSingleColumn()
	{
		$db = db::instance();
		$values = $db->execute($this->statement, $this->params->vals);
		$ret = array();
		foreach($values as $row){
			if(count($row) > 0){
				$ret[] = current($row);
			}
		}
		return $ret;
	}

	function executeSingle()
	{
		$ret = $this->executeSingleColumn();
		if($ret && count($ret) > 0){
			return $ret[0];
		}
		return null;
	}
}

class params
{
	function __construct()
	{
		$args = func_get_args();
		if(count($args) > 0 && is_array($args[0]))
		{
			$this->vals = $args[0];
		}
		else
		{
			$this->vals = array();
		}
	}

	public static $current = 1;
	public $vals;

	function addNamed($name, $val)
	{
		if($name[0] != ":");
			$name = ":" . $name;
		$this->vals[$name] = $val;
		return $name;
	}

	function add($val)
	{
		$current = $this->GetCurrent();
		$this->vals[$current] = $val;
		self::$current++;
		return $current;
	}

	function GetCurrent()
	{
		return  ":val" . self::$current;
	}

	function Combine(Params $params)
	{
		$this->vals = array_merge($this->vals, $params->vals);
	}
}

class operation extends select
{
	public $items;
	public $table;

	function setTable($table)
	{
		$this->table = $table;
		return $this;
	}

	function __construct() //$items, $table
	{
		$args = func_get_args();
		if(count($args) > 0 && is_array($args[0]))
		{
			$this->items = $args[0];
		}
		if(count($args) > 1)
		{
			$this->table = $args[1];
		}
		if(count($args) > 2)
		{
			$this->statement = $args[2];
		}

		if(count($args) > 3)
		{
			if($args[3] instanceof params)
			{
				$this->params = $args[3];
			}
			else
			{
				$this->params = new params($args[3]);
			}
		}
		else
		{
			$this->params = new params();
		}
		$this->whereModes = array();
		$this->whereModes[] = "AND";
	}

	function addItem($name, $value)
	{
		if($value !== "")
			$this->items[$name] = $value;
		else
			$this->items[$name] = "NULL";
		return $this;
	}

	function addString($name, $value)
	{
		if($value !== "" && $value !== NULL)
			$this->items[$name] = trim($value);
		else
			$this->items[$name] = "NULL";
		return $this;
	}

	function addDate($name, $value)
	{
		if($value !== "")
			$this->items[$name] = date("m/d/y", strtotime($value));
		else
			$this->items[$name] = "NULL";
		return $this;
	}

	function addDateTime($name, $value)
	{
		if($value !== "")
			$this->items[$name] = date("m/d/y g:i:s a", strtotime($value));
		else
			$this->items[$name] = "NULL";
		return $this;
	}

	function addNumber($name, $value)
	{
		if(is_numeric($value))
			$this->items[$name] = $value;
		else
			$this->items[$name] = "NULL";
		return $this;
	}

	function addFloat($name, $value)
	{
		if($value !== "")
			$this->items[$name] = floatval($value);
		else
			$this->items[$name] = "NULL";
		return $this;
	}

	function addInt($name, $value)
	{
		if($value !== "")
			$this->items[$name] = intval($value);
		else
			$this->items[$name] = "NULL";
		return $this;
	}

	function addItems($items)
	{
		foreach($items as $name => $item)
		{
			$this->addItem($name, $item);
		}
		return $this;
	}

	function updateOrInsert($keyID)
	{
		if($keyID > 0)
		{
			$this->dieEq("ID", $keyID);
			$this->update();
			return $keyID;
		}
		else
		{
			return $this->insert();
		}
	}

	function update()
	{
		$db = db::instance();

		$table = $this->table;
		$vals_array = $this->items;
		if(!is_array($vals_array))
			$vals_array = array();

		$where = trim($this->statement);
		if(stristr($where, "where") != FALSE)
			$where = substr($where, 6);

		$params = $this->params;
		$query = "UPDATE " . $table . " SET ";
		while(list($key,$value)=each($vals_array))
		{
			if($value === "NULL")
				$query .= " " . $key . " = NULL, ";
			else
				$query .= " " . $key . " = " . $params->add($value) . ", ";
		}

		$query = substr($query, 0, -2);
		$query .= " WHERE " . $where;

		$db->execute($query, $params->vals);
	}

	function delete()
	{
		$db = db::instance();
		$table = $this->table;
		$where = trim($this->statement);
		if(stristr($where, "where") != FALSE)
			$where = substr($where, 6);
		$params = $this->params;
		$db->execute("delete from " . $table . " where ". $where, $params->vals);
	}

	function insert()
	{
		$db = db::instance();
		$table = $this->table;
		$vals_array = $this->items;
		if(count($vals_array) == 0)
			return;
		$query = "INSERT INTO " . $table . " (";
		while(list($key,$value)=each($vals_array))
		{
			if($value !== "NULL")
				$query .= $key . ", ";
		}
		reset($vals_array);
		$query = substr($query, 0, -2);
		$query .= ") VALUES(";
		$changed = "";

		$insertParams = new params();
		while(list($key,$value)=each($vals_array))
		{
			if($value !== "NULL")
			{
				$query .= $insertParams->add($value) . ", ";
			}
		}
		$query = substr($query, 0, -2);
		$query .= ")";
		$db->execute($query, $insertParams->vals);
		return $db->lastInsertId();
	}
}
?>