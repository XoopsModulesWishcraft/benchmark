<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

function xoops_module_update_benchmark($module, $oldversion) {
	
	$sql=array();
	
	$sql[] = 'ALTER TABLE '.$GLOBALS['xoopsDB']->prefix('benchmark_tests') . ' CHANGE COLUMN `tbids_create` `tbids_create` MEDIUMTEXT';
	$sql[] = 'ALTER TABLE '.$GLOBALS['xoopsDB']->prefix('benchmark_tests') . ' CHANGE COLUMN `tbids_select` `tbids_select` MEDIUMTEXT';
	$sql[] = 'ALTER TABLE '.$GLOBALS['xoopsDB']->prefix('benchmark_tests') . ' CHANGE COLUMN `tbids_insert` `tbids_insert` MEDIUMTEXT';
	$sql[] = 'ALTER TABLE '.$GLOBALS['xoopsDB']->prefix('benchmark_tests') . ' CHANGE COLUMN `tbids_update` `tbids_update` MEDIUMTEXT';
	$sql[] = 'ALTER TABLE '.$GLOBALS['xoopsDB']->prefix('benchmark_tests') . ' CHANGE COLUMN `tbids_updateall` `tbids_updateall` MEDIUMTEXT';
	$sql[] = 'ALTER TABLE '.$GLOBALS['xoopsDB']->prefix('benchmark_tests') . ' CHANGE COLUMN `tbids_delete` `tbids_delete` MEDIUMTEXT';
	$sql[] = 'ALTER TABLE '.$GLOBALS['xoopsDB']->prefix('benchmark_tests') . ' CHANGE COLUMN `tbids_deleteall` `tbids_deleteall` MEDIUMTEXT';
	$sql[] = 'ALTER TABLE '.$GLOBALS['xoopsDB']->prefix('benchmark_tests') . ' CHANGE COLUMN `tbids_alter` `tbids_alter` MEDIUMTEXT';
	$sql[] = 'ALTER TABLE '.$GLOBALS['xoopsDB']->prefix('benchmark_tests') . ' CHANGE COLUMN `tbids_rename` `tbids_rename` MEDIUMTEXT';
	$sql[] = 'ALTER TABLE '.$GLOBALS['xoopsDB']->prefix('benchmark_tests') . ' CHANGE COLUMN `tbids_smarty` `tbids_smarty` MEDIUMTEXT';
	$sql[] = 'ALTER TABLE '.$GLOBALS['xoopsDB']->prefix('benchmark_tables') . ' CHANGE COLUMN `fids` `fids` MEDIUMTEXT';
	$sql[] = 'ALTER TABLE '.$GLOBALS['xoopsDB']->prefix('benchmark_results') . ' ADD COLUMN `session` INT(12) DEFAULT \'0\'';
	
	foreach ($sql as $question)
		if ($GLOBALS['xoopsDB']->queryF($question))
			xoops_error($question, 'SQL Executed Sucessfully');
	
	return true;
}
?>