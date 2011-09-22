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
	var $_fHandler=NULL;
	var $_tHandler=NULL;
	
    function __construct()
    {
    	$this->_fHandler = xoops_getmodulehandler('fields', $GLOBALS['xoopsModule']->getVar('dirname'));
    	$this->_tHandler = xoops_getmodulehandler('tests', $GLOBALS['xoopsModule']->getVar('dirname'));
    	
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
        $this->initVar('created', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('updated', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('actioned', XOBJ_DTYPE_INT, 0, false);        
    }
    
    function getCreateTable() {
    	$numbersassymbols = "'".implode("','", benchmark_getNumericEnumeration())."'";
    	$numbersaswords = "'".implode("','", benchmark_getVarcharEnumeration())."'";
    	$sql = 'CREATE TABLE `'.$GLOBALS['xoopsDB']->prefix($this->getVar('name'))."` (\n";
    	$criteria= new CriteriaCompo(new Criteria('required', '_YES'));
    	$count = $this->_fHandler->getCount($criteria);
    	$fldindex=array();
    	foreach ($this->_fHandler->getObjects($criteria, true) as $fid => $field) {
    		$i++;
    		$sql .= '`'.$field->getVar('name').'` '.str_replace('%numbersaswords%', $numbersaswords, str_replace('%numbersassymbols%', $numbersassymbols, $field->getVar('create_sql'))).($count>$i||count($this->getVar('fids'))>0?", ":"");
    		$fldindex[$fid] = 	$field->getVar('name');
    	}
    	$criteria= new CriteriaCompo(new Criteria('required', '_YES'));
    	$criteria->add(new Criteria('primarykey', '_YES'));
    	$primarykey = $this->_fHandler->getCount($criteria);
    	$criteria= new CriteriaCompo(new Criteria('`key`', '', '!='));
    	$keys = $this->_fHandler->getCount($criteria);
    	$count = count($this->getVar('fids'));
    	$i=0;
    	foreach (array_keys($this->getVar('fids')) as $name) {
    		$i++;
    		$exploded = explode('_', $name);
    		if (is_numeric($exploded[1]))
    			$fid = $exploded[1];
    		else 
    			$fid = $exploded[2];
    		$fldindex[$fid] = $name;
    		$fld = $this->_fHandler->get($fid);
    		$sql .= '`'.$name.'` '.str_replace('%numbersaswords%', $numbersaswords, str_replace('%numbersassymbols%', $numbersassymbols, $fld->getVar('create_sql'))).($count>$i||$primarykey!=0||$keys!=0?", ":""); 	
    	}
    	if ($primarykey>0) {
    		$criteria= new CriteriaCompo(new Criteria('required', '_YES'));
    		$criteria->add(new Criteria('primarykey', '_YES'));
    		$key = $this->_fHandler->getObjects($criteria, false);
    		if (is_object($key[0]))
    			$sql .= 'PRIMARY KEY (`'.$key[0]->getVar('name').'`)'.($keys!=0?", ":"");
    	}
    	if ($keys>0) {
    		$criteria= new CriteriaCompo(new Criteria('`key`', '', '!='));
    		$criteria->setSort('`key`');
    		$criteria->setOrder('ASC');
    		foreach($this->_fHandler->getObjects($criteria, true) as $fid => $key) {
    			$key = $key->getVar('key');
    			if (!empty($key))
    				$tmp[$key][$fid] = $fldindex[$fid]; 
    		}
    		$i=0;
    		$count = count($tmp);
    		foreach($tmp as $keyname => $keyfields) {
    			$i++;
    			if (!empty($keyname))
    				$sql .= 'KEY `'.$keyname.'` (`'.implode('`,`', $keyfields).'`)'.($count>$i?", ":"");
    		}
    	}
    	$sql .= ") ENGINE=".$this->getVar('engine').(strlen($this->getVar('charset'))>0?" DEFAULT CHARSET=".$this->getVar('charset'):"");
    	@$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($this->getVar('name')).'`');
    	return array('create'=>array($this->getVar('tbid')=>array($this->getVar('name')=>$sql)));
    }

    function getInsertArray($number=1) {
    	static $fld = array();
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
	    	foreach (array_keys($this->getVar('fids')) as $name) {
	    		$i++;
	    		$exploded = explode('_', $name);
	    		if (is_numeric($exploded[1]))
	    			$fid = $exploded[1];
	    		else 
	    			$fid = $exploded[2];
		    	if (!isset($fld[$fid]))
	    			$fld[$fid] = $this->_fHandler->get($fid);

	    		$values[$name] = (benchmark_getFillValue($fld[$fid], $this));
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
		static $fld = array();
		
    	$sql = array();
    	for($c=0;$c<=$number;$c++) {
		   	foreach (array_keys($this->getVar('fids')) as $name) {
	    		$i++;
	    		$exploded = explode('_', $name);
	    		if (is_numeric($exploded[1]))
	    			$fid = $exploded[1];
	    		else 
	    			$fid = $exploded[2];
		    	if (!isset($fld[$fid]))
	    			$fld[$fid] = $this->_fHandler->get($fid);
	    		$values[$name] = benchmark_getFillValue($fld[$fid], $this);
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
    	static $fld = array();
    	$sql = array();
		foreach (array_keys($this->getVar('fids')) as $name) {
	    	$i++;
	    	$exploded = explode('_', $name);
	    	if (is_numeric($exploded[1]))
	    		$fid = $exploded[1];
	    	else 
	    		$fid = $exploded[2];
		   	if (!isset($fld[$fid]))
	    		$fld[$fid] = $this->_fHandler->get($fid);
    	   	$values[$name] = benchmark_getFillValue($fld[$fid], $this);
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
        if ($GLOBALS['xoopsModuleConfig']['records_random']) {
    		$number = $number + mt_rand($GLOBALS['xoopsModuleConfig']['records_random_minimum'], $GLOBALS['xoopsModuleConfig']['records_random_maximum']); 
    	}
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
	    	foreach (array_keys($this->getVar('fids')) as $name) {
	    		$i++;
	    		$exploded = explode('_', $name);
	    		if (is_numeric($exploded[1]))
	    			$fid = $exploded[1];
	    		else 
	    			$fid = $exploded[2];
	    		$fld = $this->_fHandler->get($fid);
	    		if (is_object($fld))
	    			$values[$name] = $GLOBALS['xoopsDB']->quote(benchmark_getFillValue($fld, $this));
	    	}
	    	$i=0;
	    	$count=count($values);
	    	foreach($values as $fldname => $value) {
	    		$i++;
	    		$out_fields .= "`".$fldname."`".($count>$i?', ':'');
	    		$out_value .= $value.($count>$i?', ':'');
	    		unset ($values[$fldname]);
	    	}
	      	$sql['insert'][$this->getVar('tbid')][$c] = 'INSERT INTO `'.$GLOBALS['xoopsDB']->prefix($this->getVar('name'))."` (".$out_fields.') VALUES ('.$out_value.')';
    	}
    	return $sql;
    }

    function getAlterTable() {
    	$numbersassymbols = "'".implode("','", benchmark_getNumericEnumeration())."'";
    	$numbersaswords = "'".implode("','", benchmark_getVarcharEnumeration())."'";
    	
    	$criteria= new CriteriaCompo(new Criteria('required', '_YES'));
    	$criteria->add(new Criteria('primarykey', '_NO'));
    	$count = $this->_fHandler->getCount($criteria);
    	$values = array();
    	foreach ($this->_fHandler->getObjects($criteria, true) as $fid => $field) {
   			$values[$field->getVar('name')] = str_replace('%numbersaswords%', $numbersaswords, str_replace('%numbersassymbols%', $numbersassymbols, $field->getVar('alter_sql')));
    	}
	   	foreach ($this->getVar('fids') as $name => $field) {
    		$fld = $this->_fHandler->get($field['fid']);
    		$values[$name] = str_replace('%numbersaswords%', $numbersaswords, str_replace('%numbersassymbols%', $numbersassymbols, $fld->getVar('alter_sql')));
    	}
    	$sql = array();
    	foreach($values as $fldname => $value) {
    		$sql['alter'][$this->getVar('tbid')][$fldname] = 'ALTER TABLE `'.$GLOBALS['xoopsDB']->prefix($this->getVar('name'))."` CHANGE COLUMN `".$fldname.'` `'.$fldname.'` '.$value;
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
    
	function runInsertPlugin() {
		
		xoops_loadLanguage('plugins', $GLOBALS['xoopsModule']->getVar('dirname'));
		
		include_once($GLOBALS['xoops']->path('/modules/benchmark/plugins/'.constant($this->getVar('test').'_PLUGINFILE').'.php'));
		
		switch ($this->getVar('test')) {
			case '_MI_BENCHMARK_CREATE':
			case '_MI_BENCHMARK_SELECT':
			case '_MI_BENCHMARK_INSERT':
			case '_MI_BENCHMARK_UPDATE':
			case '_MI_BENCHMARK_UPDATEALL':
			case '_MI_BENCHMARK_DELETE':
			case '_MI_BENCHMARK_DELETEALL':
			case '_MI_BENCHMARK_ALTER':
			case '_MI_BENCHMARK_RENAME':
			case '_MI_BENCHMARK_SMARTY':
				$func = ucfirst(constant($this->getVar('test').'_PLUGIN')).ucfirst(constant($this->getVar('engine').'_PLUGIN')).ucfirst(constant($this->getVar('altered').'_PLUGIN')).'TableInsertHook';
				break;
			default:
				return $this->getVar('tbid');
				break;
		}
		
		if (function_exists($func))  {
			return @$func($this);
		}
		return $this->getVar('tbid');
	}
	
	function runGetPlugin() {
		
		xoops_loadLanguage('plugins', $GLOBALS['xoopsModule']->getVar('dirname'));
		
		include_once($GLOBALS['xoops']->path('/modules/benchmark/plugins/'.constant($this->getVar('test').'_PLUGINFILE').'.php'));
		
		switch ($this->getVar('test')) {
			case '_MI_BENCHMARK_CREATE':
			case '_MI_BENCHMARK_SELECT':
			case '_MI_BENCHMARK_INSERT':
			case '_MI_BENCHMARK_UPDATE':
			case '_MI_BENCHMARK_UPDATEALL':
			case '_MI_BENCHMARK_DELETE':
			case '_MI_BENCHMARK_DELETEALL':
			case '_MI_BENCHMARK_ALTER':
			case '_MI_BENCHMARK_RENAME':
			case '_MI_BENCHMARK_SMARTY':
				$func = ucfirst(constant($this->getVar('test').'_PLUGIN')).ucfirst(constant($this->getVar('engine').'_PLUGIN')).ucfirst(constant($this->getVar('altered').'_PLUGIN')).'TableGetHook';
				break;
			default:
				return $this;
				break;
		}
		
		if (function_exists($func))  {
			return @$func($this);
		}
		return $this;
	}
	
	function toArray($simple=false) {
		$ret = parent::toArray();
		foreach($ret as $field => $value) {
			if(defined($value))
				$ret[$field] = constant($value);
		}
		if ($this->getVar('created')>0)
			$ret['dates']['created'] = date(_DATESTRING, $this->getVar('created'));
		if ($this->getVar('updated')>0)
			$ret['dates']['updated'] = date(_DATESTRING, $this->getVar('updated'));
		if ($this->getVar('actioned')>0)
			$ret['dates']['actioned'] = date(_DATESTRING, $this->getVar('actioned'));
		
		if ($simple==true)
			return $ret;
			
		if (count($this->getVar('fids'))<>0) {
			$fields = $this->_fHandler->getObjects(new Criteria('fib', '('.implode(',', $this->getVar('fids')).')', 'IN'), true);
			foreach($fields as $fid => $field)
				$ret['fields'][$fid] = $field->toArray();
		}
		
		if ($this->getVar('tid')<>0) {
			$test = $this->_tHandler->get($this->getVar('tid'));
			$ret['test'] = $test->toArray();
		}
		return $ret;
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
    	$name = strtolower($GLOBALS['xoopsModuleConfig']['table_name'].(strlen($GLOBALS['xoopsModuleConfig']['table_name'])>0?"_":"").$tid.'_'.constant($test.'_TABLENAME').'_'.$engine.'_'.$index);
    	// Gets Fields Array
    	if ($GLOBALS['xoopsModuleConfig']['fields_random']) {
    		$fields = $fields + mt_rand($GLOBALS['xoopsModuleConfig']['fields_random_minimum'], $GLOBALS['xoopsModuleConfig']['fields_random_maximum']); 
    	}
    	$fids = array();
	    switch($GLOBALS['xoopsModuleConfig']['spectrum']) {
	    	default:
	    	case 'random':
	    		$criteria = new CriteriaCompo(new Criteria('type', "('".implode("','",$GLOBALS['xoopsModuleConfig']['field_types'])."')", 'IN'));
			   	$criteria->add(new Criteria('required', '_NO'));
		    	$criteria->setSort('RAND()');	    		
		    	while (count($fids)<$fields) {	    	
		    		foreach ($this->_fHandler->getObjects($criteria, true) as $fid => $field) {
		    			if (count($fids)<$fields) {
			    			$fieldname = strtolower($field->getVar('name').'_'.$fid.'_'.(sizeof($fids)+1).'_'.$index.'_'.$tid);
			    			if (count($fids)<$fields) {
			    				$fids[$fieldname] = array('name' => $fieldname, 'type' => $field->getVar('type'), 'size' => $field->getVar('size'), 'altered_type' => $field->getVar('altered_type'), 'fid' => $fid);
			    			}
		    			}
		    		}
		    	}
	    		break;
	    	case 'each':
	    		$criteria = new CriteriaCompo(new Criteria('type', "('".implode("','",$GLOBALS['xoopsModuleConfig']['field_types'])."')", 'IN'));
			   	$criteria->add(new Criteria('required', '_NO'));
		    	$criteria->setSort('RAND()');
		    	while (count($fids)<$fields) {
		    		foreach ($this->_fHandler->getObjects($criteria, true) as $fid => $field) {
		    			if (count($fids)<$fields) {
			    			$fieldname = strtolower($field->getVar('name').'_'.$fid.'_'.(sizeof($fids)+1).'_'.$index.'_'.$tid);
			    			$fids[$fieldname] = array('name' => $fieldname, 'type' => $field->getVar('type'), 'size' => $field->getVar('size'), 'altered_type' => $field->getVar('altered_type'), 'fid' => $fid);
		    			}
		    		}
		    	}
				break;
	    }

	    $object = $this->create();
	    $object->setVar('tid', $tid);
	    $object->setVar('fids', $fids);
	    $object->setVar('test', $test);
	    $object->setVar('engine', $engine);
	    $object->setVar('charset', $charset);
	    $object->setVar('index', $index);
	    $object->setVar('name', $name);
	    $object->setVar('fields', count($fids));
    	return $this->get($this->insert($object, true));
    }
    
    function insert($obj, $force=true, $run_plugin = false) {
    	if ($obj->isNew()) {
    		$obj->setVar('created', time());
    	} else {
    		$obj->setVar('updated', time());
    	}
	   	if ($obj->vars['test']['changed']==true ||
    		$obj->vars['engine']['changed']==true ||
    		$obj->vars['altered']['changed']==true ||
    		$run_plugin == true ) {	
			$obj->setVar('actioned', time());
			$run_plugin = true;
		}
    	if ($run_plugin == true) {
    		$id = parent::insert($obj, $force);
    		$obj = parent::get($id);
    		if (is_object($obj)) {
	    		$ret = $obj->runInsertPlugin();
	    		return ($ret!=0)?$ret:$id;
    		} else {
    			return $id;
    		}
    	} else {
    		return parent::insert($obj, $force);
    	}
    }
    
    function get($id, $fields = '*', $run_plugin = true) {
    	$obj = parent::get($id, $fields);
    	if (is_object($obj)&&$run_plugin==true)
    		return @$obj->runGetPlugin(false);
    	else 
    		return $obj;
    }
    
    function getObjects($criteria, $id_as_key=false, $as_object=true, $run_plugin = true) {
       	$objs = parent::getObjects($criteria, $id_as_key, $as_object);
    	foreach($objs as $id => $obj) {
    		if (is_object($obj)&&$run_plugin==true)
    			$objs[$id] = @$obj->runGetPlugin();
    		if (empty($objs[$id]))
    			unset($objs[$id]);
    	}
    	return $objs;
    }
    
    function getFilterCriteria($filter) {
    	$parts = explode('|', $filter);
    	$criteria = new CriteriaCompo();
    	foreach($parts as $part) {
    		$var = explode(',', $part);
    		if (!empty($var[1])&&!is_numeric($var[0])) {
    			$object = $this->create();
    			if (		$object->vars[$var[0]]['data_type']==XOBJ_DTYPE_TXTBOX || 
    						$object->vars[$var[0]]['data_type']==XOBJ_DTYPE_TXTAREA) 	{
    				$criteria->add(new Criteria('`'.$var[0].'`', '%'.$var[1].'%', (isset($var[2])?$var[2]:'LIKE')));
    			} elseif (	$object->vars[$var[0]]['data_type']==XOBJ_DTYPE_INT || 
    						$object->vars[$var[0]]['data_type']==XOBJ_DTYPE_DECIMAL || 
    						$object->vars[$var[0]]['data_type']==XOBJ_DTYPE_FLOAT ) 	{
    				$criteria->add(new Criteria('`'.$var[0].'`', $var[1], (isset($var[2])?$var[2]:'=')));			
				} elseif (	$object->vars[$var[0]]['data_type']==XOBJ_DTYPE_ENUM ) 	{
    				$criteria->add(new Criteria('`'.$var[0].'`', $var[1], (isset($var[2])?$var[2]:'=')));    				
				} elseif (	$object->vars[$var[0]]['data_type']==XOBJ_DTYPE_ARRAY ) 	{
    				$criteria->add(new Criteria('`'.$var[0].'`', '%"'.$var[1].'";%', (isset($var[2])?$var[2]:'LIKE')));    				
				}
    		} elseif (!empty($var[1])&&is_numeric($var[0])) {
    			$criteria->add(new Criteria("'".$var[0]."'", $var[1]));
    		}
    	}
    	return $criteria;
    }
        
	function getFilterForm($filter, $field, $sort='created', $op = '', $fct = '') {
    	$ele = benchmark_getFilterElement($filter, $field, $sort, $op, $fct);
    	if (is_object($ele))
    		return $ele->render();
    	else 
    		return '&nbsp;';
    }
}
?>