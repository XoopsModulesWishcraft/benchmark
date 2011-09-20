<?php
	include('../../../mainfile.php');
	$module_handler = xoops_gethandler('module');
	$xoModule = $module_handler->getByDirname('benchmark');
	redirect_header(XOOPS_URL."/modules/system/admin.php?fct=preferences&op=showmod&mod=".$xoModule->getVar('mid'));
	exit(0);
?>