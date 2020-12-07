<?php
/**
 * Utils
 *
 * @author Olivier Coste & Axel Aigret
 * @version 0 
 */

class CsvFile
{
	protected $_aColumns ;
	protected $_aLines ;
	protected $_sSeparator ;
	
	public function __construct($aColumns = null, $sSeparator = ';')
	{
		$this->_aColumns	= $aColumns ;
		$this->_aLines		= array() ;
		$this->_sSeparator	= $sSeparator ;
	}
		
	public function getColumns()
	{
		return $this->_aColumns ;
	}
	
	public function setColumns($aColumns)
	{
		$this->_aColumns = $aColumns ;
	}
	
	public function insertColumn($index, $name, $defaultValue)
	{
		array_splice($this->_aColumns, $index, 0, $name) ;
		
		foreach($this->_aLines as &$aLine)
		{
			if(count($aLine) < $index) $aLine = array_pad($aLine, $index, '') ;
			array_splice($aLine, $index, 0, $defaultValue) ;
		}
	}
	
	public function addLine($aLine)
	{
		array_push($this->_aLines, $aLine) ;
	}
	
	public function addLines($aLines)
	{
		foreach($aLines as $aLine) $this->addLine($aLine) ;
	}
	
	public function setLines($aLines)
	{
		$this->_aLines = $aLines ;
	}
	
	public function getLines()
	{
		return $this->_aLines ;
	}
	
	public function getLineAt($index)
	{
		return ($index >= 0 && $index < count($this->_aLines) ? $this->_aLines[$index] : array()) ;
	}
	
	public function removeLineAt($index, $nb = 1)
	{
		if($index >= 0 && $index < count($this->_aLines)) array_splice($this->_aLines, $index, $nb) ;
	}
	
	public function setLineAt($index,$aLine )
	{
		if($index >= 0 && $index < count($this->_aLines)) $this->_aLines[$index] = $aLine ;
	}
	
	protected function _toCSVField($str)
	{
		$str = str_replace('"', '""', $str) ;
		
		if($str)
			return '"' . $str . '"' ;
		else
			return '' ;
	}
	
	protected function generateLine($aLine)
	{
		$line = '' ;
		
		$i = 0 ;
		foreach($aLine as $value)
		{
			$line .= ($i > 0 ? $this->_sSeparator : '') . utf8_decode($this->_toCSVField($value)) ;
			$i++ ;
		}
		
		$line .= chr(13) ;
		
		return $line ; 
	}
	
	public function write($file)
	{	
		$handle = fopen($file, 'w');
		if ($handle)
		{
			fputcsv($handle, $this->_decodeToUTF8($this->_aColumns), $this->_sSeparator) ;
			foreach($this->_aLines as $line)
			{
				fputcsv($handle, $this->_decodeToUTF8($line), $this->_sSeparator) ;
			}
			fclose($handle);
		}
	}
	
	public function read($file, $bSkipFirstLine = true)
	{
		$this->_aColumns = array() ;
		$this->_aLines = array() ;
		
		//ini_set('auto_detect_line_endings', 'On') ;
		
		$handle = fopen($file, 'rb');
		if ($handle)
		{
			$firstLine = !$bSkipFirstLine ;
			for ($ligne = fgetcsv($handle, 0, $this->_sSeparator); !feof($handle); $ligne = fgetcsv($handle, 0, $this->_sSeparator))
			{
			   $j = sizeof($ligne);  
			   if(!$firstLine)
			   {
			   		$firstLine = true ;
			   		$this->_aColumns = $this->_encodeToUTF8($ligne) ;
			   }else{
			    	array_push($this->_aLines, $this->_encodeToUTF8($ligne)) ;
			   }
			}
			
			fclose($handle);
		}
		
		//ini_set('auto_detect_line_endings', 'Off') ;
	}
	
	protected function _encodeToUTF8(&$a)
	{
		foreach($a as $k => $v)
		{
			$a[$k] = utf8_encode($v) ;
		}
		
		return $a ;
	}
	
	protected function _decodeToUTF8(&$a)
	{
		foreach($a as $k => $v)
		{
			$v = str_replace(chr(10), ' ', $v) ;
			$v = str_replace(chr(13), ' ', $v) ;
			$a[$k] = utf8_decode(stripslashes($v)) ;
		}
		
		return $a ;
	}
	
	public function readAt($file, $index, $bSkipFirstLine = true)
	{
		$line = array() ;
		$this->_aLines = array() ;
		
		$handle = fopen($file, 'r');
		if ($handle)
		{
			if($bSkipFirstLine) $index++ ;
			
			$i = 0 ;
			for ($line = fgetcsv($handle, 0, $this->_sSeparator); !feof($handle) && $i < $index; $line = fgetcsv($handle, 0, $this->_sSeparator))
			{
				if(!$bSkipFirstLine || $i) array_push($this->_aLines, $line) ;
				$i++ ;
			}
			array_push($this->_aLines, $line) ;
			fclose($handle);
		}
		
		return $line ;
	}
	
	public function writeAt($file, $index, $aLine, $bSkipFirstLine = true)
	{
		$this->read($file, $bSkipFirstLine);
		
		$this->mergeLine($index, $aLine) ;
		
		$this->write($file) ;
	}
	
	public function mergeLine($index, $aLine)
	{
		if(!isset($this->_aLines[$index])) $this->_createEmptyLine($index, count($aLine)) ;
		$line = &$this->_aLines[$index] ;
		
		end($aLine) ;
		end($line) ;
		
		$l = max(array(key($aLine) + 1, key($line) + 1)) ;
		
		for($i = 0 ; $i < $l ; $i++)
		{
			if(isset($aLine[$i])) $line[$i] = $aLine[$i] ;
		}
	}
	
	protected function _createEmptyLine($index, $n)
	{
		$this->_aLines[$index] = array() ;
		$line = &$this->_aLines[$index] ;
		
		for($i = 0 ; $i < $n ; $i++)
		{
			$line[$i] = '' ;
		}
	}
	
	public function checkNotEmptyFields($index, $aFields)
	{
		$line = $this->_aLines[$index] ;
			
		$bNotEmpty = true ;
		foreach($aFields as $i)
		{
			$bNotEmpty = $bNotEmpty && !empty($line[$i]) ;
		}
		
		return $bNotEmpty ;
	}
	
	public function render()
	{
		$content	= $this->generateLine($this->_aColumns) ;
		
		foreach($this->_aLines as $line)
		{
			$content	.= $this->generateLine($line) ;
		}
		
		return $content ;
	}
	
	public function search($sExpr, $col = null)
	{
		$aIndex = array() ;
		
		for($i = 0 ; $i < count($this->_aLines) ; $i++)
		{
			$line = $col !== null ? array($this->_aLines[$i][$col]) : $this->_aLines[$i] ;
			
			if(in_array($sExpr, $line, true)) array_push($aIndex, $i) ;
		}
		
		return $aIndex ;
	}
	
	public function strpos($sExpr, $col)
	{
		$aIndex = array() ;
		
		for($i = 0 ; $i < count($this->_aLines) ; $i++)
		{
			if(strpos($this->_aLines[$i][$col], $sExpr) !== false) array_push($aIndex, $i) ;
		}
		
		return $aIndex ;
	}
}
?>