<?php


defined('XOOPS_ROOT_PATH') or die('Restricted access');


class BenchmarkCorePreload extends XoopsPreloadItem
{
	function eventCoreIncludeCommonStart()
	{
		$GLOBALS['timer']['common']['start'] = microtime(true);
	}
	
	function eventCoreIncludeCommonEnd()
	{
		$GLOBALS['timer']['common']['end'] = microtime(true);
	}

	function eventCoreFooterStart()
	{
		$GLOBALS['timer']['footer']['start'] = microtime(true);
	}
	
	function eventCoreFooterEnd()
	{
		$GLOBALS['timer']['footer']['end'] = microtime(true);
	}
	
	function eventCoreHeaderStart()
	{
		$GLOBALS['timer']['header']['start'] = microtime(true);
	}
	
	function eventCoreHeaderEnd()
	{
		$GLOBALS['timer']['header']['end'] = microtime(true);
	}
	
}

?>