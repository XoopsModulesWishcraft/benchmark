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

    function BenchmarkResults($fid = null)
    {
        $this->initVar('rid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('tid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('tbid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('test', XOBJ_DTYPE_ENUM, null, false, false, false, array('_MI_BENCHMARK_CREATE','_MI_BENCHMARK_SELECT','_MI_BENCHMARK_INSERT','_MI_BENCHMARK_UPDATE','_MI_BENCHMARK_UPDATEALL','_MI_BENCHMARK_DELETE','_MI_BENCHMARK_DELETEALL','_MI_BENCHMARK_ALTER','_MI_BENCHMARK_RENAME','_MI_BENCHMARK_SMARTY'));
        $this->initVar('engine', XOBJ_DTYPE_ENUM, 'INNODB', false, false, false, array('INNODB','MYISAM'));
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
    
    function saveResults($test, $var) {
    	$tables_handler = xoops_getmodulehandler('tables', $GLOBALS['xoopsModule']->getVar('dirname'));
    	foreach($var[$test->getVar('test')] as $testid => $results) {
    		foreach($results['number'] as $tbid => $number) {
	    		$result = $this->create(true);
	    		$result->setVar('tid', $test->getVar('tid'));
	    		$result->setVar('test', $test->getVar('test'));
	    		$table = $tables_handler->get($tbid);
	    		$result->setVar('tbid', $tbid);
	    		$result->setVar('engine', $table->getVar('engine'));
	    		$result->setVar('number', $number);
	    		$result->setVar('length', $var[$test->getVar('test')]['length'][$tbid]);
	    		$result->setVar('fields', $table->getVar('fields'));
	    		$result->setVar('records', $var[$test->getVar('test')]['records'][$tbid]);
	    		$result->setVar('started', $var[$test->getVar('test')]['timer'][$tbid]['start']);
	    		$result->setVar('ended', $var[$test->getVar('test')]['timer'][$tbid]['end']);
	    		$result->setVar('difference', $var[$test->getVar('test')]['timer'][$tbid]['end']-$var[$test->getVar('test')]['timer'][$tbid]['start']);
	    		$result->setVar('common_started', $GLOBAL['timer']['common']['start']);
	    		$result->setVar('common_ended', $GLOBAL['timer']['common']['end']);
	    		$result->setVar('common_difference', $GLOBAL['timer']['common']['end']-$GLOBAL['timer']['common']['start']);
	    		$result->setVar('boot_started', $GLOBAL['timer']['boot']['start']);
	    		$result->setVar('boot_ended', $GLOBAL['timer']['boot']['end']);
	    		$result->setVar('boot_difference', $GLOBAL['timer']['boot']['end']-$GLOBAL['timer']['boot']['start']);
	    		$this->insert($result, true);
	    		unset($result);
    		}
    	}
    }
    
    function getMinMaxAvgSumValues($test) {
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
    			$sql[0] = 'SELECT 	COUNT(*) as `c`, 
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
    			$sql[1] =  'SELECT 	COUNT(*) as `c`,    							break;
    							SUM(`common_difference`) / `c` as `' . constant($test->getVar('test').'_SUMCOMMON') . '`, 
    							AVG(`common_difference`) as `' . constant($test->getVar('test').'_AVGCOMMON') . '`, 
    							MAX(`common_difference`) as `' . constant($test->getVar('test').'_MAXCOMMON') . '`, 
    							MIN(`common_difference`) as `' . constant($test->getVar('test').'_MINCOMMON') . '`, 
    							SUM(`boot_difference`) / `c` as `' . constant($test->getVar('test').'_SUMBOOT') . '`, 
    							AVG(`boot_difference`) as `' . constant($test->getVar('test').'_AVGCOMMON') . '`, 
    							MAX(`boot_difference`) as `' . constant($test->getVar('test').'_MAXCOMMON') . '`, 
    							MIN(`boot_difference`) as `' . constant($test->getVar('test').'_MINBOOT'). '`
    					FROM `' . $GLOBALS['xoopsDB']->prefix('benchmark_results') . '` 
    					WHERE `tid` = "' . $test->getVar('tid') . '"';
    			break;
    	}
    	$result=array();
    	foreach($sql as $id => $question)
    		$result[$id] = $GLOBALS['xoopsDB']->queryF($question);
    	return array_merge($GLOBALS['xoopsDB']->fetchArray($result[0]), $GLOBALS['xoopsDB']->fetchArray($result[1]));;
    }
}
?>