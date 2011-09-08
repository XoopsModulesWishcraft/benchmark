<?php

if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}
/**
 * Class for Benchmarking
 * @author Simon Roberts (simon@chronolabs.coop)
 * @copyright copyright (c) 2000-2009 XOOPS.org
 * @package kernel
 */
class BenchmarkTables extends XoopsObject
{
	var $_fHandler;
	
    function BenchmarkTables($fid = null)
    {
    	$this->_fHandler = xoops_getmodulehandler('fields', 'benchmark');
    	
        $this->initVar('tbid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('tid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('fids', XOBJ_DTYPE_ARRAY, array(), false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 128);
        $this->initVar('index', XOBJ_DTYPE_INT, null, false);
        $this->initVar('test', XOBJ_DTYPE_ENUM, null, false, false, false, array('_MI_BENCHMARK_CREATE','_MI_BENCHMARK_SELECT','_MI_BENCHMARK_INSERT','_MI_BENCHMARK_UPDATE','_MI_BENCHMARK_UPDATEALL','_MI_BENCHMARK_DELETE','_MI_BENCHMARK_DELETEALL','_MI_BENCHMARK_ALTER','_MI_BENCHMARK_RENAME','_MI_BENCHMARK_SMARTY'));
        $this->initVar('altered', XOBJ_DTYPE_ENUM, '_NO', false, false, false, array('_YES','_NO'));
        $this->initVar('engine', XOBJ_DTYPE_ENUM, 'INNODB', false, false, false, array('INNODB','MYISAM'));
        $this->initVar('charset', XOBJ_DTYPE_TXTBOX, 'utf8', false, 35);
        $this->initVar('fields', XOBJ_DTYPE_INT, null, false);
        $this->initVar('created', XOBJ_DTYPE_INT, null, false);		
        $this->initVar('prepared', XOBJ_DTYPE_INT, null, false);        
      	$this->initVar('truncated', XOBJ_DTYPE_INT, null, false);
        $this->initVar('dropped', XOBJ_DTYPE_INT, null, false);
    }
    
    function getCreateTable() {
    	$sql = 'CREATE TABLE `'.$GLOBALS['xoopsDB']->prefix($this->getVar('name'))."` (\n";
    	$criteria= new CriteriaCompo(new Criteria('required', '_YES'));
    	$count = $this->_fHandler->getCount($criteria);
    	foreach ($this->_fHandler->getObjects($criteria, true) as $fid => $field) {
    		$i++;
    		$sql .= '`'.$field->getVar('name').'` '.$field->getVar('create_sql').($count>$i||count($this->getVar('fids'))>0?",\n":""); 	
    	}
    	$criteria= new CriteriaCompo(new Criteria('required', '_YES'));
    	$criteria->add(new Criteria('primarykey', '_YES'));
    	$primarykey = $this->_fHandler->getCount($criteria);
    	$criteria= new CriteriaCompo(new Criteria('`key`', '', 'NOT LIKE'));
    	$keys = $this->_fHandler->getCount($criteria);
    	$count = count($this->getVar('fids'));
    	$i=0;
    	foreach ($this->getVar('fids') as $name => $field) {
    		$i++;
    		$fld = $this->_fHandler->get($field['fid']);
    		$fldindex[$field['fid']] = $name;
    		$sql .= '`'.$name.'` '.$fld->getVar('create_sql').($count>$i||$primarykey!=0||$keys!=0?",\n":""); 	
    	}
    	if ($primarykey>0) {
    		$criteria= new CriteriaCompo(new Criteria('required', '_YES'));
    		$criteria->add(new Criteria('primarykey', '_YES'));
    		$key = $this->_fHandler->getObjects($criteria, false);
    		if (is_object($key[0]))
    			$sql .= 'PRIMARY KEY (`'.$key[0]->getVar('name').'`)'.($keys!=0?",\n":"");
    	}
    	if ($keys>0) {
    		$criteria= new CriteriaCompo(new Criteria('`key`', '', 'NOT LIKE'));
    		$criteria->setSort('`key`');
    		foreach($this->_fHandler->getObjects($criteria, true) as $fid => $key) {
    			$tmp[$key->getVar('key')][$fid] = $fldindex[$fid]; 
    		}
    		$i=0;
    		$count = count($tmp);
    		foreach($tmp as $keyname => $keyfields) {
    			$i++;
    			$sql .= 'KEY `'.$keyname.'` (`'.implode('`,`', $keyfields).'`)'.($count>$i?",\n":"");
    		}
    	}
    	$sql .= "\n) ENGINE=".$this->getVar('engine').(strlen($this->getVar('charset'))>0?" DEFAULT CHARSET=".$this->getVar('charset'):"");
    	return array('create'=>array($this->getVar('tbid')=>array($this->getVar('name')=>$sql)));
    }

    function getInsertArray($number=1) {
    	$sql = array();
    	for($c=0;$c<=$number;$c++) {
	    	$criteria= new CriteriaCompo(new Criteria('required', '_YES'));
	    	$criteria->add(new Criteria('primarykey', '_NO'));
	    	$count = $this->_fHandler->getCount($criteria);
	    	$values = array();
	    	foreach ($this->_fHandler->getObjects($criteria, true) as $fid => $field) {
	    		if ($field->getVar('name')=='created') {
	    			$values[$field->getVar('name')] = (time());
	    		} else {
	    			$values[$field->getVar('name')] = ($this->getVar($field->getVar('name')));
	    		} 	
	    	}
		   	foreach ($this->getVar('fids') as $name => $field) {
	    		$i++;
	    		$fld = $this->_fHandler->get($field['fid']);
	    		$values[$name] = (benchmark_getFillValue($fld, $this));
	    	}
	    	$i=0;
	    	$count=count($values);
	    	foreach($values as $fldname => $value) {
	    		$sql['insertasobject'][$this->getVar('tbid')][$c][$fldname] = $value;
	    	}
    	}
    	return $sql;
    }
    
	function getUpdateArray($number=1) {
    	$sql = array();
    	for($c=0;$c<=$number;$c++) {
		   	foreach ($this->getVar('fids') as $name => $field) {
	    		$i++;
	    		$fld = $this->_fHandler->get($field['fid']);
	    		$values[$name] = benchmark_getFillValue($fld, $this);
	    	}
	    	$i=0;
	    	$count=count($values);
	    	foreach($values as $fldname => $value) {
	    		$sql['updateasobject'][$this->getVar('tbid')][$c][$fldname] = $value;
	    	}
    	}
    	return $sql;
    }

    function getUpdateAllArray() {
    	$sql = array();
		foreach ($this->getVar('fids') as $name => $field) {
	    	$i++;
	    	$fld = $this->_fHandler->get($field['fid']);
	    	$values[$name] = benchmark_getFillValue($fld, $this);
	    }
	    $c=0;
	    $count=count($values);
	    foreach($values as $fldname => $value) {
	    	$sql['updateall'][$this->getVar('tbid')][$c][$fldname] = $value;
	    }
    	return $sql;
    }
    
    function getInsertIntoTable($number=1) {
    	$sql = array();
    	for($c=0;$c<=$number;$c++) {
	    	$criteria= new CriteriaCompo(new Criteria('required', '_YES'));
	    	$criteria->add(new Criteria('primarykey', '_NO'));
	    	$count = $this->_fHandler->getCount($criteria);
	    	$values = array();
	    	foreach ($this->_fHandler->getObjects($criteria, true) as $fid => $field) {
	    		if ($field->getVar('name')=='created') {
	    			$values[$field->getVar('name')] = $GLOBALS['xoopsDB']->quote(time());
	    		} else {
	    			$values[$field->getVar('name')] = $GLOBALS['xoopsDB']->quote($this->getVar($field->getVar('name')));
	    		} 	
	    	}
		   	foreach ($this->getVar('fids') as $name => $field) {
	    		$i++;
	    		$fld = $this->_fHandler->get($field['fid']);
	    		$values[$name] = $GLOBALS['xoopsDB']->quote(benchmark_getFillValue($fld, $this));
	    	}
	    	$i=0;
	    	$count=count($values);
	    	foreach($values as $fldname => $value) {
	    		$i++;
	    		$out_fields = "`".$fldname."`".($count>$i?', ':'');
	    		$out_value = $value.($count>$i?', ':'');
	    	}
	      	$sql['insert'][$this->getVar('tbid')][$c] = 'INSERT INTO `'.$GLOBALS['xoopsDB']->prefix($this->getVar('name'))."` (".$out_fields.') VALUES ('.$out_value.')';
    	}
    	return $sql;
    }

    function getAlterTable() {
    	$criteria= new CriteriaCompo(new Criteria('required', '_YES'));
    	$criteria->add(new Criteria('primarykey', '_NO'));
    	$count = $this->_fHandler->getCount($criteria);
    	$values = array();
    	foreach ($this->_fHandler->getObjects($criteria, true) as $fid => $field) {
   			$values[$field->getVar('name')] = $field->getVar('alter_sql');
    	}
	   	foreach ($this->getVar('fids') as $name => $field) {
    		$fld = $this->_fHandler->get($field['fid']);
    		$values[$name] = $fld->getVar('alter_sql');
    	}
    	$sql = array();
    	foreach($values as $fldname => $value) {
    		$sql['alter'][$this->getVar('tbid')][$fldname] = 'ALTER TABLE `'.$GLOBALS['xoopsDB']->prefix($this->getVar('name'))."` CHANGE COLUMN `".fieldname.'` `'.fieldname.'` '.$value;
    	}
    	return $sql;
    }
    
    function getRenameTable() {
    	$newname = $this->getVar('name')."_".substr(md5(microtime(true)),mt_rand(0,25),6);
    	$sql = array();
    	$sql['rename'][$this->getVar('tbid')][$newname] = 'RENAME TABLE `'.$GLOBALS['xoopsDB']->prefix($this->getVar('name'))."` TO `".$GLOBALS['xoopsDB']->prefix($newname)."`";
    	return $sql;
    }

    function getDropTable() {
    	$sql = array();
    	$sql['drop'][$this->getVar('tbid')][$this->getVar('name')] = 'DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($this->getVar('name'))."`";
    	return $sql;
    }

    function getTruncateTable() {
    	$sql = array();
    	$sql['truncate'][$this->getVar('tbid')][$this->getVar('name')] = 'TRUNCATE `'.$GLOBALS['xoopsDB']->prefix($this->getVar('name'))."`";
    	return $sql;
    }
    
}


/**
* XOOPS Benchmarking handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS user class objects.
*
* @author  Simon Roberts <simon@chronolabs.coop>
* @package kernel
*/
class BenchmarkTablesHandler extends XoopsPersistableObjectHandler
{
	var $_fHandler;
		
