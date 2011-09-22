<?php

	function benchmark_test_get_form($object) {
		
		xoops_loadLanguage('forms', $GLOBALS['xoopsModule']->getVar('dirname'));
		
		if (!is_object($object)) {
			$handler = xoops_getmodulehandler('tests', $GLOBALS['xoopsModule']->getVar('dirname'));
			$object = $handler->create(); 
		}
		
		if ($object->isNew())
			$sform = new XoopsThemeForm(_FRM_BENCHMARK_FORM_TESTS_ISNEW, 'tests', 'index.php', 'post');
		else
			$sform = new XoopsThemeForm(sprintf(_FRM_BENCHMARK_FORM_TESTS_EDIT, $object->getVar('name')), 'test', 'index.php', 'post');
		
		$id = $object->getVar('tid');
		if (empty($id)) $id = '0';
		
		$ele = array();	
		$ele['op'] = new XoopsFormHidden('op', 'tests');
		$ele['fct'] = new XoopsFormHidden('fct', 'save');
		$ele['id'] = new XoopsFormHidden('id', $id);
		
		$ele['name'] = new XoopsFormText(_FRM_BENCHMARK_FORM_TESTS_NAME, $id.'[name]', 35,128, $object->getVar('name'));
		$ele['name']->setDescription(_FRM_BENCHMARK_FORM_TESTS_NAME_DESC);
		$ele['testing'] = new XoopsFormText(_FRM_BENCHMARK_FORM_TESTS_TESTING, $id.'[testing]', 35,128, $object->getVar('testing'));
		$ele['testing']->setDescription(_FRM_BENCHMARK_FORM_TESTS_TESTING_DESC);
    	
		$note_configs = array();
		$note_configs['name'] = $id.'[note]';
		$note_configs['value'] = $object->getVar('note');
		$note_configs['rows'] = 35;
		$note_configs['cols'] = 60;
		$note_configs['width'] = "100%";
		$note_configs['height'] = "400px";
		$note_configs['editor'] = $GLOBALS['xoopsModuleConfig']['editor'];
		$ele['note'] = new XoopsFormEditor(_FRM_BENCHMARK_FORM_TESTS_NOTE, $note_configs['name'], $note_configs);
		$ele['note']->setDescription(_FRM_BENCHMARK_FORM_TESTS_NOTE_DESC);

		$ele['platform'] = new BenchmarkFormSelectPlatform(_FRM_BENCHMARK_FORM_TESTS_PLATFORM, $id.'[platform]', $object->getVar('platform'));
		$ele['platform']->setDescription(_FRM_BENCHMARK_FORM_TESTS_PLATFORM_DESC);
		if ($object->getVar('started')>0)
			$ele['platform']->setExtra("disabled='disabled'");
			
		$ele['begin'] = new XoopsFormDateTime(_FRM_BENCHMARK_FORM_TESTS_BEGIN, $id.'[begin]', 15, $object->getVar('begin'));
		$ele['begin']->setDescription(_FRM_BENCHMARK_FORM_TESTS_BEGIN_DESC);
    	if ($object->getVar('started')>0)
			$ele['begin']->setExtra("disabled='disabled'");
			
		if ($object->getVar('created')>0) {
			$ele['created'] = new XoopsFormLabel(_FRM_BENCHMARK_FORM_TESTS_CREATED, date(_DATESTRING, $object->getVar('created')));
		}
		
		if ($object->getVar('updated')>0) {
			$ele['updated'] = new XoopsFormLabel(_FRM_BENCHMARK_FORM_TESTS_UPDATED, date(_DATESTRING, $object->getVar('updated')));
		}
		
		if ($object->getVar('actioned')>0) {
			$ele['actioned'] = new XoopsFormLabel(_FRM_BENCHMARK_FORM_TESTS_ACTIONED, date(_DATESTRING, $object->getVar('actioned')));
		}	
		
		$ele['submit'] = new XoopsFormButton('', 'submit', _SUBMIT, 'submit');
		
		$required = array('name', 'testing', 'platform');
		
		foreach($ele as $id => $obj)			
			if (in_array($id, $required))
				$sform->addElement($ele[$id], true);			
			else
				$sform->addElement($ele[$id], false);
		
		return $sform->render();
		
	}

	function benchmark_field_get_form($object) {
		
		xoops_loadLanguage('forms', $GLOBALS['xoopsModule']->getVar('dirname'));
		
		if (!is_object($object)) {
			$handler = xoops_getmodulehandler('fields', $GLOBALS['xoopsModule']->getVar('dirname'));
			$object = $handler->create(); 
		}
		
		if ($object->isNew())
			$sform = new XoopsThemeForm(_FRM_BENCHMARK_FORM_FIELDS_ISNEW, 'fields', 'index.php', 'post');
		else
			$sform = new XoopsThemeForm(sprintf(_FRM_BENCHMARK_FORM_FIELDS_EDIT, $object->getVar('name')), 'fields', 'index.php', 'post');
		
		$id = $object->getVar('tid');
		if (empty($id)) $id = '0';
		
		$ele = array();	
		$ele['op'] = new XoopsFormHidden('op', 'fields');
		$ele['fct'] = new XoopsFormHidden('fct', 'save');
		$ele['id'] = new XoopsFormHidden('id', $id);
		
		$ele['name'] = new XoopsFormText(_FRM_BENCHMARK_FORM_FIELDS_NAME, $id.'[name]', 35,128, $object->getVar('name'));
		$ele['name']->setDescription(_FRM_BENCHMARK_FORM_FIELDS_NAME_DESC);
		$ele['create_sql'] = new XoopsFormText(_FRM_BENCHMARK_FORM_FIELDS_CREATE_SQL, $id.'[create_sql]', 35,128, $object->getVar('create_sql'));
		$ele['create_sql']->setDescription(_FRM_BENCHMARK_FORM_FIELDS_CREATE_SQL_DESC);
		$ele['type'] = new BenchmarkFormSelectFieldType(_FRM_BENCHMARK_FORM_FIELDS_TYPE, $id.'[type]', $object->getVar('type'));
		$ele['type']->setDescription(_FRM_BENCHMARK_FORM_FIELDS_TYPE_DESC);
		$ele['required'] = new BenchmarkFormSelectYN(_FRM_BENCHMARK_FORM_FIELDS_REQUIRED, $id.'[required]', $object->getVar('required'));
		$ele['required']->setDescription(_FRM_BENCHMARK_FORM_FIELDS_REQUIRED_DESC);
		$ele['primarykey'] = new BenchmarkFormSelectYN(_FRM_BENCHMARK_FORM_FIELDS_PRIMARYKEY, $id.'[primarykey]', $object->getVar('primarykey'));
		$ele['primarykey']->setDescription(_FRM_BENCHMARK_FORM_FIELDS_PRIMARYKEY_DESC);
		$ele['key'] = new XoopsFormText(_FRM_BENCHMARK_FORM_FIELDS_KEY, $id.'[key]', 25,35, $object->getVar('key'));
		$ele['key']->setDescription(_FRM_BENCHMARK_FORM_FIELDS_KEY_DESC);
		$ele['alter_sql'] = new XoopsFormText(_FRM_BENCHMARK_FORM_FIELDS_ALTER_SQL, $id.'[alter_sql]', 35,128, $object->getVar('alter_sql'));
		$ele['alter_sql']->setDescription(_FRM_BENCHMARK_FORM_FIELDS_ALTER_SQL_DESC);
		$ele['alter_type'] = new BenchmarkFormSelectFieldType(_FRM_BENCHMARK_FORM_FIELDS_ALTER_TYPE, $id.'[alter_type]', $object->getVar('alter_type'));
		$ele['alter_type']->setDescription(_FRM_BENCHMARK_FORM_FIELDS_ALTER_TYPE_DESC);
		$ele['size'] = new XoopsFormText(_FRM_BENCHMARK_FORM_FIELDS_SIZE, $id.'[size]', 15,20, $object->getVar('size'));
		$ele['size']->setDescription(_FRM_BENCHMARK_FORM_FIELDS_SIZE_DESC);
		$ele['recipricol'] = new XoopsFormText(_FRM_BENCHMARK_FORM_FIELDS_RECIPRICOL, $id.'[recipricol]', 15,20, $object->getVar('recipricol'));
		$ele['recipricol']->setDescription(_FRM_BENCHMARK_FORM_FIELDS_RECIPRICOL_DESC);

		if ($object->getVar('created')>0) {
			$ele['created'] = new XoopsFormLabel(_FRM_BENCHMARK_FORM_FIELDS_CREATED, date(_DATESTRING, $object->getVar('created')));
		}
		
		if ($object->getVar('updated')>0) {
			$ele['updated'] = new XoopsFormLabel(_FRM_BENCHMARK_FORM_FIELDS_UPDATED, date(_DATESTRING, $object->getVar('updated')));
		}
		
		if ($object->getVar('actioned')>0) {
			$ele['actioned'] = new XoopsFormLabel(_FRM_BENCHMARK_FORM_FIELDS_ACTIONED, date(_DATESTRING, $object->getVar('actioned')));
		}	
		
		$ele['submit'] = new XoopsFormButton('', 'submit', _SUBMIT, 'submit');
		
		$required = array('name', 'create_sql', 'type', 'required', 'primarykey', 'alter_sql', 'alter_type', 'size', 'recipricol');
		
		foreach($ele as $id => $obj)			
			if (in_array($id, $required))
				$sform->addElement($ele[$id], true);			
			else
				$sform->addElement($ele[$id], false);
		
		return $sform->render();
		
	}