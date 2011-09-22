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
class BenchmarkTests extends XoopsObject
{

	var $_rHandler = NULL;
	var $_tHandler = NULL;
	var $_tbHandler = NULL;
	
    function __construct()
    {
    	
    	$this->_rHandler = $results_handler = xoops_getmodulehandler('results', $GLOBALS['xoopsModule']->getVar('dirname'));
    	$this->_tHandler = $results_handler = xoops_getmodulehandler('tests', $GLOBALS['xoopsModule']->getVar('dirname'));
    	$this->_tbHandler = $results_handler = xoops_getmodulehandler('tables', $GLOBALS['xoopsModule']->getVar('dirname'));
    	
        $this->initVar('tid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('stage', XOBJ_DTYPE_ENUM, '_MI_BENCHMARK_WAIT', false, false, false, array('_MI_BENCHMARK_WAIT','_MI_BENCHMARK_PREPARED','_MI_BENCHMARK_TESTING','_MI_BENCHMARK_CLEANUP','_MI_BENCHMARK_FINISHED'));
        $this->initVar('test', XOBJ_DTYPE_ENUM, '_MI_BENCHMARK_WAIT', false, false, false, array('_MI_BENCHMARK_WAIT','_MI_BENCHMARK_CREATE','_MI_BENCHMARK_SELECT','_MI_BENCHMARK_INSERT','_MI_BENCHMARK_UPDATE','_MI_BENCHMARK_UPDATEALL','_MI_BENCHMARK_DELETE','_MI_BENCHMARK_DELETEALL','_MI_BENCHMARK_ALTER','_MI_BENCHMARK_RENAME','_MI_BENCHMARK_SMARTY','_MI_BENCHMARK_FINISHED'));
        $this->initVar('name', XOBJ_DTYPE_VARCHAR, null, false, 128);
        $this->initVar('platform', XOBJ_DTYPE_ENUM, '_MI_BENCHMARK_XOOPS25', false, false, false, array('_MI_BENCHMARK_XOOPS24','_MI_BENCHMARK_XOOPS25','_MI_BENCHMARK_XOOPS26','_MI_BENCHMARK_XOOPS27','_MI_BENCHMARK_XOOPS28','_MI_BENCHMARK_XOOPS29','_MI_BENCHMARK_XOOPS30','_MI_BENCHMARK_XOOPS31','_MI_BENCHMARK_XOOPSCUBE','_MI_BENCHMARK_ICMS'));
        $this->initVar('testing', XOBJ_DTYPE_TXTBOX, null, false, 128);
        $this->initVar('note', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('tbids_create', XOBJ_DTYPE_ARRAY, array(), false);		
        $this->initVar('tbids_select', XOBJ_DTYPE_ARRAY, array(), false);
        $this->initVar('tbids_insert', XOBJ_DTYPE_ARRAY, array(), false);
        $this->initVar('tbids_update', XOBJ_DTYPE_ARRAY, array(), false);
        $this->initVar('tbids_updateall', XOBJ_DTYPE_ARRAY, array(), false);
        $this->initVar('tbids_delete', XOBJ_DTYPE_ARRAY, array(), false);
        $this->initVar('tbids_deleteall', XOBJ_DTYPE_ARRAY, array(), false);
        $this->initVar('tbids_alter', XOBJ_DTYPE_ARRAY, array(), false);
        $this->initVar('tbids_rename', XOBJ_DTYPE_ARRAY, array(), false);
        $this->initVar('tbids_smarty', XOBJ_DTYPE_ARRAY, array(), false);
        $this->initVar('min_create_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_select_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_insert_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_update_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_updateall_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_delete_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_deleteall_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_alter_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_rename_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_smarty_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_common_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_boot_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_create_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_select_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_insert_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_update_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_updateall_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_delete_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_deleteall_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_alter_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_rename_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_smarty_time', XOBJ_DTYPE_DECIMAL, 0, false);        
        $this->initVar('max_common_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_boot_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_create_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_select_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_insert_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_update_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_updateall_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_delete_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_deleteall_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_alter_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_rename_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_smarty_time', XOBJ_DTYPE_DECIMAL, 0, false);
		$this->initVar('avg_create_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_select_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_insert_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_update_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_updateall_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_delete_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_deleteall_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_alter_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_rename_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_smarty_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_create_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_select_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_insert_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_update_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_updateall_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_delete_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_deleteall_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_alter_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_rename_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_smarty_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_create_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_select_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_insert_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_update_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_updateall_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_delete_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_deleteall_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_alter_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_rename_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_smarty_data_length', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_common_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_boot_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_create_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_select_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_insert_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_update_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_updateall_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_delete_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_deleteall_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_alter_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_rename_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_smarty_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_create_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_select_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_insert_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_update_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_updateall_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_delete_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_deleteall_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_alter_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_rename_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_smarty_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_create_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_select_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_insert_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_update_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_updateall_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_delete_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_deleteall_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_alter_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_rename_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_smarty_fields', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_create_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_select_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_insert_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_update_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_updateall_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_delete_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_deleteall_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_alter_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_rename_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('avg_smarty_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_create_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_select_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_insert_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_update_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_updateall_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_delete_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_deleteall_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_alter_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_rename_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('max_smarty_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_create_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_select_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_insert_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_update_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_updateall_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_delete_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_deleteall_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_alter_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_rename_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_smarty_records', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_common_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('min_boot_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('took_create_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('took_select_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('took_insert_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('took_update_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('took_updateall_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('took_delete_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('took_deleteall_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('took_alter_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('took_rename_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('took_smarty_time', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('took_common_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('took_boot_seconds', XOBJ_DTYPE_DECIMAL, 0, false);
        $this->initVar('tests_ran_create', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tests_ran_select', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tests_ran_insert', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tests_ran_update', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tests_ran_updateall', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tests_ran_delete', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tests_ran_deleteall', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tests_ran_alter', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tests_ran_rename', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tests_ran_smarty', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('begin', XOBJ_DTYPE_INT, time()+$GLOBALS['xoopsModuleConfig']['step_new_begin'], false);
        $this->initVar('started', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('ended', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('created', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('updated', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('actioned', XOBJ_DTYPE_INT, 0, false);
        
    }

    function getHistory() {
    	$this->setVars($this->_rHandler->getMinMaxAvgSumValues($this));
    	$this->setVar('ended', time());
    	return $this->_tHandler->insert($this, true);
    }
    
    function getForm() {
    	return benchmark_test_get_form($this);
    }
    
	function runInsertPlugin() {
		
		xoops_loadLanguage('plugins', $GLOBALS['xoopsModule']->getVar('dirname'));
		
		include_once($GLOBALS['xoops']->path('/modules/benchmark/plugins/'.constant($this->getVar('stage').'_PLUGINFILE').'.php'));

		switch ($this->getVar('test')) {
			case '_MI_BENCHMARK_WAIT':
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
			case '_MI_BENCHMARK_FINISHED':
				switch ($this->getVar('stage')) {
					case '_MI_BENCHMARK_WAIT':
					case '_MI_BENCHMARK_PREPARED':
					case '_MI_BENCHMARK_TESTING':
					case '_MI_BENCHMARK_CLEANUP':
					case '_MI_BENCHMARK_FINISHED':
						$func = ucfirst(constant($this->getVar('test').'_PLUGIN')).ucfirst(constant($this->getVar('stage').'_PLUGIN')).'TestsInsertHook';
						break;
					default:
						return $this->getVar('tid');
						break;
				}
				break;
			default:
				return $this->getVar('tid');
				break;
		}
		
		if (function_exists($func))  {
			return @$func($this);
		}
		return $this->getVar('tid');
	}
	
	function runGetPlugin() {
		
		xoops_loadLanguage('plugins', $GLOBALS['xoopsModule']->getVar('dirname'));
		
		include_once($GLOBALS['xoops']->path('/modules/benchmark/plugins/'.constant($this->getVar('stage').'_PLUGINFILE').'.php'));
		
		switch ($this->getVar('test')) {
			case '_MI_BENCHMARK_WAIT':
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
			case '_MI_BENCHMARK_FINISHED':
				switch ($this->getVar('stage')) {
					case '_MI_BENCHMARK_WAIT':
					case '_MI_BENCHMARK_PREPARED':
					case '_MI_BENCHMARK_TESTING':
					case '_MI_BENCHMARK_CLEANUP':
					case '_MI_BENCHMARK_FINISHED':
						$func = ucfirst(constant($this->getVar('test').'_PLUGIN')).ucfirst(constant($this->getVar('stage').'_PLUGIN')).'TestsGetHook';
						break;
					default:
						return $this;
						break;
				}
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
		
		if ($this->getVar('begin')>0)
			$ret['dates']['begin'] = date(_DATESTRING, $this->getVar('begin'));
		if ($this->getVar('started')>0)
			$ret['dates']['started'] = date(_DATESTRING, $this->getVar('started'));
		if ($this->getVar('ended')>0)
			$ret['dates']['ended'] = date(_DATESTRING, $this->getVar('ended'));
		if ($this->getVar('created')>0)
			$ret['dates']['created'] = date(_DATESTRING, $this->getVar('created'));
		if ($this->getVar('updated')>0)
			$ret['dates']['updated'] = date(_DATESTRING, $this->getVar('updated'));
		if ($this->getVar('actioned')>0)
			$ret['dates']['actioned'] = date(_DATESTRING, $this->getVar('actioned'));

		if ($simple==true)
			return $ret;
			
		$id = $this->getVar('tid');
		if (empty($id)) $id = '0';
		
		$ele = array();	
		$ele['op'] = new XoopsFormHidden('op', 'tests');
		$ele['fct'] = new XoopsFormHidden('fct', 'save');
		$ele['id'] = new XoopsFormHidden('id', $id);
		
		$ele['name'] = new XoopsFormText('', $id.'[name]', 15,128, $this->getVar('name'));
		$ele['testing'] = new XoopsFormText('', $id.'[testing]', 15,128, $this->getVar('testing'));
		$note_configs = array();
		$note_configs['name'] = 'note';
		$note_configs['value'] = $this->getVar('note');
		$note_configs['rows'] = 35;
		$note_configs['cols'] = 60;
		$note_configs['width'] = "100%";
		$note_configs['height'] = "100px";
		$note_configs['editor'] = $GLOBALS['xoopsModuleConfig']['editor'];
		$ele['note'] = new XoopsFormEditor('', $note_configs['name'], $note_configs);
		$ele['platform'] = new BenchmarkFormSelectPlatform('', $id.'[platform]', $this->getVar('platform'));
		if ($this->getVar('started')>0)
			$ele['platform']->setExtra("disabled='disabled'");
		$ele['begin'] = new XoopsFormDateTime('', $id.'[begin]', 15, $this->getVar('begin'));
    	if ($this->getVar('started')>0)
			$ele['begin']->setExtra("disabled='disabled'");
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
				
		$myts = MyTextSanitizer::getInstance();
		$ret['note'] = $myts->displayTarea($this->getVar('note'), true);
	
		foreach(array('create','select','insert','update','updateall','delete','deleteall','alter','rename','smarty') as $mode) {
			if (count($this->getVar('tbids_'.$mode))>0) {
				$tables = $this->_tbHandler->getObjects(new Criteria('tbid', '('.implode(',', $this->getVar('tbids_'.$mode)).')', 'IN'), true);
				foreach($tables as $tbid => $table)
					$ret['tables'][$mode][$tbid] = $table->toArray();
			}
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
class BenchmarkTestsHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
        parent::__construct($db, "benchmark_tests", 'BenchmarkTests', "tid", "name");
    }
    
    function insert($obj, $force=true, $run_plugin = false) {
    	if ($obj->isNew()) {
    		$obj->setVar('created', time());
    	} else {
    		$obj->setVar('updated', time());
    	}
	   	if ($obj->vars['stage']['changed']==true ||
    		$obj->vars['test']['changed']==true ||
    		$obj->vars['platform']['changed']==true ||
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