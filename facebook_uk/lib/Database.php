<?php
class Database
{
	protected $_dbConn ;
	protected $_dbLink ;
	
	function Database()
	{
		$this->_dbConn = mysql_connect(DB_SERVER, DB_USER, DB_PASS) ;
		
		if(!is_resource($this->_dbConn))
		{
			//die("Error in Connecting to the Database Server...");
			die(mysql_error());
		}

		$this->_dbLink	= mysql_select_db(DB_TABLE, $this->_dbConn) ;
		mysql_query("SET NAMES 'UTF8'");
	}
	
	function selectonly($query)
	{
		$rs	=	mysql_query($query) or die(mysql_error());
		return $rs;
	}
		
	function selectAll($sql)
	{ 
		$retResult	=	array();
		
		$rs	=	mysql_query($sql) or die(mysql_error());		
			
		while($row =	@mysql_fetch_object($rs))
		{
			 array_push($retResult, $row) ;
		}
		
		return $retResult;
	}
	
	function select($sql)
	{ 
		$rs	=	mysql_query($sql) or die(mysql_error());		
			
		$row =	@mysql_fetch_object($rs) ;
		
		return $row;
	}
	
	function insert($table, $arFieldsValues)
	{
		$fields	=	array_keys($arFieldsValues);
		$values	=	array_values($arFieldsValues);
		
		$formatedFields	=	array();
		foreach($fields as $field)
		{
			$field	=	"`" . $field . "`";
			$formatedFields[]	=	$field;
		}
		
		$formatedValues	=	array();
		foreach($values as $val)
		{
			$val	=	"'" . mysql_escape_string($val) . "'";
			$formatedValues[]	=	$val;
		}
		
		$sql	=	"INSERT INTO `".$table."` (";
		$sql	.=	join(",  ",$formatedFields).") ";
		$sql	.=	"VALUES( ";
		$sql	.=	join(", ",$formatedValues);
		$sql	.=	")";
		
		mysql_query($sql) or die("Error: ".mysql_errno()." ".mysql_error());
		return mysql_insert_id();
		
		if(mysql_query($sql)) return mysql_insert_id();
		else return false;		
	}
	
	function update($table, $arFieldsValues, $where = '')
	{
		$formatedValues	=	array();
		foreach($arFieldsValues as $key => $val){
			//if(!is_numeric($val)){
				$val	=	"'".mysql_escape_string($val)."'";
			//}
			$formatedValues[]	=	"$key = $val";
		}
		
	    $sql	=	"UPDATE ".$table." SET ";
		$sql	.=	join(", ",$formatedValues);
		if($where != "")
		{
			$sql	.=	" WHERE ".$where;
		}
		
		$rs	=	mysql_query($sql) or die(mysql_error());
		return mysql_affected_rows();
		//if(mysql_query($sql)) return mysql_affected_rows();
		//else return false;
	}
	
	function delete($table, $where='')
	{
		$sql	=	"DELETE FROM ".$table;
		
		if($where) 
		{
			$sql	.=	" WHERE " . $where;
		}
		
		$rs	=	mysql_query($sql) or die(mysql_error());
		return mysql_affected_rows();
	}
	
	function query($sql) 
	{
		$rs	=	mysql_query($sql) or die(mysql_error());
		return mysql_affected_rows();
	}	
}
?>