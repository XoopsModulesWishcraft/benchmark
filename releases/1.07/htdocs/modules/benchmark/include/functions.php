<?php
	
	if (!function_exists('benchmark_getFilterElement')) {
		function benchmark_getFilterElement($filter, $field, $sort='created', $op = 'test', $fct = 'list') {
			$components = benchmark_getFilterURLComponents($filter, $field, $sort);
			switch ($field) {
			    case 'altered':
				case 'required':
				case 'primarykey':
					$ele = new BenchmarkFormSelectYN('', 'filter_'.$field.'', $components['value'], 1, false, true);
			    	$ele->setExtra('onchange="window.open(\''.$_SERVER['PHP_SELF'].'?'.$components['extra'].'&filter='.$components['filter'].(!empty($components['filter'])?'|':'').$field.',\'+this.options[this.selectedIndex].value'.(!empty($components['operator'])?'+\','.$components['operator'].'\'':'').',\'_self\')"');
			    	break;
				case 'engine':
					$ele = new BenchmarkFormSelectEngine('', 'filter_'.$field.'', $components['value'], 1, false, true);
			    	$ele->setExtra('onchange="window.open(\''.$_SERVER['PHP_SELF'].'?'.$components['extra'].'&filter='.$components['filter'].(!empty($components['filter'])?'|':'').$field.',\'+this.options[this.selectedIndex].value'.(!empty($components['operator'])?'+\','.$components['operator'].'\'':'').',\'_self\')"');
			    	break;
				case 'stage':
					$ele = new BenchmarkFormSelectStages('', 'filter_'.$field.'', $components['value'], 1, false, true);
			    	$ele->setExtra('onchange="window.open(\''.$_SERVER['PHP_SELF'].'?'.$components['extra'].'&filter='.$components['filter'].(!empty($components['filter'])?'|':'').$field.',\'+this.options[this.selectedIndex].value'.(!empty($components['operator'])?'+\','.$components['operator'].'\'':'').',\'_self\')"');
			    	break;
				case 'test':
					$ele = new BenchmarkFormSelectTests('', 'filter_'.$field.'', $components['value'], 1, false, true);
			    	$ele->setExtra('onchange="window.open(\''.$_SERVER['PHP_SELF'].'?'.$components['extra'].'&filter='.$components['filter'].(!empty($components['filter'])?'|':'').$field.',\'+this.options[this.selectedIndex].value'.(!empty($components['operator'])?'+\','.$components['operator'].'\'':'').',\'_self\')"');
			    	break;
				case 'platform':
					$ele = new BenchmarkFormSelectPlatform('', 'filter_'.$field.'', $components['value'], 1, false, true);
			    	$ele->setExtra('onchange="window.open(\''.$_SERVER['PHP_SELF'].'?'.$components['extra'].'&filter='.$components['filter'].(!empty($components['filter'])?'|':'').$field.',\'+this.options[this.selectedIndex].value'.(!empty($components['operator'])?'+\','.$components['operator'].'\'':'').',\'_self\')"');
			    	break;
				case 'type':
				case 'alter_type':
					$ele = new BenchmarkFormSelectFieldType('', 'filter_'.$field.'', $components['value'], 1, false, true);
			    	$ele->setExtra('onchange="window.open(\''.$_SERVER['PHP_SELF'].'?'.$components['extra'].'&filter='.$components['filter'].(!empty($components['filter'])?'|':'').$field.',\'+this.options[this.selectedIndex].value'.(!empty($components['operator'])?'+\','.$components['operator'].'\'':'').',\'_self\')"');
			    	break;
			    case 'number':
			    case 'length':
			    case 'fields':
				case 'records':			    	
				case 'key':
				case 'recipricol':
				case 'size':
				case 'alter_sql':
				case 'create_sql':
				case 'name':
				case 'testing':
			    	$ele = new XoopsFormElementTray('');
					$ele->addElement(new XoopsFormText('', 'filter_'.$field.'', 3, 40, $components['value']));
					$button = new XoopsFormButton('', 'button_'.$field.'', '[+]');
			    	$button->setExtra('onclick="window.open(\''.$_SERVER['PHP_SELF'].'?'.$components['extra'].'&filter='.$components['filter'].(!empty($components['filter'])?'|':'').$field.',\'+$(\'#filter_'.$field.'\').val()'.(!empty($components['operator'])?'+\','.$components['operator'].'\'':'').',\'_self\')"');
			    	$ele->addElement($button);
			    	break;
			}
			return $ele;
		}
	}
	
	if (!function_exists('benchmark_getFilterURLComponents')) {
		function benchmark_getFilterURLComponents($filter, $field, $sort='created') {
			$parts = explode('|', $filter);
			$ret = array();
			$value = '';
	    	foreach($parts as $part) {
	    		$var = explode(',', $part);
	    		if (count($var)>1) {
		    		if ($var[0]==$field) {
		    			$ele_value = $var[1];
		    			if (isset($var[2]))
		    				$operator = $var[2];
		    		} elseif ($var[0]!=1) {
		    			$ret[] = implode(',', $var);
		    		}
	    		}
	    	}
	    	$pagenav = array();
	    	$pagenav['op'] = isset($_REQUEST['op'])?$_REQUEST['op']:"shops";
			$pagenav['fct'] = isset($_REQUEST['fct'])?$_REQUEST['fct']:"list";
			$pagenav['limit'] = !empty($_REQUEST['limit'])?intval($_REQUEST['limit']):30;
			$pagenav['start'] = 0;
			$pagenav['order'] = !empty($_REQUEST['order'])?$_REQUEST['order']:'DESC';
			$pagenav['sort'] = !empty($_REQUEST['sort'])?''.$_REQUEST['sort'].'':$sort;
	    	$retb = array();
			foreach($pagenav as $key=>$value) {
				$retb[] = "$key=$value";
			}
			return array('value'=>$ele_value, 'field'=>$field, 'operator'=>$operator, 'filter'=>implode('|', $ret), 'extra'=>implode('&', $retb));
		}
	}
	
	if (!function_exists('benchmark_chainedSQL')) {
		function benchmark_chainedSQL($mixed) {
			if (is_array($mixed)) {
				foreach($mixed as $key => $value) {
					return benchmark_chainedSQL($value);
				}		
			} elseif(is_string($mixed)) {
				if (!$GLOBALS['xoopsDB']->queryF($mixed)) {
					xoops_error($mixed, $GLOBALS['xoopsDB']->errno(). ' - ' . $GLOBALS['xoopsDB']->error());
					return false;
				}
				return true;
			}
		}
	}
	
	if (!function_exists('benchmark_getNextTest')) {
		function benchmark_getNextTest($test) {
			switch ($test->getVar('test')) {
				default:
				case '_MI_BENCHMARK_WAIT':
					if ($GLOBALS['xoopsModuleConfig']['create_table']==true&&$test->getVar('test')!='_MI_BENCHMARK_CREATE') {
						return '_MI_BENCHMARK_CREATE';
					} 
				case '_MI_BENCHMARK_CREATE':
					if ($GLOBALS['xoopsModuleConfig']['select_records']==true&&$test->getVar('test')!='_MI_BENCHMARK_SELECT') {
						return '_MI_BENCHMARK_SELECT';
					} 
				case '_MI_BENCHMARK_SELECT':
					if ($GLOBALS['xoopsModuleConfig']['insert_records']==true&&$test->getVar('test')!='_MI_BENCHMARK_INSERT') {
						return '_MI_BENCHMARK_INSERT';
					}				
				case '_MI_BENCHMARK_INSERT':
					if ($GLOBALS['xoopsModuleConfig']['update_records']==true&&$test->getVar('test')!='_MI_BENCHMARK_UPDATE') {
						return '_MI_BENCHMARK_UPDATE';
					}				
				case '_MI_BENCHMARK_UPDATE':
					if ($GLOBALS['xoopsModuleConfig']['update_all_records']==true&&$test->getVar('test')!='_MI_BENCHMARK_UPDATEALL') {
						return '_MI_BENCHMARK_UPDATEALL';
					}				
				case '_MI_BENCHMARK_UPDATEALL':
					if ($GLOBALS['xoopsModuleConfig']['delete_records']==true&&$test->getVar('test')!='_MI_BENCHMARK_DELETE') {
						return '_MI_BENCHMARK_DELETE';
					}			
				case '_MI_BENCHMARK_DELETE':
					if ($GLOBALS['xoopsModuleConfig']['delete_all_records']==true&&$test->getVar('test')!='_MI_BENCHMARK_DELETEALL') {
						return '_MI_BENCHMARK_DELETEALL';
					} 
				case '_MI_BENCHMARK_DELETEALL':
					if ($GLOBALS['xoopsModuleConfig']['alter_table']==true&&$test->getVar('test')!='_MI_BENCHMARK_ALTER') {
						return '_MI_BENCHMARK_ALTER';
					} 
				case '_MI_BENCHMARK_ALTER':
					if ($GLOBALS['xoopsModuleConfig']['rename_table']==true&&$test->getVar('test')!='_MI_BENCHMARK_RENAME') {
						return '_MI_BENCHMARK_RENAME';
					} 
				case '_MI_BENCHMARK_RENAME':
					if ($GLOBALS['xoopsModuleConfig']['smarty']==true&&$test->getVar('test')!='_MI_BENCHMARK_SMARTY') {
						return '_MI_BENCHMARK_SMARTY';
					}
				case '_MI_BENCHMARK_SMARTY':
					return '_MI_BENCHMARK_FINISHED';
					break;				
			}
		}
	}
	
	if (!function_exists('benchmark_getTimeTaken')) {
		function benchmark_getTimeTaken($timer, $ret=0) {
			if (is_array($timer)&&!isset($timer['start'])&&!isset($timer['end'])
				&&!is_numeric($timer['start'])&&!is_numeric($timer['end'])) {
				foreach($timer as $key => $value) {
					$ret = $ret + benchmark_getTimeTaken($value, $ret);
				}
			} elseif (is_array($timer)&&isset($timer['start'])&&isset($timer['end'])) {
				$ret = $ret + ($timer['end']-$timer['start']);
			}
			return (float)$ret;
		}
	}
	
	if (!function_exists('benchmark_getVarcharEnumeration')) {
		function benchmark_getVarcharEnumeration() {
			static $ret = array();
			if (sizeof($ret)>1)
				return $ret;
			foreach(explode('|', $GLOBALS['xoopsModuleConfig']['numbers']) as $id=>$value) {
				$result = explode('=', $value);
				if (isset($result[0])&&isset($result[1])) {
					$ret[$result[1]] = $result[1];
				}
			}
			return $ret;
		}
	}
	
	if (!function_exists('benchmark_getNumericEnumeration')) {
		function benchmark_getNumericEnumeration() {
			static $ret = array();
			if (sizeof($ret)>1)
				return $ret;
				
			foreach(explode('|', $GLOBALS['xoopsModuleConfig']['numbers']) as $id=>$value) {
				$result = explode('=', $value);
				if (isset($result[0])&&isset($result[1])) {
					$ret[$result[0]] = $result[0];
				}
			}
			
			return $ret;
		}
	}
	
	if (!function_exists('benchmark_getFillValue')) {
		function benchmark_getFillValue($field, $table) {
			mt_srand(mt_rand(0,microtime(true)/mt_rand(1,mt_rand(10,41))));
			mt_srand(mt_rand(0,microtime(true)/mt_rand(1,mt_rand(3,20))));
			mt_srand(mt_rand(0,microtime(true)/mt_rand(1,mt_rand(2,15))));
			switch ($field->getVar('type')) {
				case 'boolean':
					return mt_rand(0, 1);
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
					return $ret[mt_rand(0, sizeof($ret)-1)];
					break;
				case 'enum_int':
					$ret = benchmark_getNumericEnumeration();
					return $ret[mt_rand(0, sizeof($ret)-1)];
					break;
				case 'varchar':
				case 'mediumtext':
				case 'text':
					return (string)benchmark_genString(mt_rand($field->getVar('size')*0.159, $field->getVar('size')));
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
			for($i=0;$i<=$size-2;$i++) {
				$ret .= mt_rand(0,9);
			}
			if ($recipricol>0) {
				$ret .= '.';
				for($i=0;$i<=$recipricol-1;$i++) {
					$ret .= mt_rand(0,9);
				}
			}
			return (mt_rand(0,1)==1?$ret:'-'.$ret);
		}
	}
	
	if (!function_exists('benchmark_genString')) {
		function benchmark_genString($size) {
			$base = explode('|', $GLOBALS['xoopsModuleConfig']['characters']);
			for($i=0;$i<=$size;$i++) {
				$ret .= $base[mt_rand(0,sizeof($base))];
			}
			return $ret;
		}
	}
?>