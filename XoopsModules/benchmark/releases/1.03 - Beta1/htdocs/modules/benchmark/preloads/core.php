<?php


defined('XOOPS_ROOT_PATH') or die('Restricted access');


class BenchmarkCorePreload extends XoopsPreloadItem
{
	function eventCoreIncludeCommonStart()
	{
		$GLOBAL['timer']['common']['start'] = microtime(true);
	}
	
	function eventCoreIncludeCommonEnd()
	{
		$GLOBAL['timer']['common']['end'] = microtime(true);
	}

	function eventCoreFooterStart()
	{
		$GLOBAL['timer']['footer']['start'] = microtime(true);
	}
	
	function eventCoreFooterEnd()
	{
		$GLOBAL['timer']['footer']['end'] = microtime(true);
	}
	
	function eventCoreHeaderStart()
	{
		$GLOBAL['timer']['header']['start'] = microtime(true);
	}
	
	function eventCoreHeaderEnd()
	{
		$GLOBAL['timer']['header']['end'] = microtime(true);
	}
	
}

?>