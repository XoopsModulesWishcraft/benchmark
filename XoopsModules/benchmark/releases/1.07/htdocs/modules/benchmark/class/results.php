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
class BenchmarkResults extends XoopsObject
{

	var $_tbHandler = NULL;
	var $_tHandler = NULL;
	
    function __construct()
    {
    	$this->_tbHandler = xoops_getmodulehandler('tables', $GLOBALS['xoopsModule']->getVar('dirname'));
    	$this->_tHandler = xoops_getmodulehandler('tests', $GLOBALS['xoopsModule']->getVar('dirname'));
    	
        $this->initVar('rid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('tid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('tbid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('test', XOBJ_DTYPE_ENUM, null, false, false, false, array('_MI_BENCHMARK_CREATE','_MI_BENCHMARK_SELECT','_MI_BENCHMARK_INSERT','_MI_BENCHMARK_UPDATE','_MI_BENCHMARK_UPDATEALL','_MI_BENCHMARK_DELETE','_MI_BENCHMARK_DELETEALL','_MI_BENCHMARK_ALTER','_MI_BENCHMARK_RENAME','_MI_BENCHMARK_SMARTY'));
        $this->initVar('engine', XOBJ_DTYPE_ENUM, 'INNODB', false, false, false, array('INNODB','MYISAM'));
        $this->initVar('session', XOBJ_DTYPE_INT, null, false);
        $this->initVar('number', XOBJ_DTYPE_INT, null, false);
        $this->initVar('length', XOBJ_DTYPE_INT, null, false);
        $this->initVar('fields', XOBJ_DTYPE_INT, null, false);
        $this->initVar('records', XOBJ_DTYPE_INT, null, false);
        $this->initVar('started', XOBJ_DTYPE_DECIMAL, null, false);		
        $this->initVar('ended', XOBJ_DTYPE_DECIMAL, null, false);
        $this->initVar('difference', XOBJ_DTYPE_DECIMAL, null, false);
        $this->initVar('common_started', XOBJ_DTYPE_DECIMAL, null, false);
        $this->initVar('common_ended', XOBJ_DTYPE_DECIMAL, null, false);
        $this->initVar('common_difference', XOBJ_DTYPE_DECIMAL, null, false);
        $this->initVar('boot_started', XOBJ_DTYPE_DECIMAL, null, false);
        $this->initVar('boot_ended', XOBJ_DTYPE_DECIMAL, null, false);
        $this->initVar('boot_difference', XOBJ_DTYPE_DECIMAL, null, false);
        $this->initVar('created', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('updated', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('actioned', XOBJ_DTYPE_INT, 0, false);        
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
				$func = ucfirst(constant($this->getVar('test').'_PLUGIN')).ucfirst(constant($this->getVar('engine').'_PLUGIN')).'ResultInsertHook';
				break;
			default:
				return $this->getVar('rid');
				break;
		}
		
		if (function_exists($func))  {
			return @$func($this);
		}
		return $this->getVar('rid');
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
				$func = ucfirst(constant($this->getVar('test').'_PLUGIN')).ucfirst(constant($this->getVar('engine').'_PLUGIN')).'ResultGetHook';
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
			
		if ($this->getVar('tbid')<>0) {
			$table = $this->_tbHandler->get($this->getVar('tbid'));
			$ret['table'] = $table->toArray(true);
		}
		
		if ($this->getVar('tid')<>0) {
			$test = $this->_tHandler->get($this->getVar('tid'));
			$ret['testobj'] = $test->toArray(true);
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
class BenchmarkResultsHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
        parent::__construct($db, "benchmark_results", 'BenchmarkResults', "rid", "number");
    }
    
    function saveResults($test, $var, $tables, $session) {
    	foreach($var[$test->getVar('test')] as $id => $results) {
    		if ($id!=0) {
    			foreach($results['number'] as $tbid => $pointer) {
    				$result = $this->create(true);
		    		$result->setVar('tid', $test->getVar('tid'));
		    		$result->setVar('test', $test->getVar('test'));
		    		$result->setVar('tbid', $tbid);
		    		$result->setVar('session', $session);
		    		$result->setVar('engine', $tables[$tbid]->getVar('engine'));
		    		$result->setVar('number', $results['number'][$tbid]);
		    		$result->setVar('length', $results['length'][$tbid]);
		    		$result->setVar('fields', $tables[$tbid]->getVar('fields'));
		    		$result->setVar('records', $results['records'][$tbid]);
		    		$result->setVar('started', $results['timer'][$tbid]['start']);
		    		$result->setVar('ended', $results['timer'][$tbid]['end']);
		    		$result->setVar('difference', $results['timer'][$tbid]['end']-$results['timer'][$tbid]['start']);
		    		$result->setVar('common_started', $GLOBALS['timer']['common']['start']);
		    		$result->setVar('common_ended', $GLOBALS['timer']['common']['end']);
		    		$result->setVar('common_difference', $GLOBALS['timer']['common']['end']-$GLOBALS['timer']['common']['start']);
		    		$result->setVar('boot_started', $GLOBALS['timer']['boot']['start']);
		    		$result->setVar('boot_ended', $GLOBALS['timer']['boot']['end']);
		    		$result->setVar('boot_difference', $GLOBALS['timer']['boot']['end']-$GLOBALS['timer']['boot']['start']);
		    		$this->insert($result, true);
    			}
			}
    	}
    }
    
    function getMinMaxAvgSumValues($test) {
    	xoops_loadLanguage('modinfo', 'benchmark');
    	$sql = array();
    	switch ($test->getVar('test')){
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
    			$sql[0] = 'SELECT 	COUNT(*) as `test_results`, 
    							SUM(`difference`) as `' . constant($test->getVar('test').'_SUMDIFF') . '`, 
    							AVG(`difference`) as `' . constant($test->getVar('test').'_AVGDIFF') . '`, 
    							MAX(`difference`) as `' . constant($test->getVar('test').'_MAXDIFF') . '`, 
    							MIN(`difference`) as `' . constant($test->getVar('test').'_MINDIFF') . '`,
    							SUM(`length`) as `' . constant($test->getVar('test').'_SUMLEN') . '`, 
    							AVG(`length`) as `' . constant($test->getVar('test').'_AVGLEN') . '`, 
    							MAX(`length`) as `' . constant($test->getVar('test').'_MAXLEN') . '`, 
    							MIN(`length`) as `' . constant($test->getVar('test').'_MINLEN') . '`,
    							SUM(`fields`) as `' . constant($test->getVar('test').'_SUMFIELD') . '`, 
    							AVG(`fields`) as `' . constant($test->getVar('test').'_AVGFIELD') . '`, 
    							MAX(`fields`) as `' . constant($test->getVar('test').'_MAXFIELD') . '`, 
    							MIN(`fields`) as `' . constant($test->getVar('test').'_MINFIELD') . '`,
    							SUM(`records`) as `' . constant($test->getVar('test').'_SUMREC') . '`, 
    							AVG(`records`) as `' . constant($test->getVar('test').'_AVGREC') . '`, 
    							MAX(`records`) as `' . constant($test->getVar('test').'_MAXREC') . '`, 
    							MIN(`records`) as `' . constant($test->getVar('test').'_MINREC') . '`
    					FROM `' . $GLOBALS['xoopsDB']->prefix('benchmark_results') . '` 
    					WHERE `test` = "' . $test->getVar('test') . '" AND `tid` = "' . $test->getVar('tid') . '"';  
    			$sql[1] =  'SELECT 	COUNT(*) as `total_results`,    							break;
    							SUM(`common_difference`) as `' . constant($test->getVar('test').'_SUMCOMMON') . '`, 
    							AVG(`common_difference`) as `' . constant($test->getVar('test').'_AVGCOMMON') . '`, 
    							MAX(`common_difference`) as `' . constant($test->getVar('test').'_MAXCOMMON') . '`, 
    							MIN(`common_difference`) as `' . constant($test->getVar('test').'_MINCOMMON') . '`, 
    							SUM(`boot_difference`)  as `' . constant($test->getVar('test').'_SUMBOOT') . '`, 
    							AVG(`boot_difference`) as `' . constant($test->getVar('test').'_AVGCOMMON') . '`, 
    							MAX(`boot_difference`) as `' . constant($test->getVar('test').'_MAXCOMMON') . '`, 
    							MIN(`boot_difference`) as `' . constant($test->getVar('test').'_MINBOOT'). '`
    					FROM `' . $GLOBALS['xoopsDB']->prefix('benchmark_results') . '` 
    					WHERE `tid` = "' . $test->getVar('tid') . '"';
    			break;
    	}
    	$result='';
    	$ret = array();
    	foreach($sql as $id => $question) {
    		if ($result = $GLOBALS['xoopsDB']->queryF($question)) {
    			foreach($GLOBALS['xoopsDB']->fetchArray($result) as $field => $value) {
		    		$ret[$field] = $value;
		    	}		
    		}
    		
    	}
    	return $ret;
    }
    
    function insert($obj, $force=true, $run_plugin = false) {
    	if ($obj->isNew()) {
    		$obj->setVar('created', time());
    	} else {
    		$obj->setVar('updated', time());
    	}
	   	if ($obj->vars['test']['changed']==true ||
    		$obj->vars['engine']['changed']==true ||
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
    
    function getSum($field) {
    	if (empty($field))
    		return false;
    	$sql = 'SELECT SUM(`'.$field.'`) as CALC FROM `' . $GLOBALS['xoopsDB']->prefix('benchmark_results') . '`';
    	list($sum) = $GLOBALS['xoopsDB']->fetchRow($GLOBALS['xoopsDB']->queryF($sql));
    	return $sum;
    }
}
?>