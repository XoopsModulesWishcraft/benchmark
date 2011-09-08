<?php
	
	include('header.php');
	
	$op = isset($_REQUEST['op'])?$_REQUEST['op']:"default";
	$fct = isset($_REQUEST['fct'])?$_REQUEST['fct']:"default";
	$limit = !empty($_REQUEST['limit'])?intval($_REQUEST['limit']):30;
	$start = !empty($_REQUEST['start'])?intval($_REQUEST['start']):0;
	$order = !empty($_REQUEST['order'])?$_REQUEST['order']:'DESC';
	$sort = !empty($_REQUEST['sort'])?''.$_REQUEST['sort'].'':'created';
	$filter = !empty($_REQUEST['filter'])?''.$_REQUEST['filter'].'':'1,1';
	
	switch($op) {
		default:
		case "default":
			$xoopsOption['template_main'] = 'benchmark_index.html';
			include($GLOBALS['xoops']->path('/header.php'));
			$tests_handler =& xoops_getmodulehandler('tests', $GLOBAL['xoopsModule']->getVar('dirname'));
			$results_handler =& xoops_getmodulehandler('results', $GLOBAL['xoopsModule']->getVar('dirname'));
			$fields_handler =& xoops_getmodulehandler('fields', $GLOBAL['xoopsModule']->getVar('dirname'));
			$tables_handler =& xoops_getmodulehandler('tables', $GLOBAL['xoopsModule']->getVar('dirname'));
			$GLOBALS['xoopsTpl']->assign('tests', $tests_handler->getCount());
			$GLOBALS['xoopsTpl']->assign('results', $results_handler->getCount());
			$GLOBALS['xoopsTpl']->assign('fields', $fields_handler->getCount());
			$GLOBALS['xoopsTpl']->assign('tables', $tables_handler->getCount());
			$GLOBALS['xoopsTpl']->assign('total_seconds', $results_handler->getSum('difference'));
			$GLOBALS['xoopsTpl']->assign('total_fields', $results_handler->getSum('fields'));
			$GLOBALS['xoopsTpl']->assign('total_length', $results_handler->getSum('length'));
			$GLOBALS['xoopsTpl']->assign('total_records', $results_handler->getSum('records'));
			$GLOBALS['xoopsTpl']->assign('total_common', $results_handler->getSum('common_difference'));
			$GLOBALS['xoopsTpl']->assign('total_boot', $results_handler->getSum('boot_difference'));
			$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['xoopsModuleConfig']);
			$GLOBALS['xoopsTpl']->assign('php_self', $_SERVER['PHP_SELF']);
			$GLOBALS['xoopsTpl']->assign('form', tweetbomb_test_get_form(false));
			include($GLOBALS['xoops']->path('/footer.php'));
			exit(0);
			break;
		case "tests":	
			switch ($fct)
			{
				default:
				case "list":				
					$xoopsOption['template_main'] = 'benchmark_list_test.html';
					include($GLOBALS['xoops']->path('/header.php'));
					
					$tests_handler =& xoops_getmodulehandler('tests', $GLOBAL['xoopsModule']->getVar('dirname'));

					$criteria = $tests_handler->getFilterCriteria($filter);
					$ttl = $tests_handler->getCount($criteria);
					$sort = !empty($_REQUEST['sort'])?''.$_REQUEST['sort'].'':'created';
					
					$pagenav = new XoopsPageNav($ttl, $limit, $start, 'start', 'limit='.$limit.'&sort='.$sort.'&order='.$order.'&op='.$op.'&fct='.$fct.'&filter='.$filter);
					$GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav());
			
					foreach (array(	'tid','stage','test','name','platform','testing','begin','started','ended','created','updated','actioned') as $id => $key) {
						$GLOBALS['xoopsTpl']->assign(strtolower(str_replace('-','_',$key).'_th'), '<a href="'.$_SERVER['PHP_SELF'].'?start='.$start.'&limit='.$limit.'&sort='.str_replace('_','-',$key).'&order='.((str_replace('_','-',$key)==$sort)?($order=='DESC'?'ASC':'DESC'):$order).'&op='.$op.'&filter='.$filter.'">'.(defined('_MN_BENCHMARK_TH_'.strtoupper(str_replace('-','_',$key)))?constant('_MN_BENCHMARK_TH_'.strtoupper(str_replace('-','_',$key))):'_MN_BENCHMARK_TH_'.strtoupper(str_replace('-','_',$key))).'</a>');
						$GLOBALS['xoopsTpl']->assign('filter_'.strtolower(str_replace('-','_',$key)).'_th', $tests_handler->getFilterForm($filter, $key, $sort, $op, $fct));
					}
					
					$GLOBALS['xoopsTpl']->assign('limit', $limit);
					$GLOBALS['xoopsTpl']->assign('start', $start);
					$GLOBALS['xoopsTpl']->assign('order', $order);
					$GLOBALS['xoopsTpl']->assign('sort', $sort);
					$GLOBALS['xoopsTpl']->assign('filter', $filter);
					$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['xoopsModuleConfig']);
										
					$criteria->setStart($start);
					$criteria->setLimit($limit);
					$criteria->setSort('`'.$sort.'`');
					$criteria->setOrder($order);
						
					$tests = $tests_handler->getObjects($criteria, true);
					foreach($tests as $tid => $test) {
						$GLOBALS['xorTpl']->append('tests', $test->toArray());
					}
					$GLOBALS['xoopsTpl']->assign('form', tweetbomb_test_get_form(false));
					$GLOBALS['xoopsTpl']->assign('php_self', $_SERVER['PHP_SELF']);
					include($GLOBALS['xoops']->path('/footer.php'));
					exit(0);
					
					break;		
					
				case "new":
					
					$xoopsOption['template_main'] = 'benchmark_create_test.html';
					include($GLOBALS['xoops']->path('/header.php'));
										
					$tests_handler =& xoops_getmodulehandler('tests', $GLOBAL['xoopsModule']->getVar('dirname'));
					if (isset($_REQUEST['id'])) {
						$test = $tests_handler->get(intval($_REQUEST['id']));
					} else {
						$test = $tests_handler->create();
					}
					
					$GLOBALS['xoopsTpl']->assign('form', $test->getForm());
					$GLOBALS['xoopsTpl']->assign('php_self', $_SERVER['PHP_SELF']);
					include($GLOBALS['xoops']->path('/footer.php'));
					exit(0);
					
				case "edit":
					
					$xoopsOption['template_main'] = 'benchmark_edit_test.html';
					include($GLOBALS['xoops']->path('/header.php'));
										
					$tests_handler =& xoops_getmodulehandler('tests', $GLOBAL['xoopsModule']->getVar('dirname'));
					if (isset($_REQUEST['id'])) {
						$test = $tests_handler->get(intval($_REQUEST['id']));
					} else {
						$test = $tests_handler->create();
					}
					
					$GLOBALS['xoopsTpl']->assign('form', $test->getForm());
					$GLOBALS['xoopsTpl']->assign('php_self', $_SERVER['PHP_SELF']);
					include($GLOBALS['xoops']->path('/footer.php'));
					exit(0);
					
					break;
				case "save":
					
					$tests_handler =& xoops_getmodulehandler('tests', $GLOBAL['xoopsModule']->getVar('dirname'));
					$id=0;
					if ($id=intval($_REQUEST['id'])) {
						$test = $tests_handler->get($id);
					} else {
						$test = $tests_handler->create();
					}
					$test->setVars($_POST[$id]);
					if (!empty($_POST[$id]['begin']))
						$test->setVar('begin', strtotime($_POST[$id]['begin']));
										
					if (!$id=$tests_handler->insert($campaign)) {
						redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_TEST_FAILEDTOSAVE);
						exit(0);
					} else {
						redirect_header('index.php?op='.$op.'&fct=edit&id='.$id, 10, _MN_MSG_TEST_SAVEDOKEY);
						exit(0);
					}
					break;
				case "savelist":
					
					$tests_handler =& xoops_getmodulehandler('tests', $GLOBAL['xoopsModule']->getVar('dirname'));
					foreach($_REQUEST['id'] as $id) {
						$test = $tests_handler->get($id);
						$test->setVars($_POST[$id]);
						if (!empty($_POST[$id]['begin']))
							$test->setVar('begin', strtotime($_POST[$id]['begin']));
							
						if (!$tests_handler->insert($test)) {
							redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_TEST_FAILEDTOSAVE);
							exit(0);
						} 
					}
					redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_TEST_SAVEDOKEY);
					exit(0);
					break;				
				case "delete":	
								
					$tests_handler =& xoops_getmodulehandler('tests', $GLOBAL['xoopsModule']->getVar('dirname'));
					$results_handler =& xoops_getmodulehandler('results', $GLOBAL['xoopsModule']->getVar('dirname'));
					$tables_handler =& xoops_getmodulehandler('tables', $GLOBAL['xoopsModule']->getVar('dirname'));
					$id=0;
					if (isset($_POST['id'])&&$id=intval($_POST['id'])) {
						$test = $tests_handler->get($id);
						if (!$tests_handler->delete($test)) {
							redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_TEST_FAILEDTODELETE);
							exit(0);
						} else {
							$criteria = new Criteria('tid', $test->getVar('tid'));
							$results_handler->deleteAll($criteria);
							$tables_handler->deleteAll($criteria);
							redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_TEST_DELETED);
							exit(0);
						}
					} else {
						include($GLOBALS['xoops']->path('/header.php'));
						$test = $tests_handler->get(intval($_REQUEST['id']));
						xoops_confirm(array('id'=>$_REQUEST['id'], 'op'=>$_REQUEST['op'], 'fct'=>$_REQUEST['fct'], 'limit'=>$_REQUEST['limit'], 'start'=>$_REQUEST['start'], 'order'=>$_REQUEST['order'], 'sort'=>$_REQUEST['sort'], 'filter'=>$_REQUEST['filter']), 'index.php', sprintf(_MN_MSG_TEST_DELETE, $test->getVar('name')));
						include($GLOBALS['xoops']->path('/footer.php'));
						exit(0);
					}
					break;
			}
			break;
		case "results":	
			switch ($fct)
			{
				default:
				case "list":				
					$xoopsOption['template_main'] = 'benchmark_list_result.html';
					include($GLOBALS['xoops']->path('/header.php'));
					
					$results_handler =& xoops_getmodulehandler('results', $GLOBAL['xoopsModule']->getVar('dirname'));

					$criteria = $results_handler->getFilterCriteria($filter);
					$ttl = $results_handler->getCount($criteria);
					$sort = !empty($_REQUEST['sort'])?''.$_REQUEST['sort'].'':'created';
					
					$pagenav = new XoopsPageNav($ttl, $limit, $start, 'start', 'limit='.$limit.'&sort='.$sort.'&order='.$order.'&op='.$op.'&fct='.$fct.'&filter='.$filter);
					$GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav());
			
					foreach (array(	'rid','tid','tbid','test','engine','number','length','fields','records','started','ended','difference','common_difference','boot_difference','created','updated','actioned') as $id => $key) {
						$GLOBALS['xoopsTpl']->assign(strtolower(str_replace('-','_',$key).'_th'), '<a href="'.$_SERVER['PHP_SELF'].'?start='.$start.'&limit='.$limit.'&sort='.str_replace('_','-',$key).'&order='.((str_replace('_','-',$key)==$sort)?($order=='DESC'?'ASC':'DESC'):$order).'&op='.$op.'&filter='.$filter.'">'.(defined('_MN_BENCHMARK_TH_'.strtoupper(str_replace('-','_',$key)))?constant('_MN_BENCHMARK_TH_'.strtoupper(str_replace('-','_',$key))):'_MN_BENCHMARK_TH_'.strtoupper(str_replace('-','_',$key))).'</a>');
						$GLOBALS['xoopsTpl']->assign('filter_'.strtolower(str_replace('-','_',$key)).'_th', $results_handler->getFilterForm($filter, $key, $sort, $op, $fct));
					}
					
					$GLOBALS['xoopsTpl']->assign('limit', $limit);
					$GLOBALS['xoopsTpl']->assign('start', $start);
					$GLOBALS['xoopsTpl']->assign('order', $order);
					$GLOBALS['xoopsTpl']->assign('sort', $sort);
					$GLOBALS['xoopsTpl']->assign('filter', $filter);
					$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['xoopsModuleConfig']);
										
					$criteria->setStart($start);
					$criteria->setLimit($limit);
					$criteria->setSort('`'.$sort.'`');
					$criteria->setOrder($order);
						
					$results = $results_handler->getObjects($criteria, true);
					foreach($results as $tid => $result) {
						$GLOBALS['xorTpl']->append('results', $result->toArray());
					}
					$GLOBALS['xoopsTpl']->assign('form', tweetbomb_RESULT_get_form(false));
					$GLOBALS['xoopsTpl']->assign('php_self', $_SERVER['PHP_SELF']);
					include($GLOBALS['xoops']->path('/footer.php'));
					exit(0);
					
					break;		

				case "export":				
					
					$results_handler =& xoops_getmodulehandler('results', $GLOBAL['xoopsModule']->getVar('dirname'));

					$criteria = $results_handler->getFilterCriteria($filter);
					$ttl = $results_handler->getCount($criteria);
					$sort = !empty($_REQUEST['sort'])?''.$_REQUEST['sort'].'':'created';

					$criteria->setSort('`'.$sort.'`');
					$criteria->setOrder($order);
						
					$results = $results_handler->getObjects($criteria, true);
					foreach($results as $tid => $result) {
						$out = array();
						foreach($result->toArray() as $field => $value) {
							switch ($field) {
								case 'form':
									break;
								case 'test':
								case 'table':
									foreach($value as $fieldb => $valueb) {
										if (is_numeric($valueb)) {
											$out[$fieldb] = ''.$valueb.',';
										}elseif (is_string($value)) {
											$out[$fieldb] = '"'.$valueb.'"';
										}
									}
									break;
								default:
									if (is_numeric($value)) {
										$out[$field] = ''.$value.',';
									}elseif (is_string($value)) {
										$out[$field] = '"'.$value.'"';
									}
									break;
							}
						}
						$header = '"'.implode('","', array_keys($out)).'"'."\n";
						$output .= implode(',', $out)."\n";
					}
					
					header('Content-Disposition: attachment; filename="results_'.md5($filter).'.csv"');
					header("Content-Type: text/comma-separated-values");
					echo $header;
					echo $output;					
					exit(0);
					
					break;				
