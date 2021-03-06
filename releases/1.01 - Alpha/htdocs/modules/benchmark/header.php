<?php

	$GLOBALS['timer']['boot']['start'] = microtime(true);
	
	require ('../../mainfile.php');
	require ('include/functions.php');
	
	xoops_load('pagenav');	
	xoops_load('xoopsmailer');
	
	xoops_load('xoopscache');
	if (!class_exists('XoopsCache')) {
		// XOOPS 2.4 Compliance
		xoops_load('cache');
		if (!class_exists('XoopsCache')) {
			include_once XOOPS_ROOT_PATH.'/class/cache/xoopscache.php';
		}
	}
	
	$module_handler = xoops_gethandler('module');
	$config_handler = xoops_gethandler('config');
	$GLOBALS['xoopsModule'] = $module_handler->getByDirame('benchmark');
	$GLOBALS['xoopsModuleConfig'] = $config_handler->getConfigList($GLOBALS['xoopsModule']->getVar('mid'));

	ini_set('memory_limit', $GLOBALS['xoopsModuleConfig']['memory_limit']);
	set_time_limit($GLOBALS['xoopsModuleConfig']['time_limit']);
	
	$fields_handler = xoops_getmodulehandler('fields', $GLOBALS['xoopsModule']->getVar('dirname'));
	$results_handler = xoops_getmodulehandler('results', $GLOBALS['xoopsModule']->getVar('dirname'));
	$tables_handler = xoops_getmodulehandler('tables', $GLOBALS['xoopsModule']->getVar('dirname'));
	$tester_handler = xoops_getmodulehandler('tester', $GLOBALS['xoopsModule']->getVar('dirname'));
	$tests_handler = xoops_getmodulehandler('tests', $GLOBALS['xoopsModule']->getVar('dirname'));
	
?>