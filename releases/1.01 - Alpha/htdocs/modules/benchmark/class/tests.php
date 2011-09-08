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
	
    function __construct()
    {
    	
    	$this->_rHandler = $results_handler = xoops_getmodulehandler('results', $GLOBALS['xoopsModule']->getVar('dirname'));
    	$this->_tHandler = $results_handler = xoops_getmodulehandler('tests', $GLOBALS['xoopsModule']->getVar('dirname'));
    	
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
        $this->initVar('begin', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('started', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('ended', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('created', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('updated', XOBJ_DTYPE_INT, 0, false);
        
    }

    function getHistory() {
    	$this->setVars($this->_rHandler->getMinMaxAvgSumValues($this));
    	$this->setVar('ended', time());
    	return $this->_tHandler->insert($this, true);
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
}
?>