    function __construct(&$db) 
    {
        parent::__construct($db, "benchmark_tables", 'BenchmarkTables', "tbid", "name");
        $this->_fHandler = xoops_getmodulehandler('fields', 'benchmark');
    }
    
    function designTable($tid, $test, $fields = 18, $engine = 'INNODB', $charset = 'utf8') {
		// Gets Table Index
    	$criteria = new CriteriaCompo(new Criteria('tid', $tid));
    	$criteria->add(new Criteria('test', $test));
    	$criteria->add(new Criteria('engine', $engine));
    	$index = $this->getCount($criteria)+1;
    	// Gets Table Name
    	$name = strtolower($GLOBALS['xoopsModuleConfig']['table_name'].(strlen($GLOBALS['xoopsModuleConfig']['table_name'])>0?"_":"").'_'.$tid.'_'.constant($test.'_TABLENAME').'_'.$engine.'_'.$index);
    	// Gets Fields Array
    	$fids = array();
	    switch($GLOBALS['xoopsModuleConfig']['spectrum']) {
	    	default:
	    	case 'random':
			    $criteria = new CriteriaCompo(new Criteria('type', "('".implode("','",$GLOBALS['xoopsModuleConfig']['field_types'])."')", 'IN'));
			   	$criteria->add(new Criteria('required', '_NO'));
		    	$criteria->setSort('RAND()');
		    	$criteria->setLimit($fields);
		    	while (count($fids)<$fields) {	    	
		    		foreach ($this->_fHandler->getObjects($criteria, true) as $fid => $field) {
		    			$fieldname = strtolower($field->getVar('name').'_'.(sizeof($fids)+1).'_'.$index.'_'.$tid);
		    			if (count($fids)<$fields) {
		    				$fids[$fieldname] = array('name' => $fieldname, 'type' => $field->getVar('type'), 'size' => $field->getVar('size'), 'altered_type' => $field->getVar('altered_type'), 'fid' => $fid);
		    			}
		    		}
		    	}
	    		break;
	    	case 'each':
	    		$criteria = new CriteriaCompo(new Criteria('type', "('".implode("','",$GLOBALS['xoopsModuleConfig']['field_types'])."')", 'IN'));
			   	$criteria->add(new Criteria('required', '_NO'));
		    	$criteria->setSort('RAND()');
	    		foreach ($this->_fHandler->getObjects($criteria, true) as $fid => $field) {
	    			$fieldname = strtolower($field->getVar('name').'_'.(sizeof($fids)+1).'_'.$index.'_'.$tid);
	    			$fids[$fieldname] = array('name' => $fieldname, 'type' => $field->getVar('type'), 'size' => $field->getVar('size'), 'altered_type' => $field->getVar('altered_type'), 'fid' => $fid);
	    		}
	    	
	    }
	    
	    $object = $this->create();
	    $object->setVars('tid', $tid);
	    $object->setVars('fids', $fids);
	    $object->setVars('test', $test);
	    $object->setVars('engine', $engine);
	    $object->setVars('charset', $charset);
	    $object->setVars('index', $index);
	    $object->setVars('name', $name);
	    $object->setVars('fields', count($fids));
	    
    	return $this->get($this->insert($object, true));
    }
}
?>