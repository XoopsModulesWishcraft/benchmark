<?php

	require ('header.php');
	
	if ($currently = XoopsCache::read('benchmark_currently_doing_test'))
		exit;
	else 
		XoopsCache::write('benchmark_currently_doing_test', microtime(true), 1200);
		
	if (!$tid = XoopsCache::read('benchmark_current_test')) {
		$criteria = new CriteriaCompo(new Criteria('ended', '0', '='));
		$criteria->add(new Criteria('started', '0', '='));
		$criteria->add(new Criteria('begin', time(), '<'));
		$criteria->add(new Criteria('ended', '0', '='), "OR");
		$tests = $tests_handler->getObjects($criteria, false);
		
		if (is_object($tests[0])) {
			$tid = $tests[0]->getVar('tid');
			XoopsCache::write('benchmark_current_test', $tid);
			$tests[0]->setVar('started', time());
			$tests_handler->insert($tests[0], true);
			
			xoops_loadLanguage('email', $GLOBALS['xoopsModule']->getVar('dirname'));
			
			$xoopsMailer =& getMailer();
			$xoopsMailer->setHTML(true);
			$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/benchmark/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
			$xoopsMailer->setTemplate('benchmark_start_test.html');
			$xoopsMailer->setSubject(sprintf(_EMAIL_BENCHMARK_STARTOFCRON, $tests[0]->getVar('name'), date(_DATESTRING)));
			
			foreach(explode('|',$GLOBALS['xoopsModuleConfig']['email_addresses']) as $emailaddy)
				$xoopsMailer->setToEmails($emailaddy);
			
			$xoopsMailer->assign("SITEURL", XOOPS_URL);
			$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
			$xoopsMailer->assign("NAME", $tests[0]->getVar('name'));
			$xoopsMailer->assign("TESTING", $tests[0]->getVar('testing'));
			$xoopsMailer->assign("NOTE", $tests[0]->getVar('note'));
			$xoopsMailer->assign("STAGE", constant($tests[0]->getVar('stage').'_EMAIL'));
			$xoopsMailer->assign("TEST", constant($tests[0]->getVar('test').'_EMAIL'));
			$xoopsMailer->assign("PLATFORM", constant($tests[0]->getVar('platform').'_EMAIL'));
			$xoopsMailer->assign("BEGIN", ($tests[0]->getVar('begin')==0?'--':date(_DATESTRING, $tests[0]->getVar('begin'))));
			$xoopsMailer->assign("STARTED", ($tests[0]->getVar('started')==0?'--':date(_DATESTRING, $tests[0]->getVar('started'))));
			$xoopsMailer->assign("CREATED", ($tests[0]->getVar('created')==0?'--':date(_DATESTRING, $tests[0]->getVar('created'))));
			$xoopsMailer->assign("UPDATED", ($tests[0]->getVar('updated')==0?'--':date(_DATESTRING, $tests[0]->getVar('updated'))));
	
			if ($GLOBALS['xoopsModuleConfig']['send_email'])
				if(!$xoopsMailer->send() ){
					xoops_error($xoopsMailer->getErrors(true), _EMAIL_BENCHMARK_SENDERROR);
				}
				
		} else {
			$criteria = new CriteriaCompo(new Criteria('test', '_MI_BENCHMARK_FINISHED', '!='));
			$tests = $tests_handler->getObjects($criteria, false);
			if (is_object($tests[0])) {
				$tid = $tests[0]->getVar('tid');
				XoopsCache::write('benchmark_current_test', $tid);
				$tests[0]->setVar('started', time());
				$tests_handler->insert($tests[0], true);
				
				xoops_loadLanguage('email', $GLOBALS['xoopsModule']->getVar('dirname'));
				
				$xoopsMailer =& getMailer();
				$xoopsMailer->setHTML(true);
				$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/benchmark/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
				$xoopsMailer->setTemplate('benchmark_start_test.html');
				$xoopsMailer->setSubject(sprintf(_EMAIL_BENCHMARK_STARTOFCRON, $tests[0]->getVar('name'), date(_DATESTRING)));
				
				foreach(explode('|',$GLOBALS['xoopsModuleConfig']['email_addresses']) as $emailaddy)
					$xoopsMailer->setToEmails($emailaddy);
				
				$xoopsMailer->assign("SITEURL", XOOPS_URL);
				$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
				$xoopsMailer->assign("NAME", $tests[0]->getVar('name'));
				$xoopsMailer->assign("TESTING", $tests[0]->getVar('testing'));
				$xoopsMailer->assign("NOTE", $tests[0]->getVar('note'));
				$xoopsMailer->assign("STAGE", constant($tests[0]->getVar('stage').'_EMAIL'));
				$xoopsMailer->assign("TEST", constant($tests[0]->getVar('test').'_EMAIL'));
				$xoopsMailer->assign("PLATFORM", constant($tests[0]->getVar('platform').'_EMAIL'));
				$xoopsMailer->assign("BEGIN", ($tests[0]->getVar('begin')==0?'--':date(_DATESTRING, $tests[0]->getVar('begin'))));
				$xoopsMailer->assign("STARTED", ($tests[0]->getVar('started')==0?'--':date(_DATESTRING, $tests[0]->getVar('started'))));
				$xoopsMailer->assign("CREATED", ($tests[0]->getVar('created')==0?'--':date(_DATESTRING, $tests[0]->getVar('created'))));
				$xoopsMailer->assign("UPDATED", ($tests[0]->getVar('updated')==0?'--':date(_DATESTRING, $tests[0]->getVar('updated'))));
		
				if ($GLOBALS['xoopsModuleConfig']['send_email'])
					if(!$xoopsMailer->send() ){
						xoops_error($xoopsMailer->getErrors(true), _EMAIL_BENCHMARK_SENDERROR);
					}
			} else {
				echo 'Nothing to do: '.date('Y-m-d H:i:s').'<br/>';
				XoopsCache::delete('benchmark_currently_doing_test');
				exit(0);
			}
		}
	}
	
	set_time_limit(3600);
	
	$test = $tests_handler->get($tid);
			
	$engines = $GLOBALS['xoopsModuleConfig']['engines'];
	if (count($engines)==0)
		$engines = array('INNODB');
		
	$charset = $GLOBALS['xoopsModuleConfig']['charset'];
	if (empty($charset))
		$charset = 'utf8';

	$fields = $GLOBALS['xoopsModuleConfig']['fields_num'];
	if (intval($fields)==0)
		$fields = 18;
		
	$GLOBALS['timer']['boot']['end'] = microtime(true);
	
	if (is_object($test)) {
		echo 'Test: '.$test->getVar('test').'<br/>';
		echo 'Stage: '.$test->getVar('stage').'<br/>';
		echo 'Next Test: '.benchmark_getNextTest($test).'<br/>';
		echo 'Started: '.date('Y-m-d H:i:s').'<br/>';
		if (!$test->isNew()) {
			switch ($test->getVar('test')) {
				case '_MI_BENCHMARK_WAIT':
					switch ($test->getVar('stage')) {
						case '_MI_BENCHMARK_WAIT':
						case '_MI_BENCHMARK_PREPARED':
						case '_MI_BENCHMARK_TESTING':
						case '_MI_BENCHMARK_CLEANUP':
						case '_MI_BENCHMARK_FINISHED':
							$test->setVar('begin', time());
							$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							$test->setVar('test', benchmark_getNextTest($test));
							break;
					}
					break;
				case '_MI_BENCHMARK_CREATE':
					switch ($test->getVar('stage')) {
						case '_MI_BENCHMARK_WAIT':
							$test->setVar('stage', '_MI_BENCHMARK_TESTING');
							break;
						case '_MI_BENCHMARK_PREPARED':
							$test->setVar('begin', time());
							$test->setVar('stage', '_MI_BENCHMARK_WAIT');
							$num = floor($GLOBALS['xoopsModuleConfig']['create_table_num']/count($engines));
							$sql=array();
							$tbids=array();
							foreach($engines as $engine) {
								for($i=0;$i<$num;$i++) {
									$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $charset);
									$sql[$table->getVar('tbid')] = $table->getCreateTable();
									$tbids[$table->getVar('tbid')] = $table->getVar('tbid'); 
									unset($table);
								}
							}
							foreach($tbids as $tbid) {
								$table = $tables_handler->get($tbid);
								$table->setVar('create', time());
								$table->setVar('prepared', time());
								$tables_handler->insert($table);
								unset($table);
							}		
							$test->setVar('tbids_create', $tbids);	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $sql);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=0;
								foreach($data as $tbid => $sqla) {
									foreach($sqla as $instruct => $sqlb) {	
										foreach($sqlb as $tbidb => $sqlc) {
											foreach($sqlc as $name => $question) {
												$GLOBALS['results'][$test->getVar('test')][0]['counter']++;
												$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']=microtime(true);
												$GLOBALS['xoopsDB']->queryF($question);
												$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']=microtime(true);
												$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]=strlen($question);
												$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['records'][$tbid]=0;
												$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['number'][$tbid]=$GLOBALS['results'][$test->getVar('test')][0]['counter'];
												$GLOBALS['results'][$test->getVar('test')][0]['took'] = $GLOBALS['results'][$test->getVar('test')][0]['took'] + ($GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']-$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']);		
											}
										}
									}
								}
							}
							$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_create')).')', 'IN'), true);
							$results_handler->saveResults($test, $GLOBALS['results'], $tables);
							$test->setVar('took_create_seconds', $GLOBALS['results'][$test->getVar('test')][0]['took']);
							$test->setVar('tests_ran_create', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
							$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							break;
						case '_MI_BENCHMARK_CLEANUP':
							$criteria = new Criteria('tbid', '('.implode(',', $test->getVar('tbids_create')).')', 'IN');
							$tables = $tables_handler->getObjects($criteria, true);
							foreach($tables as $tbid => $table) {
								$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name'))."`");
								$table->setVar('dropped', time());
								$tables_handler->insert($table);
							}
							$test->setVar('stage', '_MI_BENCHMARK_FINISHED');
							$test->getHistory();
							break;
						case '_MI_BENCHMARK_FINISHED':
							$xoopsMailer =& getMailer();
							$xoopsMailer->setHTML(true);
							$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/benchmark/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
							$xoopsMailer->setTemplate('benchmark_create_finished.html');
							$xoopsMailer->setSubject(sprintf(_EMAIL_BENCHMARK_TASK_ENDED, constant($test->getVar('test').'_EMAIL'), $test->getVar('name'), date(_DATESTRING)));
							
							foreach(explode('|',$GLOBALS['xoopsModuleConfig']['email_addresses']) as $emailaddy)
								$xoopsMailer->setToEmails($emailaddy);
							
							$xoopsMailer->assign("SITEURL", XOOPS_URL);
							$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
							$xoopsMailer->assign("NAME", $test->getVar('name'));
							$xoopsMailer->assign("TESTING", $test->getVar('testing'));
							$xoopsMailer->assign("NOTE", $test->getVar('note'));
							$xoopsMailer->assign("STAGE", constant($test->getVar('stage').'_EMAIL'));
							$xoopsMailer->assign("TEST", constant($test->getVar('test').'_EMAIL'));
							$xoopsMailer->assign("NEXTTEST", constant(benchmark_getNextTest($test).'_EMAIL'));
							$xoopsMailer->assign("PLATFORM", constant($test->getVar('platform').'_EMAIL'));
							$xoopsMailer->assign("BEGIN", ($test->getVar('begin')==0?'--':date(_DATESTRING, $test->getVar('begin'))));
							$xoopsMailer->assign("STARTED", ($test->getVar('started')==0?'--':date(_DATESTRING, $test->getVar('started'))));
							$xoopsMailer->assign("CREATED", ($test->getVar('created')==0?'--':date(_DATESTRING, $test->getVar('created'))));
							$xoopsMailer->assign("UPDATED", ($test->getVar('updated')==0?'--':date(_DATESTRING, $test->getVar('updated'))));
							$xoopsMailer->assign("ENDED", ($test->getVar('ended')==0?'--':date(_DATESTRING, $test->getVar('ended'))));
							
							foreach($test->toArray(true) as $field => $value) {
								if (!in_array($field, array('name', 'testing', 'note', 'stage', 'test', 'platform', 'begin', 'started', 'ended', 'created', 'updated','form', 'table'))) {
									$xoopsMailer->assign(strtoupper($field), $value);
								}
							}
							
							if ($GLOBALS['xoopsModuleConfig']['send_email'])
								if(!$xoopsMailer->send() ){
									xoops_error($xoopsMailer->getErrors(true), _EMAIL_BENCHMARK_SENDERROR);
								}
		
							$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							$test->setVar('test', benchmark_getNextTest($test));
							break;
					}
					echo 'Stage A Finished: '.date('Y-m-d H:i:s').'<br/>';
					break;
				case '_MI_BENCHMARK_SELECT':
						switch ($test->getVar('stage')) {
						case '_MI_BENCHMARK_WAIT':
							$test->setVar('stage', '_MI_BENCHMARK_TESTING');
							break;
						case '_MI_BENCHMARK_PREPARED':
							$test->setVar('begin', time());
							$test->setVar('stage', '_MI_BENCHMARK_WAIT');
							$tbids=array();
							foreach($engines as $engine) {
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $charset);
								benchmark_chainedSQL($table->getCreateTable());
								for($i=0;$i<$GLOBALS['xoopsModuleConfig']['select_records_num'];$i=$i+10) {
									benchmark_chainedSQL($table->getInsertIntoTable(10));
								}
								$tbids[$table->getVar('tbid')] = $table->getVar('tbid');
							}
							foreach($tbids as $tbid) {
								$table = $tables_handler->get($tbid);
								$table->setVar('create', time());
								$table->setVar('prepared', time());
								$tables_handler->insert($table);
							}		
							$test->setVar('tbids_select', $tbids);	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$criteria = new Criteria('tbid', '('.implode(',', $data).')', 'IN');
								$tables = $tables_handler->getObjects($criteria, true);
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=0;
								$criteria = new Criteria('1','1');
								$criteria->setLimit($GLOBALS['xoopsModuleConfig']['select_records_num']);
								foreach($tables as $tbid => $table) {
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									$records = $_tHandler->getCount($criteria);
									for($c=0;$c<$GLOBALS['xoopsModuleConfig']['select_tests_num'];$c++) {
										$GLOBALS['results'][$test->getVar('test')][0]['counter']++;
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']=microtime(true);
										$records = $_tHandler->getObjects($criteria, true, false);
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']=microtime(true);		
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]=strlen(implode('', $records));
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['records'][$tbid]=count($records);
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['number'][$tbid]=$GLOBALS['results'][$test->getVar('test')][0]['counter'];
										$GLOBALS['results'][$test->getVar('test')][0]['took'] = $GLOBALS['results'][$test->getVar('test')][0]['took'] + ($GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']-$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']); 
									}
								}
							}
							$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_select')).')', 'IN'), true);
							$test->setVar('took_select_seconds', $GLOBALS['results'][$test->getVar('test')][0]['took']);
							$test->setVar('tests_ran_select', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
							$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							$results_handler->saveResults($test, $GLOBALS['results'], $tables);
							break;
						case '_MI_BENCHMARK_CLEANUP':
							$criteria = new Criteria('tbid', '('.implode(',', $test->getVar('tbids_select')).')', 'IN');
							$tables = $tables_handler->getObjects($criteria, true);
							foreach($tables as $tbid => $table) {
								$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name'))."`");
								$table->setVar('dropped', time());
								$tables_handler->insert($table);
							}
							$test->setVar('stage', '_MI_BENCHMARK_FINISHED');
							$test->getHistory();
							break;
						case '_MI_BENCHMARK_FINISHED':
							$xoopsMailer =& getMailer();
							$xoopsMailer->setHTML(true);
							$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/benchmark/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
							$xoopsMailer->setTemplate('benchmark_select_finished.html');
							$xoopsMailer->setSubject(sprintf(_EMAIL_BENCHMARK_TASK_ENDED, constant($test->getVar('test').'_EMAIL'), $test->getVar('name'), date(_DATESTRING)));
							
							foreach(explode('|',$GLOBALS['xoopsModuleConfig']['email_addresses']) as $emailaddy)
								$xoopsMailer->setToEmails($emailaddy);
							
							$xoopsMailer->assign("SITEURL", XOOPS_URL);
							$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
							$xoopsMailer->assign("NAME", $test->getVar('name'));
							$xoopsMailer->assign("TESTING", $test->getVar('testing'));
							$xoopsMailer->assign("NOTE", $test->getVar('note'));
							$xoopsMailer->assign("STAGE", constant($test->getVar('stage').'_EMAIL'));
							$xoopsMailer->assign("TEST", constant($test->getVar('test').'_EMAIL'));
							$xoopsMailer->assign("NEXTTEST", constant(benchmark_getNextTest($test).'_EMAIL'));
							$xoopsMailer->assign("PLATFORM", constant($test->getVar('platform').'_EMAIL'));
							$xoopsMailer->assign("BEGIN", ($test->getVar('begin')==0?'--':date(_DATESTRING, $test->getVar('begin'))));
							$xoopsMailer->assign("STARTED", ($test->getVar('started')==0?'--':date(_DATESTRING, $test->getVar('started'))));
							$xoopsMailer->assign("CREATED", ($test->getVar('created')==0?'--':date(_DATESTRING, $test->getVar('created'))));
							$xoopsMailer->assign("UPDATED", ($test->getVar('updated')==0?'--':date(_DATESTRING, $test->getVar('updated'))));
							$xoopsMailer->assign("ENDED", ($test->getVar('ended')==0?'--':date(_DATESTRING, $test->getVar('ended'))));
							
							foreach($test->toArray(true) as $field => $value) {
								if (!in_array($field, array('name', 'testing', 'note', 'stage', 'test', 'platform', 'begin', 'started', 'ended', 'created', 'updated'))) {
									$xoopsMailer->assign(strtoupper($field), $value);
								}
							}
							
							if ($GLOBALS['xoopsModuleConfig']['send_email'])
								if(!$xoopsMailer->send() ){
									xoops_error($xoopsMailer->getErrors(true), _EMAIL_BENCHMARK_SENDERROR);
								}
												
							$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							$test->setVar('test', benchmark_getNextTest($test));
							break;
					}
					echo 'Stage B Finished: '.date('Y-m-d H:i:s').'<br/>';
					break;
				case '_MI_BENCHMARK_INSERT':
						switch ($test->getVar('stage')) {
						case '_MI_BENCHMARK_WAIT':
							$test->setVar('stage', '_MI_BENCHMARK_TESTING');
							break;
						case '_MI_BENCHMARK_PREPARED':
							$test->setVar('begin', time());
							$test->setVar('stage', '_MI_BENCHMARK_WAIT');
							$sql=array();
							$tbids=array();
							foreach($engines as $engine) {
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $charset);
								benchmark_chainedSQL($table->getCreateTable());
								$data[$table->getVar('tbid')] = $table->getInsertArray($GLOBALS['xoopsModuleConfig']['insert_records_num']);
								$tbids[$table->getVar('tbid')] = $table->getVar('tbid');
								 
							}
							foreach($tbids as $tbid) {
								$table = $tables_handler->get($tbid);
								$table->setVar('create', time());
								$table->setVar('prepared', time());
								$tables_handler->insert($table);
							}		
							$test->setVar('tbids_insert', $tbids);	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $data);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=0;
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									for($c=0;$c<$GLOBALS['xoopsModuleConfig']['insert_tests_num'];$c++) {
										set_time_limit($GLOBALS['execution']=$GLOBALS['execution']+20);
										$GLOBALS['results'][$test->getVar('test')][0]['counter']++;
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']=microtime(true);
										foreach($var['insertasobject'][$tbid] as $r => $records) {
											$object = $_tHandler->create(true);
											$object->setVars($records);
											$_tHandler->insert($object);
											$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]=$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]+strlen(implode('', $records));
										}
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']=microtime(true);		
										$GLOBALS['results'][$test->getVar('test')][0]['took'] = $GLOBALS['results'][$test->getVar('test')][0]['took'] + ($GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']-$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']);
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['records'][$tbid]=count($var['insertasobject'][$tbid]);
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['number'][$tbid]=$GLOBALS['results'][$test->getVar('test')][0]['counter'];
										$GLOBALS['xoopsDB']->queryF('TRUNCATE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name')).'`');
									}
								}
							}
							$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_insert')).')', 'IN'), true);
							$results_handler->saveResults($test, $GLOBALS['results'], $tables);
							$test->setVar('took_insert_seconds', $GLOBALS['results'][$test->getVar('test')][0]['took']);
							$test->setVar('tests_ran_insert', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
							$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							break;
						case '_MI_BENCHMARK_CLEANUP':
							$criteria = new Criteria('tbid', '('.implode(',', $test->getVar('tbids_select')).')', 'IN');
							$tables = $tables_handler->getObjects($criteria, true);
							foreach($tables as $tbid => $table) {
								$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name'))."`");
								$table->setVar('dropped', time());
								$tables_handler->insert($table);
							}
							$test->setVar('stage', '_MI_BENCHMARK_FINISHED');
							$test->getHistory();
							break;
						case '_MI_BENCHMARK_FINISHED':
							$xoopsMailer =& getMailer();
							$xoopsMailer->setHTML(true);
							$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/benchmark/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
							$xoopsMailer->setTemplate('benchmark_insert_finished.html');
							$xoopsMailer->setSubject(sprintf(_EMAIL_BENCHMARK_TASK_ENDED, constant($test->getVar('test').'_EMAIL'), $test->getVar('name'), date(_DATESTRING)));
							
							foreach(explode('|',$GLOBALS['xoopsModuleConfig']['email_addresses']) as $emailaddy)
								$xoopsMailer->setToEmails($emailaddy);
							
							$xoopsMailer->assign("SITEURL", XOOPS_URL);
							$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
							$xoopsMailer->assign("NAME", $test->getVar('name'));
							$xoopsMailer->assign("TESTING", $test->getVar('testing'));
							$xoopsMailer->assign("NOTE", $test->getVar('note'));
							$xoopsMailer->assign("STAGE", constant($test->getVar('stage').'_EMAIL'));
							$xoopsMailer->assign("TEST", constant($test->getVar('test').'_EMAIL'));
							$xoopsMailer->assign("NEXTTEST", constant(benchmark_getNextTest($test).'_EMAIL'));
							$xoopsMailer->assign("PLATFORM", constant($test->getVar('platform').'_EMAIL'));
							$xoopsMailer->assign("BEGIN", ($test->getVar('begin')==0?'--':date(_DATESTRING, $test->getVar('begin'))));
							$xoopsMailer->assign("STARTED", ($test->getVar('started')==0?'--':date(_DATESTRING, $test->getVar('started'))));
							$xoopsMailer->assign("CREATED", ($test->getVar('created')==0?'--':date(_DATESTRING, $test->getVar('created'))));
							$xoopsMailer->assign("UPDATED", ($test->getVar('updated')==0?'--':date(_DATESTRING, $test->getVar('updated'))));
							$xoopsMailer->assign("ENDED", ($test->getVar('ended')==0?'--':date(_DATESTRING, $test->getVar('ended'))));
							
							foreach($test->toArray(true) as $field => $value) {
								if (!in_array($field, array('name', 'testing', 'note', 'stage', 'test', 'platform', 'begin', 'started', 'ended', 'created', 'updated'))) {
									$xoopsMailer->assign(strtoupper($field), $value);
								}
							}
							
							if ($GLOBALS['xoopsModuleConfig']['send_email'])
								if(!$xoopsMailer->send() ){
									xoops_error($xoopsMailer->getErrors(true), _EMAIL_BENCHMARK_SENDERROR);
								}
												
							$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							$test->setVar('test', benchmark_getNextTest($test));
							break;
					}
					echo 'Stage C Finished: '.date('Y-m-d H:i:s').'<br/>';
					break;
				case '_MI_BENCHMARK_UPDATE':
						switch ($test->getVar('stage')) {
						case '_MI_BENCHMARK_WAIT':
							$test->setVar('stage', '_MI_BENCHMARK_TESTING');
							break;
						case '_MI_BENCHMARK_PREPARED':
							$test->setVar('begin', time());
							$test->setVar('stage', '_MI_BENCHMARK_WAIT');
							$sql=array();
							$tbids=array();
							foreach($engines as $engine) {
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $charset);
								benchmark_chainedSQL($table->getCreateTable());
								for($i=0;$i<$GLOBALS['xoopsModuleConfig']['update_records_num'];$i=$i+10) {
									benchmark_chainedSQL($table->getInsertIntoTable(10));
								}
								$tbids[$table->getVar('tbid')] = $table->getVar('tbid');
							}
			
							foreach($tbids as $tbid) {
								$table = $tables_handler->get($tbid);
								$table->setVar('create', time());
								$table->setVar('prepared', time());
								$tables_handler->insert($table);
							}		
							$test->setVar('tbids_update', $tbids);	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=0;
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									$records = $_tHandler->getCount(NULL);
									for($c=0;$c<$GLOBALS['xoopsModuleConfig']['update_tests_num'];$c++) {
										$updatedata = $table->getUpdateArray($records);
										$objects = $_tHandler->getObjects(NULL, false);
										$GLOBALS['results'][$test->getVar('test')][0]['counter']++;
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']=microtime(true);
										foreach($objects as $r => $object) {
											$object->setVars($updatedata['updateasobject'][$tbid][$r]);
											$_tHandler->insert($object, true);
											$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]=$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]+strlen(implode('', $records));
										}
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']=microtime(true);		
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['records'][$tbid]=$records;
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['number'][$tbid]=$GLOBALS['results'][$test->getVar('test')][0]['counter'];
										$GLOBALS['results'][$test->getVar('test')][0]['took'] = $GLOBALS['results'][$test->getVar('test')][0]['took'] + ($GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']-$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']);
									}
								}
							}
							$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_update')).')', 'IN'), true);
							$results_handler->saveResults($test, $GLOBALS['results'], $tables);
							$test->setVar('took_update_seconds', $GLOBALS['results'][$test->getVar('test')][0]['took']);
							$test->setVar('tests_ran_update', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
							$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							break;
						case '_MI_BENCHMARK_CLEANUP':
							$criteria = new Criteria('tbid', '('.implode(',', $test->getVar('tbids_update')).')', 'IN');
							$tables = $tables_handler->getObjects($criteria, true);
							foreach($tables as $tbid => $table) {
								$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name'))."`");
								$table->setVar('dropped', time());
								$tables_handler->insert($table);
							}
							$test->setVar('stage', '_MI_BENCHMARK_FINISHED');
							$test->getHistory();
							break;
						case '_MI_BENCHMARK_FINISHED':
							$xoopsMailer =& getMailer();
							$xoopsMailer->setHTML(true);
							$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/benchmark/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
							$xoopsMailer->setTemplate('benchmark_update_finished.html');
							$xoopsMailer->setSubject(sprintf(_EMAIL_BENCHMARK_TASK_ENDED, constant($test->getVar('test').'_EMAIL'), $test->getVar('name'), date(_DATESTRING)));
							
							foreach(explode('|',$GLOBALS['xoopsModuleConfig']['email_addresses']) as $emailaddy)
								$xoopsMailer->setToEmails($emailaddy);
							
							$xoopsMailer->assign("SITEURL", XOOPS_URL);
							$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
							$xoopsMailer->assign("NAME", $test->getVar('name'));
							$xoopsMailer->assign("TESTING", $test->getVar('testing'));
							$xoopsMailer->assign("NOTE", $test->getVar('note'));
							$xoopsMailer->assign("STAGE", constant($test->getVar('stage').'_EMAIL'));
							$xoopsMailer->assign("TEST", constant($test->getVar('test').'_EMAIL'));
							$xoopsMailer->assign("NEXTTEST", constant(benchmark_getNextTest($test).'_EMAIL'));
							$xoopsMailer->assign("PLATFORM", constant($test->getVar('platform').'_EMAIL'));
							$xoopsMailer->assign("BEGIN", ($test->getVar('begin')==0?'--':date(_DATESTRING, $test->getVar('begin'))));
							$xoopsMailer->assign("STARTED", ($test->getVar('started')==0?'--':date(_DATESTRING, $test->getVar('started'))));
							$xoopsMailer->assign("CREATED", ($test->getVar('created')==0?'--':date(_DATESTRING, $test->getVar('created'))));
							$xoopsMailer->assign("UPDATED", ($test->getVar('updated')==0?'--':date(_DATESTRING, $test->getVar('updated'))));
							$xoopsMailer->assign("ENDED", ($test->getVar('ended')==0?'--':date(_DATESTRING, $test->getVar('ended'))));
							
							foreach($test->toArray(true) as $field => $value) {
								if (!in_array($field, array('name', 'testing', 'note', 'stage', 'test', 'platform', 'begin', 'started', 'ended', 'created', 'updated'))) {
									$xoopsMailer->assign(strtoupper($field), $value);
								}
							}
							
							if ($GLOBALS['xoopsModuleConfig']['send_email'])
								if(!$xoopsMailer->send() ){
									xoops_error($xoopsMailer->getErrors(true), _EMAIL_BENCHMARK_SENDERROR);
								}
												
							$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							$test->setVar('test', benchmark_getNextTest($test));
							break;
					}
					echo 'Stage D Finished: '.date('Y-m-d H:i:s').'<br/>';
					break;
									
				case '_MI_BENCHMARK_UPDATEALL':
						switch ($test->getVar('stage')) {
						case '_MI_BENCHMARK_WAIT':
							$test->setVar('stage', '_MI_BENCHMARK_TESTING');
							break;
						case '_MI_BENCHMARK_PREPARED':
							$test->setVar('begin', time());
							$test->setVar('stage', '_MI_BENCHMARK_WAIT');
							$sql=array();
							$tbids=array();
							foreach($engines as $engine) {
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $charset);
								benchmark_chainedSQL($table->getCreateTable());
								for($i=0;$i<$GLOBALS['xoopsModuleConfig']['update_all_records_num'];$i=$i+10) {
									benchmark_chainedSQL($table->getInsertIntoTable(10));
								}
								$tbids[$table->getVar('tbid')] = $table->getVar('tbid');
							}							
							foreach($tbids as $tbid) {
								$table = $tables_handler->get($tbid);
								$table->setVar('create', time());
								$table->setVar('prepared', time());
								$tables_handler->insert($table);
							}		
							$test->setVar('tbids_updateall', $tbids);	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=0;
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									$records = $_tHandler->getCount(NULL);
									for($c=0;$c<$GLOBALS['xoopsModuleConfig']['update_all_tests_num'];$c++) {
										$updatedata = $table->getUpdateArray(1);
										$GLOBALS['results'][$test->getVar('test')][0]['counter']++;
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]=$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]+strlen(implode('', $updatedata['updateasobject'][$tbid][0]));
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']=microtime(true);
										foreach($updatedata['updateasobject'][$tbid][0] as $field => $value) {
											$_tHandler->updateAll($field, $value, null, true);
										}
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']=microtime(true);		
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['records'][$tbid]=$records;
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['number'][$tbid]=$GLOBALS['results'][$test->getVar('test')][0]['counter'];
										$GLOBALS['results'][$test->getVar('test')][0]['took'] = $GLOBALS['results'][$test->getVar('test')][0]['took'] + ($GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']-$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']);
									}
								}
							}
							$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_updateall')).')', 'IN'), true);
							$results_handler->saveResults($test, $GLOBALS['results'], $tables);
							$test->setVar('took_updateall_seconds', $GLOBALS['results'][$test->getVar('test')][0]['took']);
							$test->setVar('tests_ran_updateall', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
							$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							break;
						case '_MI_BENCHMARK_CLEANUP':
							$criteria = new Criteria('tbid', '('.implode(',', $test->getVar('tbids_update')).')', 'IN');
							$tables = $tables_handler->getObjects($criteria, true);
							foreach($tables as $tbid => $table) {
								$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name'))."`");
								$table->setVar('dropped', time());
								$tables_handler->insert($table);
							}
							$test->setVar('stage', '_MI_BENCHMARK_FINISHED');
							$test->getHistory();
							break;
						case '_MI_BENCHMARK_FINISHED':
							$xoopsMailer =& getMailer();
							$xoopsMailer->setHTML(true);
							$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/benchmark/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
							$xoopsMailer->setTemplate('benchmark_updateall_finished.html');
							$xoopsMailer->setSubject(sprintf(_EMAIL_BENCHMARK_TASK_ENDED, constant($test->getVar('test').'_EMAIL'), $test->getVar('name'), date(_DATESTRING)));
							
							foreach(explode('|',$GLOBALS['xoopsModuleConfig']['email_addresses']) as $emailaddy)
								$xoopsMailer->setToEmails($emailaddy);
							
							$xoopsMailer->assign("SITEURL", XOOPS_URL);
							$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
							$xoopsMailer->assign("NAME", $test->getVar('name'));
							$xoopsMailer->assign("TESTING", $test->getVar('testing'));
							$xoopsMailer->assign("NOTE", $test->getVar('note'));
							$xoopsMailer->assign("STAGE", constant($test->getVar('stage').'_EMAIL'));
							$xoopsMailer->assign("TEST", constant($test->getVar('test').'_EMAIL'));
							$xoopsMailer->assign("NEXTTEST", constant(benchmark_getNextTest($test).'_EMAIL'));
							$xoopsMailer->assign("PLATFORM", constant($test->getVar('platform').'_EMAIL'));
							$xoopsMailer->assign("BEGIN", ($test->getVar('begin')==0?'--':date(_DATESTRING, $test->getVar('begin'))));
							$xoopsMailer->assign("STARTED", ($test->getVar('started')==0?'--':date(_DATESTRING, $test->getVar('started'))));
							$xoopsMailer->assign("CREATED", ($test->getVar('created')==0?'--':date(_DATESTRING, $test->getVar('created'))));
							$xoopsMailer->assign("UPDATED", ($test->getVar('updated')==0?'--':date(_DATESTRING, $test->getVar('updated'))));
							$xoopsMailer->assign("ENDED", ($test->getVar('ended')==0?'--':date(_DATESTRING, $test->getVar('ended'))));
							
							foreach($test->toArray(true) as $field => $value) {
								if (!in_array($field, array('name', 'testing', 'note', 'stage', 'test', 'platform', 'begin', 'started', 'ended', 'created', 'updated'))) {
									$xoopsMailer->assign(strtoupper($field), $value);
								}
							}
							
							if ($GLOBALS['xoopsModuleConfig']['send_email'])
								if(!$xoopsMailer->send() ){
									xoops_error($xoopsMailer->getErrors(true), _EMAIL_BENCHMARK_SENDERROR);
								}
												
							$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							$test->setVar('test', benchmark_getNextTest($test));
							break;
					}
					echo 'Stage E Finished: '.date('Y-m-d H:i:s').'<br/>';
					break;
					
				case '_MI_BENCHMARK_DELETE':
						switch ($test->getVar('stage')) {
						case '_MI_BENCHMARK_WAIT':
							$test->setVar('stage', '_MI_BENCHMARK_TESTING');
							break;
						case '_MI_BENCHMARK_PREPARED':
							$test->setVar('begin', time());
							$test->setVar('stage', '_MI_BENCHMARK_WAIT');
							$sql=array();
							$tbids=array();
							foreach(array('MYISAM') as $engine) {
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $charset);
								benchmark_chainedSQL($table->getCreateTable());
								for($i=0;$i<$GLOBALS['xoopsModuleConfig']['delete_records_num'];$i=$i+10) {
									benchmark_chainedSQL($table->getInsertIntoTable(10));
								}
								$tbids[$table->getVar('tbid')] = $table->getVar('tbid');
							}	
							foreach($tbids as $tbid) {
								$table = $tables_handler->get($tbid);
								$table->setVar('create', time());
								$table->setVar('prepared', time());
								$tables_handler->insert($table);
							}		
							$test->setVar('tbids_delete', $tbids);	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=0;
								$criteria = new Criteria('1', '1');
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									$records = $_tHandler->getCount($criteria);
									for($c=0;$c<$GLOBALS['xoopsModuleConfig']['delete_all_tests_num'];$c++) {
										foreach($_tHandler->getObjects($criteria, true) as $object) {
											$GLOBALS['results'][$test->getVar('test')][0]['counter']++;
											$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]=$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]+strlen(implode('', $updatedata['updateasobject'][$tbid][0]));
											$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']=microtime(true);
											$_tHandler->delete($object);
											$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']=microtime(true);		
											$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['records'][$tbid]=$records;
											$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['number'][$tbid]=$GLOBALS['results'][$test->getVar('test')][0]['counter'];
											$GLOBALS['results'][$test->getVar('test')][0]['took'] = $GLOBALS['results'][$test->getVar('test')][0]['took'] + ($GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']-$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']);
										}
										$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name'))."`");
										benchmark_chainedSQL($table->getCreateTable());
										for($i=0;$i<$GLOBALS['xoopsModuleConfig']['delete_records_num'];$i=$i+10) {
											benchmark_chainedSQL($table->getInsertIntoTable(10));
										}
									}
								}
							}
							$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_delete')).')', 'IN'), true);
							$results_handler->saveResults($test, $GLOBALS['results'], $tables);
							$test->setVar('took_delete_seconds', $GLOBALS['results'][$test->getVar('test')][0]['took']);
							$test->setVar('tests_ran_delete', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
							$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							break;
						case '_MI_BENCHMARK_CLEANUP':
							$criteria = new Criteria('tbid', '('.implode(',', $test->getVar('tbids_delete')).')', 'IN');
							$tables = $tables_handler->getObjects($criteria, true);
							foreach($tables as $tbid => $table) {
								$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name'))."`");
								$table->setVar('dropped', time());
								$tables_handler->insert($table);
							}
							$test->setVar('stage', '_MI_BENCHMARK_FINISHED');
							$test->getHistory();
							break;
						case '_MI_BENCHMARK_FINISHED':
							$xoopsMailer =& getMailer();
							$xoopsMailer->setHTML(true);
							$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/benchmark/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
							$xoopsMailer->setTemplate('benchmark_delete_finished.html');
							$xoopsMailer->setSubject(sprintf(_EMAIL_BENCHMARK_TASK_ENDED, constant($test->getVar('test').'_EMAIL'), $test->getVar('name'), date(_DATESTRING)));
							
							foreach(explode('|',$GLOBALS['xoopsModuleConfig']['email_addresses']) as $emailaddy)
								$xoopsMailer->setToEmails($emailaddy);
							
							$xoopsMailer->assign("SITEURL", XOOPS_URL);
							$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
							$xoopsMailer->assign("NAME", $test->getVar('name'));
							$xoopsMailer->assign("TESTING", $test->getVar('testing'));
							$xoopsMailer->assign("NOTE", $test->getVar('note'));
							$xoopsMailer->assign("STAGE", constant($test->getVar('stage').'_EMAIL'));
							$xoopsMailer->assign("TEST", constant($test->getVar('test').'_EMAIL'));
							$xoopsMailer->assign("NEXTTEST", constant(benchmark_getNextTest($test).'_EMAIL'));
							$xoopsMailer->assign("PLATFORM", constant($test->getVar('platform').'_EMAIL'));
							$xoopsMailer->assign("BEGIN", ($test->getVar('begin')==0?'--':date(_DATESTRING, $test->getVar('begin'))));
							$xoopsMailer->assign("STARTED", ($test->getVar('started')==0?'--':date(_DATESTRING, $test->getVar('started'))));
							$xoopsMailer->assign("CREATED", ($test->getVar('created')==0?'--':date(_DATESTRING, $test->getVar('created'))));
							$xoopsMailer->assign("UPDATED", ($test->getVar('updated')==0?'--':date(_DATESTRING, $test->getVar('updated'))));
							$xoopsMailer->assign("ENDED", ($test->getVar('ended')==0?'--':date(_DATESTRING, $test->getVar('ended'))));
							
							foreach($test->toArray(true) as $field => $value) {
								if (!in_array($field, array('name', 'testing', 'note', 'stage', 'test', 'platform', 'begin', 'started', 'ended', 'created', 'updated'))) {
									$xoopsMailer->assign(strtoupper($field), $value);
								}
							}
							
							if ($GLOBALS['xoopsModuleConfig']['send_email'])
								if(!$xoopsMailer->send() ){
									xoops_error($xoopsMailer->getErrors(true), _EMAIL_BENCHMARK_SENDERROR);
								}
												
							$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							$test->setVar('test', benchmark_getNextTest($test));
							break;
					}
					echo 'Stage F Finished: '.date('Y-m-d H:i:s').'<br/>';
					break;
					
				case '_MI_BENCHMARK_DELETEALL':
						switch ($test->getVar('stage')) {
						case '_MI_BENCHMARK_WAIT':
							$test->setVar('stage', '_MI_BENCHMARK_TESTING');
							break;
						case '_MI_BENCHMARK_PREPARED':
							$test->setVar('begin', time());
							$test->setVar('stage', '_MI_BENCHMARK_WAIT');
							$sql=array();
							$tbids=array();
							foreach(array('MYISAM') as $engine) {
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $charset);
								benchmark_chainedSQL($table->getCreateTable());
								for($i=0;$i<$GLOBALS['xoopsModuleConfig']['delete_all_records_num'];$i=$i+10) {
									benchmark_chainedSQL($table->getInsertIntoTable(10));
								}
								$tbids[$table->getVar('tbid')] = $table->getVar('tbid');
							}	
							foreach($tbids as $tbid) {
								$table = $tables_handler->get($tbid);
								$table->setVar('create', time());
								$table->setVar('prepared', time());
								$tables_handler->insert($table);
							}		
							$test->setVar('tbids_deleteall', $tbids);	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=0;
								$criteria = new Criteria('1', '1');
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									$records = $_tHandler->getCount($criteria);
									for($c=0;$c<$GLOBALS['xoopsModuleConfig']['delete_all_tests_num'];$c++) {
										$GLOBALS['results'][$test->getVar('test')][0]['counter']++;
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]=0;
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']=microtime(true);
										$_tHandler->deleteAll($criteria);
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']=microtime(true);		
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['records'][$tbid]=$records;
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['number'][$tbid]=$GLOBALS['results'][$test->getVar('test')][0]['counter'];
										$GLOBALS['results'][$test->getVar('test')][0]['took'] = $GLOBALS['results'][$test->getVar('test')][0]['took'] + ($GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']-$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']);
										$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name'))."`");
										$table->setVar('dropped', time());
										benchmark_chainedSQL($table->getCreateTable());
										for($i=0;$i<$GLOBALS['xoopsModuleConfig']['delete_all_records_num'];$i=$i+10) {
											benchmark_chainedSQL($table->getInsertIntoTable(10));
										}
										$table->setVar('create', time());
										$table->setVar('prepared', time());
										$tables_handler->insert($table);
										
									}
								}
							}
							$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_deleteall')).')', 'IN'), true);
							$results_handler->saveResults($test, $GLOBALS['results'], $tables);
							$test->setVar('took_deleteall_seconds', $GLOBALS['results'][$test->getVar('test')][0]['took']);
							$test->setVar('tests_ran_deleteall', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
							$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							break;
						case '_MI_BENCHMARK_CLEANUP':
							$criteria = new Criteria('tbid', '('.implode(',', $test->getVar('tbids_deleteall')).')', 'IN');
							$tables = $tables_handler->getObjects($criteria, true);
							foreach($tables as $tbid => $table) {
								$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name'))."`");
								$table->setVar('dropped', time());
								$tables_handler->insert($table);
							}
							$test->setVar('stage', '_MI_BENCHMARK_FINISHED');
							$test->getHistory();
							break;
						case '_MI_BENCHMARK_FINISHED':
							$xoopsMailer =& getMailer();
							$xoopsMailer->setHTML(true);
							$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/benchmark/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
							$xoopsMailer->setTemplate('benchmark_deleteall_finished.html');
							$xoopsMailer->setSubject(sprintf(_EMAIL_BENCHMARK_TASK_ENDED, constant($test->getVar('test').'_EMAIL'), $test->getVar('name'), date(_DATESTRING)));
							
							foreach(explode('|',$GLOBALS['xoopsModuleConfig']['email_addresses']) as $emailaddy)
								$xoopsMailer->setToEmails($emailaddy);
							
							$xoopsMailer->assign("SITEURL", XOOPS_URL);
							$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
							$xoopsMailer->assign("NAME", $test->getVar('name'));
							$xoopsMailer->assign("TESTING", $test->getVar('testing'));
							$xoopsMailer->assign("NOTE", $test->getVar('note'));
							$xoopsMailer->assign("STAGE", constant($test->getVar('stage').'_EMAIL'));
							$xoopsMailer->assign("TEST", constant($test->getVar('test').'_EMAIL'));
							$xoopsMailer->assign("NEXTTEST", constant(benchmark_getNextTest($test).'_EMAIL'));
							$xoopsMailer->assign("PLATFORM", constant($test->getVar('platform').'_EMAIL'));
							$xoopsMailer->assign("BEGIN", ($test->getVar('begin')==0?'--':date(_DATESTRING, $test->getVar('begin'))));
							$xoopsMailer->assign("STARTED", ($test->getVar('started')==0?'--':date(_DATESTRING, $test->getVar('started'))));
							$xoopsMailer->assign("CREATED", ($test->getVar('created')==0?'--':date(_DATESTRING, $test->getVar('created'))));
							$xoopsMailer->assign("UPDATED", ($test->getVar('updated')==0?'--':date(_DATESTRING, $test->getVar('updated'))));
							$xoopsMailer->assign("ENDED", ($test->getVar('ended')==0?'--':date(_DATESTRING, $test->getVar('ended'))));
							
							foreach($test->toArray() as $field => $value) {
								if (!in_array($field, array('name', 'testing', 'note', 'stage', 'test', 'platform', 'begin', 'started', 'ended', 'created', 'updated'))) {
									$xoopsMailer->assign(strtoupper($field), $value);
								}
							}
							
							if ($GLOBALS['xoopsModuleConfig']['send_email'])
								if(!$xoopsMailer->send(true) ){
									xoops_error($xoopsMailer->getErrors(true), _EMAIL_BENCHMARK_SENDERROR);
								}
												
							$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							$test->setVar('test', benchmark_getNextTest($test));
							break;
					}
					echo 'Stage G Finished: '.date('Y-m-d H:i:s').'<br/>';
					break;
					
					
				case '_MI_BENCHMARK_ALTER':
					switch ($test->getVar('stage')) {
						case '_MI_BENCHMARK_WAIT':
							$test->setVar('stage', '_MI_BENCHMARK_TESTING');
							break;
						case '_MI_BENCHMARK_PREPARED':
							$test->setVar('begin', time());
							$test->setVar('stage', '_MI_BENCHMARK_WAIT');
							$sql=array();
							$tbids=array();
							foreach($engines as $engine) {
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $charset);
								benchmark_chainedSQL($table->getCreateTable());
								for($i=0;$i<$GLOBALS['xoopsModuleConfig']['alter_records_num'];$i=$i+10) {
									benchmark_chainedSQL($table->getInsertIntoTable(10));
								}
								$tbids[$table->getVar('tbid')] = $table->getVar('tbid');
							}	
							benchmark_chainedSQL($sql);
							foreach($tbids as $tbid) {
								$table = $tables_handler->get($tbid);
								$table->setVar('create', time());
								$table->setVar('prepared', time());
								$tables_handler->insert($table);
							}		
							$test->setVar('tbids_alter', $tbids);	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=0;
								$criteria = new Criteria('1', '1');
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$alterdata = $table->getAlterTable(); 
									for($c=0;$c<$GLOBALS['xoopsModuleConfig']['alter_table_num'];$c++) {
										$GLOBALS['results'][$test->getVar('test')][0]['counter']++;
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]=strlen(implode('', $alterdata['alter'][$table->getVar('tbid')]));
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']=microtime(true);
										foreach($alterdata['alter'][$table->getVar('tbid')] as $fieldname => $question) {
											$GLOBALS['xoopsDB']->queryF($question);
										}
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']=microtime(true);		
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['records'][$tbid]=count($records);
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['number'][$tbid]=$GLOBALS['results'][$test->getVar('test')][0]['counter'];
										$GLOBALS['results'][$test->getVar('test')][0]['took'] = $GLOBALS['results'][$test->getVar('test')][0]['took'] + ($GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']-$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']);
										$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name'))."`");
										
										$table->setVar('dropped', time());
										benchmark_chainedSQL($table->getCreateTable());
										for($i=0;$i<$GLOBALS['xoopsModuleConfig']['alter_records_num'];$i=$i+10) {
											benchmark_chainedSQL($table->getInsertIntoTable(10));
										}
										$table->setVar('create', time());
										$table->setVar('prepared', time());
										$tables_handler->insert($table);
									}
								}
							}
							$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_alter')).')', 'IN'), true);
							$results_handler->saveResults($test, $GLOBALS['results'], $tables);
							$test->setVar('took_alter_seconds', $GLOBALS['results'][$test->getVar('test')][0]['took']);
							$test->setVar('tests_ran_alter', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
							$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							break;
						case '_MI_BENCHMARK_CLEANUP':
							$criteria = new Criteria('tbid', '('.implode(',', $test->getVar('tbids_deleteall')).')', 'IN');
							$tables = $tables_handler->getObjects($criteria, true);
							foreach($tables as $tbid => $table) {
								$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name'))."`");
								$table->setVar('dropped', time());
								$tables_handler->insert($table);
							}
							$test->setVar('stage', '_MI_BENCHMARK_FINISHED');
							$test->getHistory();
							break;
						case '_MI_BENCHMARK_FINISHED':
							$xoopsMailer =& getMailer();
							$xoopsMailer->setHTML(true);
							$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/benchmark/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
							$xoopsMailer->setTemplate('benchmark_alter_finished.html');
							$xoopsMailer->setSubject(sprintf(_EMAIL_BENCHMARK_TASK_ENDED, constant($test->getVar('test').'_EMAIL'), $test->getVar('name'), date(_DATESTRING)));
							
							foreach(explode('|',$GLOBALS['xoopsModuleConfig']['email_addresses']) as $emailaddy)
								$xoopsMailer->setToEmails($emailaddy);
							
							$xoopsMailer->assign("SITEURL", XOOPS_URL);
							$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
							$xoopsMailer->assign("NAME", $test->getVar('name'));
							$xoopsMailer->assign("TESTING", $test->getVar('testing'));
							$xoopsMailer->assign("NOTE", $test->getVar('note'));
							$xoopsMailer->assign("STAGE", constant($test->getVar('stage').'_EMAIL'));
							$xoopsMailer->assign("TEST", constant($test->getVar('test').'_EMAIL'));
							$xoopsMailer->assign("NEXTTEST", constant(benchmark_getNextTest($test).'_EMAIL'));
							$xoopsMailer->assign("PLATFORM", constant($test->getVar('platform').'_EMAIL'));
							$xoopsMailer->assign("BEGIN", ($test->getVar('begin')==0?'--':date(_DATESTRING, $test->getVar('begin'))));
							$xoopsMailer->assign("STARTED", ($test->getVar('started')==0?'--':date(_DATESTRING, $test->getVar('started'))));
							$xoopsMailer->assign("CREATED", ($test->getVar('created')==0?'--':date(_DATESTRING, $test->getVar('created'))));
							$xoopsMailer->assign("UPDATED", ($test->getVar('updated')==0?'--':date(_DATESTRING, $test->getVar('updated'))));
							$xoopsMailer->assign("ENDED", ($test->getVar('ended')==0?'--':date(_DATESTRING, $test->getVar('ended'))));
							
							foreach($test->toArray(true) as $field => $value) {
								if (!in_array($field, array('name', 'testing', 'note', 'stage', 'test', 'platform', 'begin', 'started', 'ended', 'created', 'updated'))) {
									$xoopsMailer->assign(strtoupper($field), $value);
								}
							}
							
							if ($GLOBALS['xoopsModuleConfig']['send_email'])
								if(!$xoopsMailer->send() ){
									xoops_error($xoopsMailer->getErrors(true), _EMAIL_BENCHMARK_SENDERROR);
								}
												
							$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							$test->setVar('test', benchmark_getNextTest($test));
							break;
					}
					echo 'Stage H Finished: '.date('Y-m-d H:i:s').'<br/>';
					break;
				case '_MI_BENCHMARK_RENAME':
					switch ($test->getVar('stage')) {
						case '_MI_BENCHMARK_WAIT':
							$test->setVar('stage', '_MI_BENCHMARK_TESTING');
							break;
						case '_MI_BENCHMARK_PREPARED':
							$test->setVar('begin', time());
							$test->setVar('stage', '_MI_BENCHMARK_WAIT');
							$sql=array();
							$tbids=array();
							for($i=0;$i<$GLOBALS['xoopsModuleConfig']['rename_table_num']/count($engines);$i++) {
								foreach($engines as $engine) {
									$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $charset);
									benchmark_chainedSQL($table->getCreateTable());
									$tbids[$table->getVar('tbid')] = $table->getVar('tbid');
								}
							}
							foreach($tbids as $tbid) {
								$table = $tables_handler->get($tbid);
								$table->setVar('create', time());
								$table->setVar('prepared', time());
								$tables_handler->insert($table);
							}		
							$test->setVar('tbids_rename', $tbids);	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=0;
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$renamedata = $table->getRenameTable(); 
									foreach($renamedata['rename'][$table->getVar('tbid')] as $newname => $question) {
										$GLOBALS['results'][$test->getVar('test')][0]['counter']++;
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]=strlen($question);
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']=microtime(true);
										$GLOBALS['xoopsDB']->queryF($question);
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']=microtime(true);		
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['records'][$tbid]=count($records);
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['number'][$tbid]=$GLOBALS['results'][$test->getVar('test')][0]['counter'];
										$GLOBALS['results'][$test->getVar('test')][0]['took'] = $GLOBALS['results'][$test->getVar('test')][0]['took'] + ($GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']-$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']);
										
										$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($newname)."`");
										
										$table->setVar('dropped', time());
										$tables_handler->insert($table);
										benchmark_chainedSQL($table->getCreateTable());
										$table->setVar('create', time());
										$table->setVar('prepared', time());
										$tables_handler->insert($table);
									}
								}
							}
							$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_rename')).')', 'IN'), true);
							$results_handler->saveResults($test, $GLOBALS['results'], $tables);
							$test->setVar('took_rename_seconds', $GLOBALS['results'][$test->getVar('test')][0]['took']);
							$test->setVar('tests_ran_rename', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
							$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							break;
						case '_MI_BENCHMARK_CLEANUP':
							$criteria = new Criteria('tbid', '('.implode(',', $test->getVar('tbids_deleteall')).')', 'IN');
							$tables = $tables_handler->getObjects($criteria, true);
							foreach($tables as $tbid => $table) {
								$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name'))."`");
								$table->setVar('dropped', time());
								$tables_handler->insert($table);
							}
							$test->setVar('stage', '_MI_BENCHMARK_FINISHED');
							$test->getHistory();
							break;
						case '_MI_BENCHMARK_FINISHED':
							$xoopsMailer =& getMailer();
							$xoopsMailer->setHTML(true);
							$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/benchmark/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
							$xoopsMailer->setTemplate('benchmark_rename_finished.html');
							$xoopsMailer->setSubject(sprintf(_EMAIL_BENCHMARK_TASK_ENDED, constant($test->getVar('test').'_EMAIL'), $test->getVar('name'), date(_DATESTRING)));
							
							foreach(explode('|',$GLOBALS['xoopsModuleConfig']['email_addresses']) as $emailaddy)
								$xoopsMailer->setToEmails($emailaddy);
							
							$xoopsMailer->assign("SITEURL", XOOPS_URL);
							$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
							$xoopsMailer->assign("NAME", $test->getVar('name'));
							$xoopsMailer->assign("TESTING", $test->getVar('testing'));
							$xoopsMailer->assign("NOTE", $test->getVar('note'));
							$xoopsMailer->assign("STAGE", constant($test->getVar('stage').'_EMAIL'));
							$xoopsMailer->assign("TEST", constant($test->getVar('test').'_EMAIL'));
							$xoopsMailer->assign("NEXTTEST", constant(benchmark_getNextTest($test).'_EMAIL'));
							$xoopsMailer->assign("PLATFORM", constant($test->getVar('platform').'_EMAIL'));
							$xoopsMailer->assign("BEGIN", ($test->getVar('begin')==0?'--':date(_DATESTRING, $test->getVar('begin'))));
							$xoopsMailer->assign("STARTED", ($test->getVar('started')==0?'--':date(_DATESTRING, $test->getVar('started'))));
							$xoopsMailer->assign("CREATED", ($test->getVar('created')==0?'--':date(_DATESTRING, $test->getVar('created'))));
							$xoopsMailer->assign("UPDATED", ($test->getVar('updated')==0?'--':date(_DATESTRING, $test->getVar('updated'))));
							$xoopsMailer->assign("ENDED", ($test->getVar('ended')==0?'--':date(_DATESTRING, $test->getVar('ended'))));
							
							foreach($test->toArray(true) as $field => $value) {
								if (!in_array($field, array('name', 'testing', 'note', 'stage', 'test', 'platform', 'begin', 'started', 'ended', 'created', 'updated'))) {
									$xoopsMailer->assign(strtoupper($field), $value);
								}
							}
							
							if ($GLOBALS['xoopsModuleConfig']['send_email'])
								if(!$xoopsMailer->send() ){
									xoops_error($xoopsMailer->getErrors(true), _EMAIL_BENCHMARK_SENDERROR);
								}
												
							$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							$test->setVar('test', benchmark_getNextTest($test));
							break;
					}
					echo 'Stage I Finished: '.date('Y-m-d H:i:s').'<br/>';
					break;
					
				case '_MI_BENCHMARK_SMARTY':
					switch ($test->getVar('stage')) {
						case '_MI_BENCHMARK_WAIT':
							$test->setVar('stage', '_MI_BENCHMARK_TESTING');
							break;
						case '_MI_BENCHMARK_PREPARED':
							$test->setVar('begin', time());
							$test->setVar('stage', '_MI_BENCHMARK_WAIT');
							$sql=array();
							$tbids=array();
							foreach($engines as $engine) {
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $charset);
								benchmark_chainedSQL($table->getCreateTable());
								for($i=0;$i<$GLOBALS['xoopsModuleConfig']['smarty_num'];$i=$i+10) {
									benchmark_chainedSQL($table->getInsertIntoTable(10));
								}
								$tbids[$table->getVar('tbid')] = $table->getVar('tbid');
							}								
														benchmark_chainedSQL($sql);			
							foreach($tbids as $tbid) {
								$table = $tables_handler->get($tbid);
								$table->setVar('create', time());
								$table->setVar('prepared', time());
								$tables_handler->insert($table);
							}		
							$test->setVar('tbids_smarty', $tbids);	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							include XOOPS_ROOT_PATH.'/class/template.php';
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=0;
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									$objects = $_tHandler->getObjects(NULL, false); 
									for($i=0;$i<$GLOBALS['xoopsModuleConfig']['smarty_tests_num'];$i++) {
										ob_start();
										$GLOBALS['results'][$test->getVar('test')][0]['counter']++;
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']=microtime(true);
										$GLOBALS['xoopsTpl'] = new XoopsTpl();
										foreach($objects as $id => $object) {
											$GLOBALS['xoopsTpl']->append('data', $object->toArray());
										}								
										$GLOBALS['xoopsTpl']->display('db:benchmark_test.html');
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]=strlen(ob_get_clean());
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']=microtime(true);		
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['records'][$tbid]=count($objects);
										$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['number'][$tbid]=$GLOBALS['results'][$test->getVar('test')][0]['counter'];
										$GLOBALS['results'][$test->getVar('test')][0]['took'] = $GLOBALS['results'][$test->getVar('test')][0]['took'] + ($GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']-$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']);
										ob_end_clean();
										unset($GLOBALS['xoopsTpl']);
									}
								}
							}
							$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_smarty')).')', 'IN'), true);
							$results_handler->saveResults($test, $GLOBALS['results'], $tables);
							$test->setVar('took_smarty_seconds', $GLOBALS['results'][$test->getVar('test')][0]['took']);
							$test->setVar('tests_ran_smarty', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
							$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							break;
						case '_MI_BENCHMARK_CLEANUP':
							$criteria = new Criteria('tbid', '('.implode(',', $test->getVar('tbids_deleteall')).')', 'IN');
							$tables = $tables_handler->getObjects($criteria, true);
							foreach($tables as $tbid => $table) {
								$GLOBALS['xoopsDB']->queryF('DROP TABLE `'.$GLOBALS['xoopsDB']->prefix($table->getVar('name'))."`");
								$table->setVar('dropped', time());
								$tables_handler->insert($table);
							}
							$test->setVar('stage', '_MI_BENCHMARK_FINISHED');
							$test->getHistory();
							break;
						case '_MI_BENCHMARK_FINISHED':
							$xoopsMailer =& getMailer();
							$xoopsMailer->setHTML(true);
							$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/benchmark/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
							$xoopsMailer->setTemplate('benchmark_smarty_finished.html');
							$xoopsMailer->setSubject(sprintf(_EMAIL_BENCHMARK_TASK_ENDED, constant($test->getVar('test').'_EMAIL'), $test->getVar('name'), date(_DATESTRING)));
							
							foreach(explode('|',$GLOBALS['xoopsModuleConfig']['email_addresses']) as $emailaddy)
								$xoopsMailer->setToEmails($emailaddy);
							
							$xoopsMailer->assign("SITEURL", XOOPS_URL);
							$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
							$xoopsMailer->assign("NAME", $test->getVar('name'));
							$xoopsMailer->assign("TESTING", $test->getVar('testing'));
							$xoopsMailer->assign("NOTE", $test->getVar('note'));
							$xoopsMailer->assign("STAGE", constant($test->getVar('stage').'_EMAIL'));
							$xoopsMailer->assign("TEST", constant($test->getVar('test').'_EMAIL'));
							$xoopsMailer->assign("NEXTTEST", constant(benchmark_getNextTest($test).'_EMAIL'));
							$xoopsMailer->assign("PLATFORM", constant($test->getVar('platform').'_EMAIL'));
							$xoopsMailer->assign("BEGIN", ($test->getVar('begin')==0?'--':date(_DATESTRING, $test->getVar('begin'))));
							$xoopsMailer->assign("STARTED", ($test->getVar('started')==0?'--':date(_DATESTRING, $test->getVar('started'))));
							$xoopsMailer->assign("CREATED", ($test->getVar('created')==0?'--':date(_DATESTRING, $test->getVar('created'))));
							$xoopsMailer->assign("UPDATED", ($test->getVar('updated')==0?'--':date(_DATESTRING, $test->getVar('updated'))));
							$xoopsMailer->assign("ENDED", ($test->getVar('ended')==0?'--':date(_DATESTRING, $test->getVar('ended'))));
											
							foreach($test->toArray(true) as $field => $value) {
								if (!in_array($field, array('name', 'testing', 'note', 'stage', 'test', 'platform', 'begin', 'started', 'ended', 'created', 'updated'))) {
									$xoopsMailer->assign(strtoupper($field), $value);
								}
							}
							
							if ($GLOBALS['xoopsModuleConfig']['send_email'])
								if(!$xoopsMailer->send() ){
									xoops_error($xoopsMailer->getErrors(true), _EMAIL_BENCHMARK_SENDERROR);
								}
										
							$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							$test->setVar('test', benchmark_getNextTest($test));
							break;
					}
					echo 'Stage G Finished: '.date('Y-m-d H:i:s').'<br/>';
					break;
				case '_MI_BENCHMARK_FINISHED':
					$test->setVar('ended', time());
					foreach(array('_MI_BENCHMARK_WAIT','_MI_BENCHMARK_CREATE','_MI_BENCHMARK_SELECT','_MI_BENCHMARK_INSERT','_MI_BENCHMARK_UPDATE','_MI_BENCHMARK_UPDATEALL','_MI_BENCHMARK_DELETE','_MI_BENCHMARK_DELETEALL','_MI_BENCHMARK_ALTER','_MI_BENCHMARK_RENAME','_MI_BENCHMARK_SMARTY','_MI_BENCHMARK_FINISHED') as $type) {
						$test->setVar('test', $type);
						$test->getHistory();
					}
					$tests_handler->insert($test, true);
					XoopsCache::delete('benchmark_current_test');
					
					$xoopsMailer =& getMailer();
					$xoopsMailer->setHTML(true);
					$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/benchmark/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
					$xoopsMailer->setTemplate('benchmark_all_finished.html');
					$xoopsMailer->setSubject(sprintf(_EMAIL_BENCHMARK_TASK_ENDED, constant($test->getVar('test').'_EMAIL'), $test->getVar('name'), date(_DATESTRING)));
					
					foreach(explode('|',$GLOBALS['xoopsModuleConfig']['email_addresses']) as $emailaddy)
						$xoopsMailer->setToEmails($emailaddy);
					
					$xoopsMailer->assign("SITEURL", XOOPS_URL);
					$xoopsMailer->assign("SITENAME", $GLOBALS['xoopsConfig']['sitename']);
					$xoopsMailer->assign("NAME", $test->getVar('name'));
					$xoopsMailer->assign("TESTING", $test->getVar('testing'));
					$xoopsMailer->assign("NOTE", $test->getVar('note'));
					$xoopsMailer->assign("STAGE", constant($test->getVar('stage').'_EMAIL'));
					$xoopsMailer->assign("TEST", constant($test->getVar('test').'_EMAIL'));
					$xoopsMailer->assign("NEXTTEST", constant(benchmark_getNextTest($test).'_EMAIL'));
					$xoopsMailer->assign("PLATFORM", constant($test->getVar('platform').'_EMAIL'));
					$xoopsMailer->assign("BEGIN", ($test->getVar('begin')==0?'--':date(_DATESTRING, $test->getVar('begin'))));
					$xoopsMailer->assign("STARTED", ($test->getVar('started')==0?'--':date(_DATESTRING, $test->getVar('started'))));
					$xoopsMailer->assign("CREATED", ($test->getVar('created')==0?'--':date(_DATESTRING, $test->getVar('created'))));
					$xoopsMailer->assign("UPDATED", ($test->getVar('updated')==0?'--':date(_DATESTRING, $test->getVar('updated'))));
					$xoopsMailer->assign("ENDED", ($test->getVar('ended')==0?'--':date(_DATESTRING, $test->getVar('ended'))));
					
					foreach($test->toArray(true) as $field => $value) {
						if (!in_array($field, array('name', 'testing', 'note', 'stage', 'test', 'platform', 'begin', 'started', 'ended', 'created', 'updated'))) {
							$xoopsMailer->assign(strtoupper($field), $value);
						}
					}
					if ($GLOBALS['xoopsModuleConfig']['send_email'])
						if(!$xoopsMailer->send() ){
							xoops_error($xoopsMailer->getErrors(true), _EMAIL_BENCHMARK_SENDERROR);
						}
					exit(0);
					break;
			}
			echo 'Next Stage: '.$test->getVar('stage').'<br/>';
			if (!$tests_handler->insert($test, true)) {
				xoops_error($GLOBALS['xoopsDB']->error(), $GLOBALS['xoopsDB']->errno());
				echo '<pre>';
				print_r($test);
				echo '</pre>';
			}
			XoopsCache::write('benchmark_current_test', $test->getVar('tid'));
		} else {
			XoopsCache::delete('benchmark_current_test');
		}
	} else {
		XoopsCache::delete('benchmark_current_test');
	}
	echo 'Finished: '.date('Y-m-d H:i:s').'<br/>';
	XoopsCache::delete('benchmark_currently_doing_test');
?>