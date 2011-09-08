<?php
	
	if (!function_exists('benchmark_chainedSQL')) {
		function benchmark_chainedSQL($mixed) {
			if (is_array($mixed)) {
				foreach($mixed as $key => $value) {
					return benchmark_chainedSQL($value);
				}		
			} elseif(is_string($mixed)) {
				return $GLOBALS['xoopsDB']->queryF($mixed);
			}
		}
	}
	
	if (!function_exists('benchmark_getNextTest')) {
		function benchmark_getNextTest($test) {
			switch ($test->getVar('test')) {
				default:
				case '_MI_BENCHMARK_WAIT':
				case '_MI_BENCHMARK_CREATE':
					if ($GLOBALS['xoopsModuleConfig']['test_create_table']==true&&$test->getVar('test')!='_MI_BENCHMARK_CREATE') {
						return '_MI_BENCHMARK_CREATE';
						break;
					}
				case '_MI_BENCHMARK_SELECT':
					if ($GLOBALS['xoopsModuleConfig']['test_select_records']==true&&$test->getVar('test')!='_MI_BENCHMARK_SELECT') {
						return '_MI_BENCHMARK_SELECT';
						break;
					}
				case '_MI_BENCHMARK_INSERT':
					if ($GLOBALS['xoopsModuleConfig']['test_insert_records']==true&&$test->getVar('test')!='_MI_BENCHMARK_INSERT') {
						return '_MI_BENCHMARK_INSERT';
						break;
					}				
				case '_MI_BENCHMARK_UPDATE':
					if ($GLOBALS['xoopsModuleConfig']['test_update_records']==true&&$test->getVar('test')!='_MI_BENCHMARK_UPDATE') {
						return '_MI_BENCHMARK_UPDATE';
						break;
					}				
				case '_MI_BENCHMARK_UPDATEALL':
					if ($GLOBALS['xoopsModuleConfig']['test_update_all_records']==true&&$test->getVar('test')!='_MI_BENCHMARK_UPDATEALL') {
						return '_MI_BENCHMARK_UPDATEALL';
						break;
					}				
				case '_MI_BENCHMARK_DELETE':
					if ($GLOBALS['xoopsModuleConfig']['test_delete_records']==true&&$test->getVar('test')!='_MI_BENCHMARK_DELETE') {
						return '_MI_BENCHMARK_DELETE';
						break;
					}				
				case '_MI_BENCHMARK_DELETEALL':
					if ($GLOBALS['xoopsModuleConfig']['test_delete_all_records']==true&&$test->getVar('test')!='_MI_BENCHMARK_DELETEALL') {
						return '_MI_BENCHMARK_DELETEALL';
						break;
					}				
				case '_MI_BENCHMARK_ALTER':
					if ($GLOBALS['xoopsModuleConfig']['test_alter_table']==true&&$test->getVar('test')!='_MI_BENCHMARK_ALTER') {
						return '_MI_BENCHMARK_ALTER';
						break;
					}				
				case '_MI_BENCHMARK_RENAME':
					if ($GLOBALS['xoopsModuleConfig']['test_rename_table']==true&&$test->getVar('test')!='_MI_BENCHMARK_RENAME') {
						return '_MI_BENCHMARK_RENAME';
						break;
					}		
				case '_MI_BENCHMARK_SMARTY':
					if ($GLOBALS['xoopsModuleConfig']['test_smarty']==true&&$test->getVar('test')!='_MI_BENCHMARK_SMARTY') {
						return '_MI_BENCHMARK_SMARTY';
						break;
					}		
				case '_MI_BENCHMARK_FINISHED':
					return '_MI_BENCHMARK_FINISHED';
					break;				
			}
		}
	}
	
	if (!function_exists('benchmark_getTimeTaken')) {
		function benchmark_getTimeTaken($timer, $ret=0) {
			if (is_array($timer)&&!isset($timer['start'])&&!isset($timer['end'])) {
				foreach($timer as $key => $value) {
					$ret = $ret + benchmark_getTimeTaken($timer[$key], $ret);
				}
			} elseif (is_array($timer)&&isset($timer['start'])&&isset($timer['end'])) {
				$ret = $ret + ($timer['end']-$timer['start']);
			}
			return $ret;
		}
	}
	
	if (!function_exists('benchmark_getVarcharEnumeration')) {
		function benchmark_getVarcharEnumeration() {
			$ret = array();
			foreach(explode('|', $GLOBAL['xoopsModuleConfig']['test_numbers']) as $value) {
				$result = explode('=', $value);
				if (isset($result[0])&&isset($result[1])&&count($result)==2) {
					$ret[$result[1]] = $result[1];
				}
			}
			return $ret;
		}
	}
	
	if (!function_exists('benchmark_getNumericEnumeration')) {
		function benchmark_getNumericEnumeration() {
			$ret = array();
			foreach(explode('|', $GLOBAL['xoopsModuleConfig']['test_numbers']) as $value) {
				$result = explode('=', $value);
				if (isset($result[0])&&isset($result[1])&&count($result)==2) {
					$ret[$result[0]] = $result[0];
				}
			}
			return $ret;
		}
	}
	
	if (!function_exists('benchmark_getFillValue')) {
		function benchmark_getFillValue($field, $table) {
			mt_srand(time());
			switch ($field->getVar('type')) {
				case 'bigint':
				case 'mediumint':
				case 'smallint':
				case 'int':
				case 'tinyint':
					return intval(benchmark_genNumerical($field->getVar('size'), $field->getVar('recipricol')));
					break;
				case 'float':
					return floatval(benchmark_genNumerical($field->getVar('size'), $field->getVar('recipricol')));
					break;
				case 'decimal':
				case 'double':
					return doubleval(benchmark_genNumerical($field->getVar('size'), $field->getVar('recipricol')));
					break;
				case 'enum_varchar':
					$ret = benchmark_getVarcharEnumeration();
					return $ret[mt_rand(0, sizeof($ret))];
					break;
				case 'enum_int':
					$ret = benchmark_getNumericEnumeration();
					return $ret[mt_rand(0, sizeof($ret))];
					break;
				case 'varchar':
				case 'mediumtext':
				case 'text':
					return (string)benchmark_genString($field->getVar('size'));
					break;
				case 'date':
					return date('Y-m-d', mt_rand(1, time()));
				case 'time':
					return date('H:i:s', mt_rand(1, time()));
				case 'datetime':
					return date('Y-m-d H:i:s', mt_rand(1, time()));
				case 'year':
					return mt_rand(1, 9999);
			}
		}
	}
	
	if (!function_exists('benchmark_genNumerical')) {
		function benchmark_genNumerical($size, $recipricol) {
			for($i=0;$i<=$size;$i++) {
				$ret .= mt_rand(0,9);
			}
			if ($recipricol>0) {
				$ret .= '.';
				for($i=0;$i<=$recipricol;$i++) {
					$ret .= mt_rand(0,9);
				}
			}
			return (mt_rand(0,1)==1?$ret:'-'.$ret);
		}
	}
	
	if (!function_exists('benchmark_genString')) {
		function benchmark_genString($size) {
			$base = explode('|', $GLOBAL['xoopsModuleConfig']['test_characters']);
			for($i=0;$i<=$size;$i++) {
				$ret .= $base[mt_rand(0,sizeof($base))];
			}
			return $ret;
		}
	}
?>