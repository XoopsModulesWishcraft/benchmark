<?php

	require ('header.php');
	
	if ($testnumurrently = XoopsCache::read('benchmark_currently_doing_test'))
		exit;
	else 
		XoopsCache::write('benchmark_currently_doing_test', microtime(true), 600);
	
	echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Cron Started: '.date('Y-m-d H:i:s').(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
			
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
			$criteria->add(new Criteria('begin', time(), '<'));
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
				echo 'Nothing to do: '.date('Y-m-d H:i:s').(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
				XoopsCache::delete('benchmark_currently_doing_test');
				exit(0);
			}
		}
	}
	
	$test = $tests_handler->get($tid);
			
	$engines = $GLOBALS['xoopsModuleConfig']['engines'];
	if (count($engines)==0)
		$engines = array('INNODB');
		
	$testnumharset = $GLOBALS['xoopsModuleConfig']['charset'];
	if (empty($testnumharset))
		$testnumharset = 'utf8';

	$fields = $GLOBALS['xoopsModuleConfig']['fields_num'];
	if (intval($fields)==0)
		$fields = 18;
		
	$GLOBALS['timer']['boot']['end'] = microtime(true);
	
	if (is_object($test)) {
		echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Current Test: '.constant($test->getVar('test')).(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
		echo 'Current Stage: '.constant($test->getVar('stage')).(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
		echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Roll Over when finished to: '.constant(benchmark_getNextTest($test)).(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
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
									$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $testnumharset);
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
							$test->setVar('tbids_create', array_merge($test->getVar('tbids_create'), $tbids));	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $sql);
							break;		
						case '_MI_BENCHMARK_TESTING':
						if ($testnum = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'))) { $testnum++; } else { $testnum=1; }
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=$test->getVar('tests_ran_create');
								if ($testnum<=1) {
									$testnum = ceil(count($test->getVar('tbids_create'))/count($data));
								}
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
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'), $testnum, 60*60*24*2);
							if ($testnum >= $GLOBALS['xoopsModuleConfig']['create_tests_num']) {
								echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Last Test: 100% of ' .$GLOBALS['xoopsModuleConfig']['create_tests_num'].' benchmarking test\'s done'.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								XoopsCache::delete('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'));
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_create')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_create_seconds', $test->getVar('took_create_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_create', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							} else {
								echo 'Test: ' . $testnum . ' of ' .$GLOBALS['xoopsModuleConfig']['create_tests_num'].(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_create')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_create_seconds', $test->getVar('took_create_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_create', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							}
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
					    	$results_handler =& xoops_getmodulehandler('results', $GLOBALS['xoopsModule']->getVar('dirname'));
					    	$history = $results_handler->getMinMaxAvgSumValues($test);
					    	$test->setVars($history);
					    	$test->setVar('ended', time());
							foreach($history as $field => $value) {
								echo ucfirst($field).': '.$value.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
							}
					    	
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
					echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Stage A Finished: '.date('Y-m-d H:i:s').(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
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
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $testnumharset);
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
							$test->setVar('tbids_select', array_merge($test->getVar('tbids_select'), $tbids));	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
						if ($testnum = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'))) { $testnum++; } else { $testnum=1; }
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								if ($testnum<=1) {
									$testnum = ceil(count($test->getVar('tbids_select'))/count($data));
								}
								$criteria = new Criteria('tbid', '('.implode(',', $data).')', 'IN');
								$tables = $tables_handler->getObjects($criteria, true);
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=$test->getVar('tests_ran_select');
								$criteria = new Criteria('1','1');
								$criteria->setLimit($GLOBALS['xoopsModuleConfig']['select_records_num']);
								foreach($tables as $tbid => $table) {
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									$recordcount = $_tHandler->getCount($criteria);
									$GLOBALS['results'][$test->getVar('test')][0]['counter']++;
									$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']=microtime(true);
									$records = $_tHandler->getObjects($criteria, true, false);
									$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']=microtime(true);		
									$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['length'][$tbid]=strlen(implode('', $records));
									$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['records'][$tbid]=$recordcount;
									$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['number'][$tbid]=$GLOBALS['results'][$test->getVar('test')][0]['counter'];
									$GLOBALS['results'][$test->getVar('test')][0]['took'] = $GLOBALS['results'][$test->getVar('test')][0]['took'] + ($GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['end']-$GLOBALS['results'][$test->getVar('test')][$GLOBALS['results'][$test->getVar('test')][0]['counter']]['timer'][$tbid]['start']); 
								}
							}
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'), $testnum, 60*60*24*2);
							if ($testnum >= $GLOBALS['xoopsModuleConfig']['select_tests_num']) {
								echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Last Test: 100% of ' .$GLOBALS['xoopsModuleConfig']['select_tests_num'].' benchmarking test\'s done'.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								XoopsCache::delete('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'));
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_select')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_select_seconds', $test->getVar('took_select_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_select', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							} else {
								echo 'Test: ' . $testnum . ' of ' .$GLOBALS['xoopsModuleConfig']['select_tests_num'].(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_select')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_select_seconds', $test->getVar('took_select_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_select', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							}
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
					    	$results_handler =& xoops_getmodulehandler('results', $GLOBALS['xoopsModule']->getVar('dirname'));
					    	$history = $results_handler->getMinMaxAvgSumValues($test);
					    	$test->setVars($history);
					    	$test->setVar('ended', time());
							foreach($history as $field => $value) {
								echo '`'.$field.'`: "'.$value.'"'.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
							}
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
					echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Stage B Finished: '.date('Y-m-d H:i:s').(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
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
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $testnumharset);
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
							$test->setVar('tbids_insert', array_merge($test->getVar('tbids_insert'), $tbids));	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $data);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($testnum = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'))) { $testnum++; } else { $testnum=1; }
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=$test->getVar('tests_ran_insert');
								if ($testnum<=1) {
									$testnum = ceil(count($test->getVar('tbids_insert'))/count($data));
								}
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									
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
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'), $testnum, 60*60*24*2);
							if ($testnum >= $GLOBALS['xoopsModuleConfig']['insert_tests_num']) {
								echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Last Test: 100% of ' .$GLOBALS['xoopsModuleConfig']['insert_tests_num'].' benchmarking test\'s done'.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								XoopsCache::delete('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'));
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_insert')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_insert_seconds', $test->getVar('took_insert_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_insert', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							} else {
								echo 'Test: ' . $testnum . ' of ' .$GLOBALS['xoopsModuleConfig']['insert_tests_num'].(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_insert')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_insert_seconds', $test->getVar('took_insert_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_insert', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							}
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
					    	$results_handler =& xoops_getmodulehandler('results', $GLOBALS['xoopsModule']->getVar('dirname'));
					    	$history = $results_handler->getMinMaxAvgSumValues($test);
					    	$test->setVars($history);
					    	$test->setVar('ended', time());
							foreach($history as $field => $value) {
								echo ucfirst($field).': '.$value.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
							}
					    	
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
					echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Stage C Finished: '.date('Y-m-d H:i:s').(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
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
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $testnumharset);
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
							$test->setVar('tbids_update', array_merge($test->getVar('tbids_update'), $tbids));	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($testnum = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'))) { $testnum++; } else { $testnum=1; }
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=$test->getVar('tests_ran_update');
								if ($testnum<=1) {
									$testnum = ceil(count($test->getVar('tbids_update'))/count($data));
								}
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									$records = $_tHandler->getCount(NULL);
								
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
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'), $testnum, 60*60*24*2);
							if ($testnum >= $GLOBALS['xoopsModuleConfig']['update_tests_num']) {
								echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Last Test: 100% of ' .$GLOBALS['xoopsModuleConfig']['update_tests_num'].' benchmarking test\'s done'.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								XoopsCache::delete('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'));
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_update')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_update_seconds', $test->getVar('took_update_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_update', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							} else {
								echo 'Test: ' . $testnum . ' of ' .$GLOBALS['xoopsModuleConfig']['update_tests_num'].(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_update')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_update_seconds', $test->getVar('took_update_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_update', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							}
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
					    	$results_handler =& xoops_getmodulehandler('results', $GLOBALS['xoopsModule']->getVar('dirname'));
					    	$history = $results_handler->getMinMaxAvgSumValues($test);
					    	$test->setVars($history);
					    	$test->setVar('ended', time());
							foreach($history as $field => $value) {
								echo ucfirst($field).': '.$value.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
							}
					    	
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
					echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Stage D Finished: '.date('Y-m-d H:i:s').(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
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
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $testnumharset);
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
							$test->setVar('tbids_updateall', array_merge($test->getVar('tbids_updateall'), $tbids));	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($testnum = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'))) { $testnum++; } else { $testnum=1; }
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=$test->getVar('tests_ran_updateall');
								if ($testnum<=1) {
									$testnum = ceil(count($test->getVar('tbids_updateall'))/count($data));
								}
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									$records = $_tHandler->getCount(NULL);
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
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'), $testnum, 60*60*24*2);
							if ($testnum >= $GLOBALS['xoopsModuleConfig']['update_all_tests_num']) {
								echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Last Test: 100% of ' .$GLOBALS['xoopsModuleConfig']['update_all_tests_num'].' benchmarking test\'s done'.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								XoopsCache::delete('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'));
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_updateall')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_updateall_seconds', $test->getVar('took_updateall_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_updateall', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							} else {
								echo 'Test: ' . $testnum . ' of ' .$GLOBALS['xoopsModuleConfig']['update_all_tests_num'].(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_updateall')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_updateall_seconds', $test->getVar('took_updateall_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_updateall', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							}
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
					    	$results_handler =& xoops_getmodulehandler('results', $GLOBALS['xoopsModule']->getVar('dirname'));
					    	$history = $results_handler->getMinMaxAvgSumValues($test);
					    	$test->setVars($history);
					    	$test->setVar('ended', time());
							foreach($history as $field => $value) {
								echo ucfirst($field).': '.$value.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
							}
					    	
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
					echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Stage E Finished: '.date('Y-m-d H:i:s').(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
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
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $testnumharset);
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
							$test->setVar('tbids_delete', array_merge($test->getVar('tbids_delete'), $tbids));	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($testnum = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'))) { $testnum++; } else { $testnum=1; }
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=$test->getVar('tests_ran_delete');
								$criteria = new Criteria('1', '1');
								if ($testnum<=1) {
									$testnum = ceil(count($test->getVar('tbids_delete'))/count($data));
								}
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									$records = $_tHandler->getCount($criteria);
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
								}
							}
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'), $testnum, 60*60*24*2);
							if ($testnum >= $GLOBALS['xoopsModuleConfig']['delete_tests_num']) {
								echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Last Test: 100% of ' .$GLOBALS['xoopsModuleConfig']['delete_tests_num'].' benchmarking test\'s done'.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								XoopsCache::delete('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'));
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_delete')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_delete_seconds', $test->getVar('took_delete_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_delete', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							} else {
								echo 'Test: ' . $testnum . ' of ' .$GLOBALS['xoopsModuleConfig']['delete_tests_num'].(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_delete')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_delete_seconds', $test->getVar('took_delete_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_delete', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							}
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
					    	$results_handler =& xoops_getmodulehandler('results', $GLOBALS['xoopsModule']->getVar('dirname'));
					    	$history = $results_handler->getMinMaxAvgSumValues($test);
					    	$test->setVars($history);
					    	$test->setVar('ended', time());
							foreach($history as $field => $value) {
								echo ucfirst($field).': '.$value.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
							}
					    	
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
					echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Stage F Finished: '.date('Y-m-d H:i:s').(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
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
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $testnumharset);
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
							$test->setVar('tbids_deleteall', array_merge($test->getVar('tbids_deleteall'), $tbids));	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($testnum = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'))) { $testnum++; } else { $testnum=1; }
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=$test->getVar('tests_ran_deleteall');
								$criteria = new Criteria('1', '1');
								if ($testnum<=1) {
									$testnum = ceil(count($test->getVar('tbids_deleteall'))/count($data));
								}
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									$records = $_tHandler->getCount($criteria);
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
								
								}
									
							}
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'), $testnum, 60*60*24*2);
							if ($testnum >= $GLOBALS['xoopsModuleConfig']['delete_all_tests_num']) {
								echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Last Test: 100% of ' .$GLOBALS['xoopsModuleConfig']['delete_all_tests_num'].' benchmarking test\'s done'.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								XoopsCache::delete('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'));
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_deleteall')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_deleteall_seconds', $test->getVar('took_deleteall_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_deleteall', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							} else {
								echo 'Test: ' . $testnum . ' of ' .$GLOBALS['xoopsModuleConfig']['delete_all_tests_num'].(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_deleteall')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_deleteall_seconds', $test->getVar('took_deleteall_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_deleteall', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
							}
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
					    	$results_handler =& xoops_getmodulehandler('results', $GLOBALS['xoopsModule']->getVar('dirname'));
					    	$history = $results_handler->getMinMaxAvgSumValues($test);
					    	$test->setVars($history);
					    	$test->setVar('ended', time());
							foreach($history as $field => $value) {
								echo ucfirst($field).': '.$value.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
							}
					    	
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
					echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Stage G Finished: '.date('Y-m-d H:i:s').(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
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
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $testnumharset);
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
							$test->setVar('tbids_alter', array_merge($test->getVar('tbids_alter'), $tbids));	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($testnum = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'))) { $testnum++; } else { $testnum=1; }
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=$test->getVar('tests_ran_alter');
								$criteria = new Criteria('1', '1');
								if ($testnum<=1) {
									$testnum = ceil(count($test->getVar('tbids_alter'))/count($data));
								}
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$alterdata = $table->getAlterTable(); 
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
								}
							}
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'), $testnum, 60*60*24*2);
							if ($testnum >= $GLOBALS['xoopsModuleConfig']['alter_tests_num']) {
								echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Last Test: 100% of ' .$GLOBALS['xoopsModuleConfig']['alter_tests_num'].' benchmarking test\'s done'.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								XoopsCache::delete('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'));
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_alter')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_alter_seconds', $test->getVar('took_alter_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_alter', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							} else {
								echo 'Test: ' . $testnum . ' of ' .$GLOBALS['xoopsModuleConfig']['alter_tests_num'].(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_alter')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
								$test->setVar('took_alter_seconds', $test->getVar('took_alter_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_alter', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
							}
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
					    	$results_handler =& xoops_getmodulehandler('results', $GLOBALS['xoopsModule']->getVar('dirname'));
					    	$history = $results_handler->getMinMaxAvgSumValues($test);
					    	$test->setVars($history);
					    	$test->setVar('ended', time());
							foreach($history as $field => $value) {
								echo ucfirst($field).': '.$value.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
							}
					    	
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
					echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Stage H Finished: '.date('Y-m-d H:i:s').(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
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
									$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $testnumharset);
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
							$test->setVar('tbids_rename', array_merge($test->getVar('tbids_rename'), $tbids));	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							if ($testnum = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'))) { $testnum++; } else { $testnum=1; }
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								if ($testnum<=1) {
									$testnum = ceil(count($test->getVar('tbids_rename'))/count($data));
								}
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=$test->getVar('tests_ran_rename');
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
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'), $testnum, 60*60*24*2);
							if ($testnum >= $GLOBALS['xoopsModuleConfig']['rename_tests_num']) {
								echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Last Test: 100% of ' .$GLOBALS['xoopsModuleConfig']['rename_tests_num'].' benchmarking test\'s done'.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								XoopsCache::delete('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'));
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_rename')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_rename_seconds', $test->getVar('took_rename_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_rename', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							} else {
								echo 'Test: ' . $testnum . ' of ' .$GLOBALS['xoopsModuleConfig']['rename_tests_num'].(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_rename')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
								$test->setVar('took_rename_seconds', $test->getVar('took_rename_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_rename', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
							}
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
					    	$results_handler =& xoops_getmodulehandler('results', $GLOBALS['xoopsModule']->getVar('dirname'));
					    	$history = $results_handler->getMinMaxAvgSumValues($test);
					    	$test->setVars($history);
					    	$test->setVar('ended', time());
							foreach($history as $field => $value) {
								echo ucfirst($field).': '.$value.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
							}
					    	
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
					echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Stage I Finished: '.date('Y-m-d H:i:s').(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
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
								$table = $tables_handler->designTable($test->getVar('tid'), $test->getVar('test'), $fields, $engine, $testnumharset);
								benchmark_chainedSQL($table->getCreateTable());
								for($i=0;$i<$GLOBALS['xoopsModuleConfig']['smarty_num'];$i=$i+10) {
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
							$test->setVar('tbids_smarty', array_merge($test->getVar('tbids_smarty'), $tbids));	
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')), $tbids);
							break;		
						case '_MI_BENCHMARK_TESTING':
							include XOOPS_ROOT_PATH.'/class/template.php';
							if ($testnum = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'))) { $testnum++; } else { $testnum=1; }
							if ($data = XoopsCache::read('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test')))) {
								$GLOBALS['results'][$test->getVar('test')][0]['counter']=$test->getVar('tests_ran_smarty');
								if ($testnum<=1) {
									$testnum = ceil(count($test->getVar('tbids_smarty'))/count($data));
								}
								foreach($data as $tbid => $var) {
									$table = $tables_handler->get($tbid);
									$_tHandler = new BenchmarkTesterHandler($GLOBALS['xoopsDB'], $table);
									$_tHandler->initialise($GLOBALS['xoopsDB'], $table);
									$objects = $_tHandler->getObjects(NULL, false); 
									
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
							XoopsCache::write('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'), $testnum, 60*60*24*2);
							if ($testnum >= $GLOBALS['xoopsModuleConfig']['smarty_tests_num']) {
								echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Last Test: 100% of ' .$GLOBALS['xoopsModuleConfig']['smarty_tests_num'].' benchmarking test\'s done'.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								XoopsCache::delete('benchmark_data_'.$test->getVar('tid').'_'.strtolower($test->getVar('test').'_testnumber'));
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_smarty')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('took_smarty_seconds', $test->getVar('took_smarty_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_smarty', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
								$test->setVar('stage', '_MI_BENCHMARK_CLEANUP');
							} else {
								echo 'Test: ' . $testnum . ' of ' .$GLOBALS['xoopsModuleConfig']['smarty_tests_num'].(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
								$tables = $tables_handler->getObjects(new Criteria('tbid', "(".implode(",", $test->getVar('tbids_smarty')).')', 'IN'), true);
								$results_handler->saveResults($test, $GLOBALS['results'], $tables, $testnum);
								$test->setVar('stage', '_MI_BENCHMARK_PREPARED');
								$test->setVar('took_smarty_seconds', $test->getVar('took_smarty_seconds')+$GLOBALS['results'][$test->getVar('test')][0]['took']);
								$test->setVar('tests_ran_smarty', $GLOBALS['results'][$test->getVar('test')][0]['counter']);
							}
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
					    	$results_handler =& xoops_getmodulehandler('results', $GLOBALS['xoopsModule']->getVar('dirname'));
					    	$history = $results_handler->getMinMaxAvgSumValues($test);
					    	$test->setVars($history);
					    	$test->setVar('ended', time());
							foreach($history as $field => $value) {
								echo ucfirst($field).': '.$value.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
							}
					    	
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
					echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Stage G Finished: '.date('Y-m-d H:i:s').(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
					break;
				case '_MI_BENCHMARK_FINISHED':
					$test->setVar('ended', time());
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
					XoopsCache::delete('benchmark_currently_doing_test');
					exit(0);
					break;
			}
			echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Next Stage: '.constant($test->getVar('stage')).(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
			echo 'Next Test: '.constant($test->getVar('test')).(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
			if (!$tests_handler->insert($test, true)) {
				xoops_error($GLOBALS['xoopsDB']->error(), $GLOBALS['xoopsDB']->errno());
			}
			XoopsCache::write('benchmark_current_test', $test->getVar('tid'));
		} else {
			XoopsCache::delete('benchmark_current_test');
		}
	} else {
		XoopsCache::delete('benchmark_current_test');
	}
	
	echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Test Values: '.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
	
	if (!is_array($history)) {
		$history = array();
	}
	foreach($test->toArray(true) as $field => $value) {
		if (!empty($value)&&!in_array($field, array_keys($history))&&$value!=0) {
			if (!is_array($value)&&!is_object($value)) {
				echo ucfirst($field).': '.(defined($value)?constant($value):$value).(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
			} else {
				if (substr($field,0, strlen('tbids'))=='tbids') {
					echo ucfirst($field).': array('.implode(', ',$value).')'.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");	
				} else {
					foreach($value as $fieldb => $valueb) {
						if (!empty($valueb)&&!in_array($fieldb, array_keys($history))&&$valueb!=0) {
							if (!is_array($valueb)&&!is_object($valueb))
								echo ucfirst($field).'.'.ucfirst($fieldb).': '.(defined($valueb)?constant($valueb):$valueb).(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
							else 
								echo ucfirst($field).'.'.ucfirst($fieldb).': array('.implode(', ',$valueb).')'.(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
						}
					}
				}
			}
		}
	}
	echo (isset($_SERVER['HTTP_HOST'])?'<br/>':"\n").'Cron Terminated: '.date('Y-m-d H:i:s').(isset($_SERVER['HTTP_HOST'])?'<br/>':"\n");
	XoopsCache::delete('benchmark_currently_doing_test');
?>