/*	
				case "new":
					
					$xoopsOption['template_main'] = 'benchmark_create_result.html';
					include($GLOBALS['xoops']->path('/header.php'));
										
					$results_handler =& xoops_getmodulehandler('results', $GLOBAL['xoopsModule']->getVar('dirname'));
					if (isset($_REQUEST['id'])) {
						$result = $results_handler->get(intval($_REQUEST['id']));
					} else {
						$result = $results_handler->create();
					}
					
					$GLOBALS['xoopsTpl']->assign('form', $result->getForm());
					$GLOBALS['xoopsTpl']->assign('php_self', $_SERVER['PHP_SELF']);
					include($GLOBALS['xoops']->path('/footer.php'));
					exit(0);
					
				case "edit":
					
					$xoopsOption['template_main'] = 'benchmark_edit_result.html';
					include($GLOBALS['xoops']->path('/header.php'));
										
					$results_handler =& xoops_getmodulehandler('results', $GLOBAL['xoopsModule']->getVar('dirname'));
					if (isset($_REQUEST['id'])) {
						$result = $results_handler->get(intval($_REQUEST['id']));
					} else {
						$result = $results_handler->create();
					}
					
					$GLOBALS['xoopsTpl']->assign('form', $result->getForm());
					$GLOBALS['xoopsTpl']->assign('php_self', $_SERVER['PHP_SELF']);
					include($GLOBALS['xoops']->path('/footer.php'));
					exit(0);
					
					break;
				case "save":
					
					$results_handler =& xoops_getmodulehandler('results', $GLOBAL['xoopsModule']->getVar('dirname'));
					$id=0;
					if ($id=intval($_REQUEST['id'])) {
						$result = $results_handler->get($id);
					} else {
						$result = $results_handler->create();
					}
					$result->setVars($_POST[$id]);
					if (!empty($_POST[$id]['begin']))
						$result->setVar('begin', strtotime($_POST[$id]['begin']));
										
					if (!$id=$results_handler->insert($campaign)) {
						redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_RESULT_FAILEDTOSAVE);
						exit(0);
					} else {
						redirect_header('index.php?op='.$op.'&fct=edit&id='.$id, 10, _MN_MSG_RESULT_SAVEDOKEY);
						exit(0);
					}
					break;
				case "savelist":
					
					$results_handler =& xoops_getmodulehandler('results', $GLOBAL['xoopsModule']->getVar('dirname'));
					foreach($_REQUEST['id'] as $id) {
						$result = $results_handler->get($id);
						$result->setVars($_POST[$id]);
						if (!empty($_POST[$id]['begin']))
							$result->setVar('begin', strtotime($_POST[$id]['begin']));
							
						if (!$results_handler->insert($result)) {
							redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_RESULT_FAILEDTOSAVE);
							exit(0);
						} 
					}
					redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_RESULT_SAVEDOKEY);
					exit(0);
					break;				
				case "delete":	
								
					$results_handler =& xoops_getmodulehandler('results', $GLOBAL['xoopsModule']->getVar('dirname'));
					$results_handler =& xoops_getmodulehandler('results', $GLOBAL['xoopsModule']->getVar('dirname'));
					$tables_handler =& xoops_getmodulehandler('tables', $GLOBAL['xoopsModule']->getVar('dirname'));
					$id=0;
					if (isset($_POST['id'])&&$id=intval($_POST['id'])) {
						$result = $results_handler->get($id);
						if (!$results_handler->delete($result)) {
							redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_RESULT_FAILEDTODELETE);
							exit(0);
						} else {
							$criteria = new Criteria('tid', $result->getVar('tid'));
							$results_handler->deleteAll($criteria);
							$tables_handler->deleteAll($criteria);
							redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_RESULT_DELETED);
							exit(0);
						}
					} else {
						include($GLOBALS['xoops']->path('/header.php'));
						$result = $results_handler->get(intval($_REQUEST['id']));
						xoops_confirm(array('id'=>$_REQUEST['id'], 'op'=>$_REQUEST['op'], 'fct'=>$_REQUEST['fct'], 'limit'=>$_REQUEST['limit'], 'start'=>$_REQUEST['start'], 'order'=>$_REQUEST['order'], 'sort'=>$_REQUEST['sort'], 'filter'=>$_REQUEST['filter']), 'index.php', sprintf(_MN_MSG_RESULT_DELETE, $result->getVar('name')));
						include($GLOBALS['xoops']->path('/footer.php'));
						exit(0);
					
					}
					break;*/
			}
			break;

		case "fields":	
			switch ($fct)
			{
				default:
				case "list":				
					$xoopsOption['template_main'] = 'benchmark_list_field.html';
					include($GLOBALS['xoops']->path('/header.php'));
					
					$fields_handler =& xoops_getmodulehandler('fields', $GLOBAL['xoopsModule']->getVar('dirname'));

					$criteria = $fields_handler->getFilterCriteria($filter);
					$ttl = $fields_handler->getCount($criteria);
					$sort = !empty($_REQUEST['sort'])?''.$_REQUEST['sort'].'':'created';
					
					$pagenav = new XoopsPageNav($ttl, $limit, $start, 'start', 'limit='.$limit.'&sort='.$sort.'&order='.$order.'&op='.$op.'&fct='.$fct.'&filter='.$filter);
					$GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav());
			
					foreach (array(	'fid','name','type','required','primarykey','key','create_sql','alter_sql','size','recipricol','alter_type','created','updated','actioned') as $id => $key) {
						$GLOBALS['xoopsTpl']->assign(strtolower(str_replace('-','_',$key).'_th'), '<a href="'.$_SERVER['PHP_SELF'].'?start='.$start.'&limit='.$limit.'&sort='.str_replace('_','-',$key).'&order='.((str_replace('_','-',$key)==$sort)?($order=='DESC'?'ASC':'DESC'):$order).'&op='.$op.'&filter='.$filter.'">'.(defined('_MN_BENCHMARK_TH_'.strtoupper(str_replace('-','_',$key)))?constant('_MN_BENCHMARK_TH_'.strtoupper(str_replace('-','_',$key))):'_MN_BENCHMARK_TH_'.strtoupper(str_replace('-','_',$key))).'</a>');
						$GLOBALS['xoopsTpl']->assign('filter_'.strtolower(str_replace('-','_',$key)).'_th', $fields_handler->getFilterForm($filter, $key, $sort, $op, $fct));
					}
					
					$GLOBALS['xoopsTpl']->assign('limit', $limit);
					$GLOBALS['xoopsTpl']->assign('start', $start);
					$GLOBALS['xoopsTpl']->assign('order', $order);
					$GLOBALS['xoopsTpl']->assign('sort', $sort);
					$GLOBALS['xoopsTpl']->assign('filter', $filter);
					$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['xoopsModuleConfig']);
										
					$criteria->setStart($start);
					$criteria->setLimit($limit);
					$criteria->setSort('`'.$sort.'`');
					$criteria->setOrder($order);
						
					$fields = $fields_handler->getObjects($criteria, true);
					foreach($fields as $tid => $field) {
						$GLOBALS['xorTpl']->append('fields', $field->toArray());
					}
					$GLOBALS['xoopsTpl']->assign('form', tweetbomb_FIELD_get_form(false));
					$GLOBALS['xoopsTpl']->assign('php_self', $_SERVER['PHP_SELF']);
					include($GLOBALS['xoops']->path('/footer.php'));
					exit(0);
					
					break;		
					
				case "new":
					
					$xoopsOption['template_main'] = 'benchmark_create_field.html';
					include($GLOBALS['xoops']->path('/header.php'));
										
					$fields_handler =& xoops_getmodulehandler('fields', $GLOBAL['xoopsModule']->getVar('dirname'));
					if (isset($_REQUEST['id'])) {
						$field = $fields_handler->get(intval($_REQUEST['id']));
					} else {
						$field = $fields_handler->create();
					}
					
					$GLOBALS['xoopsTpl']->assign('form', $field->getForm());
					$GLOBALS['xoopsTpl']->assign('php_self', $_SERVER['PHP_SELF']);
					include($GLOBALS['xoops']->path('/footer.php'));
					exit(0);
					
				case "edit":
					
					$xoopsOption['template_main'] = 'benchmark_edit_field.html';
					include($GLOBALS['xoops']->path('/header.php'));
										
					$fields_handler =& xoops_getmodulehandler('fields', $GLOBAL['xoopsModule']->getVar('dirname'));
					if (isset($_REQUEST['id'])) {
						$field = $fields_handler->get(intval($_REQUEST['id']));
					} else {
						$field = $fields_handler->create();
					}
					
					$GLOBALS['xoopsTpl']->assign('form', $field->getForm());
					$GLOBALS['xoopsTpl']->assign('php_self', $_SERVER['PHP_SELF']);
					include($GLOBALS['xoops']->path('/footer.php'));
					exit(0);
					
					break;
				case "save":
					
					$fields_handler =& xoops_getmodulehandler('fields', $GLOBAL['xoopsModule']->getVar('dirname'));
					$id=0;
					if ($id=intval($_REQUEST['id'])) {
						$field = $fields_handler->get($id);
					} else {
						$field = $fields_handler->create();
					}
					$field->setVars($_POST[$id]);
					if (!empty($_POST[$id]['begin']))
						$field->setVar('begin', strtotime($_POST[$id]['begin']));
										
					if (!$id=$fields_handler->insert($campaign)) {
						redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_FIELD_FAILEDTOSAVE);
						exit(0);
					} else {
						redirect_header('index.php?op='.$op.'&fct=edit&id='.$id, 10, _MN_MSG_FIELD_SAVEDOKEY);
						exit(0);
					}
					break;
				case "savelist":
					
					$fields_handler =& xoops_getmodulehandler('fields', $GLOBAL['xoopsModule']->getVar('dirname'));
					foreach($_REQUEST['id'] as $id) {
						$field = $fields_handler->get($id);
						$field->setVars($_POST[$id]);
						if (!empty($_POST[$id]['begin']))
							$field->setVar('begin', strtotime($_POST[$id]['begin']));
							
						if (!$fields_handler->insert($field)) {
							redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_FIELD_FAILEDTOSAVE);
							exit(0);
						} 
					}
					redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_FIELD_SAVEDOKEY);
					exit(0);
					break;				
				case "delete":	
					
										
					$fields_handler =& xoops_getmodulehandler('fields', $GLOBAL['xoopsModule']->getVar('dirname'));
					$results_handler =& xoops_getmodulehandler('results', $GLOBAL['xoopsModule']->getVar('dirname'));
					$tables_handler =& xoops_getmodulehandler('tables', $GLOBAL['xoopsModule']->getVar('dirname'));
					$id=0;
					if (isset($_POST['id'])&&$id=intval($_POST['id'])) {
						$field = $fields_handler->get($id);
						if (!$fields_handler->delete($field)) {
							redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_FIELD_FAILEDTODELETE);
							exit(0);
						} else {
							$criteria = new Criteria('tid', $field->getVar('tid'));
							$results_handler->deleteAll($criteria);
							$tables_handler->deleteAll($criteria);
							redirect_header('index.php?op='.$op.'&fct=list&limit='.$limit.'&start='.$start.'&order='.$order.'&sort='.$sort.'&filter='.$filter, 10, _MN_MSG_FIELD_DELETED);
							exit(0);
						}
					} else {
						include($GLOBALS['xoops']->path('/header.php'));
						$field = $fields_handler->get(intval($_REQUEST['id']));
						xoops_confirm(array('id'=>$_REQUEST['id'], 'op'=>$_REQUEST['op'], 'fct'=>$_REQUEST['fct'], 'limit'=>$_REQUEST['limit'], 'start'=>$_REQUEST['start'], 'order'=>$_REQUEST['order'], 'sort'=>$_REQUEST['sort'], 'filter'=>$_REQUEST['filter']), 'index.php', sprintf(_MN_MSG_FIELD_DELETE, $field->getVar('name')));
						include($GLOBALS['xoops']->path('/footer.php'));
						exit(0);
					}
					break;
			}
			break;
	}
	
?>