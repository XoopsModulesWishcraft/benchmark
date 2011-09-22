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
class BenchmarkFields extends XoopsObject
{

    function BenchmarkFields($fid = null)
    {
        $this->initVar('fid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 128);
        $this->initVar('type', XOBJ_DTYPE_ENUM, null, false, false, false, array('bigint','mediumint','smallint','int','tinyint','float','decimal','double','enum_varchar','enum_int','varchar','mediumtext','text','date','time','datetime','year'));
        $this->initVar('required', XOBJ_DTYPE_ENUM, '_NO', false, false, false, array('_YES','_NO'));
        $this->initVar('primarykey', XOBJ_DTYPE_ENUM, '_NO', false, false, false, array('_YES','_NO'));
        $this->initVar('key', XOBJ_DTYPE_TXTBOX, null, false, 35);
        $this->initVar('create_sql', XOBJ_DTYPE_TXTBOX, null, false, 128);
        $this->initVar('alter_sql', XOBJ_DTYPE_TXTBOX, null, false, 128);
        $this->initVar('size', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('recipricol', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('alter_type', XOBJ_DTYPE_ENUM, null, false, false, false, array('bigint','mediumint','smallint','int','tinyint','float','decimal','double','enum_varchar','enum_int','varchar','mediumtext','text','date','time','datetime','year'));
        $this->initVar('created', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('updated', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('actioned', XOBJ_DTYPE_INT, 0, false);		
    }
    
    function getForm() {
    	return benchmark_field_get_form($this);
    }
    
	function runInsertPlugin() {
		
		xoops_loadLanguage('plugins', $GLOBALS['xoopsModule']->getVar('dirname'));
		
		include_once($GLOBALS['xoops']->path('/modules/benchmark/plugins/'.$this->getVar('type').'.php'));
		
		switch ($this->getVar('type')) {
			case 'bigint':
			case 'mediumint':
			case 'smallint':
			case 'int':
			case 'tinyint':
			case 'float':
			case 'decimal':
			case 'double':
			case 'enum_varchar':
			case 'enum_int':
			case 'varchar':
			case 'mediumtext':
			case 'text':
			case 'date':
			case 'time':
			case 'datetime':
			case 'year':
				$func = ucfirst($this->getVar('type')).ucfirst(constant($this->getVar('required').'_PLUGIN')).ucfirst(constant($this->getVar('primarykey').'_PLUGIN')).'InsertHook';
				break;
			default:
				return $this->getVar('fid');
				break;
		}
		
		if (function_exists($func))  {
			return @$func($this);
		}
		return $this->getVar('fid');
	}
	
	function runGetPlugin() {
		
		xoops_loadLanguage('plugins', $GLOBALS['xoopsModule']->getVar('dirname'));
		
		include_once($GLOBALS['xoops']->path('/modules/benchmark/plugins/'.$this->getVar('type').'.php'));
		
		switch ($this->getVar('type')) {
			case 'bigint':
			case 'mediumint':
			case 'smallint':
			case 'int':
			case 'tinyint':
			case 'float':
			case 'decimal':
			case 'double':
			case 'enum_varchar':
			case 'enum_int':
			case 'varchar':
			case 'mediumtext':
			case 'text':
			case 'date':
			case 'time':
			case 'datetime':
			case 'year':
				$func = ucfirst($this->getVar('type')).ucfirst(constant($this->getVar('required').'_PLUGIN')).ucfirst(constant($this->getVar('primarykey').'_PLUGIN')).'GetHook';
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
    
	function toArray() {
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

		$id = $this->getVar('tid');
		if (empty($id)) $id = '0';
		
		$ele = array();	
		$ele['id'] = new XoopsFormHidden('id', $id);
		
		$ele['name'] = new XoopsFormText('', $id.'[name]', 15,128, $this->getVar('name'));
		$ele['create_sql'] = new XoopsFormText('', $id.'[create_sql]', 15,128, $this->getVar('create_sql'));
		$ele['type'] = new BenchmarkFormSelectFieldType('', $id.'[type]', $this->getVar('type'));
		$ele['required'] = new BenchmarkFormSelectYN('', $id.'[required]', $this->getVar('required'));
		$ele['primarykey'] = new BenchmarkFormSelectYN('', $id.'[primarykey]', $this->getVar('primarykey'));
		$ele['key'] = new XoopsFormText('', $id.'[key]', 10,35, $this->getVar('key'));
		$ele['alter_sql'] = new XoopsFormText('', $id.'[alter_sql]', 15,128, $this->getVar('alter_sql'));
		$ele['alter_type'] = new BenchmarkFormSelectFieldType('', $id.'[alter_type]', $this->getVar('alter_type'));
		$ele['size'] = new XoopsFormText('', $id.'[size]', 7,20, $this->getVar('size'));
		$ele['recipricol'] = new XoopsFormText('', $id.'[recipricol]', 7,20, $this->getVar('recipricol'));
		if ($this->getVar('created')>0) {
			$ele['created'] = new XoopsFormLabel('', date(_DATESTRING, $this->getVar('created')));
		}
		if ($this->getVar('updated')>0) {
			$ele['updated'] = new XoopsFormLabel('', date(_DATESTRING, $this->getVar('updated')));
		}
		if ($this->getVar('actioned')>0) {
			$ele['actioned'] = new XoopsFormLabel('', date(_DATESTRING, $this->getVar('actioned')));
		}	
		
		
		foreach($ele as $id => $obj)
			$ret['form'][$id] = $ele[$id]->render();			

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
class BenchmarkFieldsHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
        parent::__construct($db, "benchmark_fields", 'BenchmarkFields', "fid", "name");
    }
    
    function insert($obj, $force=true, $run_plugin = false) {
    	$this->recalc();
    	if ($obj->isNew()) {
    		$obj->setVar('created', time());
    	} else {
    		$obj->setVar('updated', time());
    	}
    	if ($obj->vars['type']['changed']==true ||
    		$obj->vars['required']['changed']==true ||
    		$obj->vars['primarykey']['changed']==true ||
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