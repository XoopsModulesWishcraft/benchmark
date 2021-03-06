<?php
$modversion['name'] = _MI_BENCHMARK_NAME;
$modversion['version'] = 1.02;
$modversion['description'] = _MI_BENCHMARK_DESC;
$modversion['credits'] = "Benchmark is a GNU module for MySQL and XOOPS Benchmarking";
$modversion['author'] = "wishcraft (simon@chronolabs.coop)";
$modversion['help'] = "";
$modversion['license'] = "GPL/GNU";
$modversion['official'] = 1;
$modversion['image'] = "images/benchmark_slogo.png";
$modversion['dirname'] = "irc";

$modversion['hasMain'] = 1;

// Admin things
$modversion['hasAdmin'] = 0;
$modversion['adminindex'] = "admin/admin.php";
$modversion['adminmenu'] = "admin/menu.php";

// Mysql file
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

// Tables
$i=0;
$modversion['tables'][$i++] = "benchmark_fields";
$modversion['tables'][$i++] = "benchmark_results";
$modversion['tables'][$i++] = "benchmark_tables";
$modversion['tables'][$i++] = "benchmark_tests";

$i=0;
xoops_load('XoopsEditorHandler');
$editor_handler = XoopsEditorHandler::getInstance();
foreach ($editor_handler->getList(false) as $id => $val)
	$options[$val] = $id;
	
$i++;
$modversion['config'][$i]['name'] = 'editor';
$modversion['config'][$i]['title'] = "_MI_BENCHMARK_EDITOR";
$modversion['config'][$i]['description'] = "_MI_BENCHMARK_EDITOR_DESC";
$modversion['config'][$i]['formtype'] = 'select';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'tinymce';
$modversion['config'][$i]['options'] = $options;

$i++;
$modversion['config'][$i]['name'] = 'email_addresses';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_EMAIL_ADDRESSES';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_EMAIL_ADDRESSES_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'textarea';
$modversion['config'][$i]['default'] = $GLOBALS['xoopsConfig']['adminmail'];

$i++;
$modversion['config'][$i]['name'] = 'send_email';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_SEND_EMAIL';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_SEND_EMAIL_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = true;

$i++;
$modversion['config'][$i]['name'] = 'memory_limit';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_MEMORY_FOR_CRON';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_MEMORY_FOR_CRON_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '128M';

$i++;
$modversion['config'][$i]['name'] = 'time_limit';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_SECONDS_FOR_CRON';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_SECONDS_FOR_CRON_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 60*60*2;

$i++;
$modversion['config'][$i]['name'] = 'interval_of_cron';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_INTERVAL_OF_CRON';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_INTERVAL_OF_CRON_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 60*2;

$i++;
$modversion['config'][$i]['name'] = 'step_new_begin';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_STEP_NEW_BEGIN';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_STEP_NEW_BEGIN_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 60*60*4;


$i++;
$modversion['config'][$i]['name'] = 'create_table';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_CREATE_TABLE';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_CREATE_TABLE_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = true;

$i++;
$modversion['config'][$i]['name'] = 'create_table_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_CREATE_TABLE_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_CREATE_TABLE_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 100;

$i++;
$modversion['config'][$i]['name'] = 'alter_table';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_ALTER_TABLE';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_ALTER_TABLE_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = true;

$i++;
$modversion['config'][$i]['name'] = 'alter_table_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_ALTER_TABLE_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_ALTER_TABLE_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 200;

$i++;
$modversion['config'][$i]['name'] = 'alter_records_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_ALTER_TABLE_RECORDS';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_ALTER_TABLE_RECORDS_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1000;

$i++;
$modversion['config'][$i]['name'] = 'rename_table';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_RENAME_TABLE';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_RENAME_TABLE_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = true;

$i++;
$modversion['config'][$i]['name'] = 'rename_table_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_RENAME_TABLE_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_RENAME_TABLE_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 50;

$i++;
$modversion['config'][$i]['name'] = 'insert_records';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_INSERT_RECORDS';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_INSERT_RECORDS_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = true;

$i++;
$modversion['config'][$i]['name'] = 'insert_records_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_INSERT_RECORDS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_INSERT_RECORDS_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1000;

$i++;
$modversion['config'][$i]['name'] = 'insert_tests_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_INSERT_TESTS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_INSERT_TESTS_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 200;

$i++;
$modversion['config'][$i]['name'] = 'delete_records';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_DELETE_RECORDS';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_DELETE_RECORDS_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = true;

$i++;
$modversion['config'][$i]['name'] = 'delete_records_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_DELETE_RECORDS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_DELETE_RECORDS_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1000;

$i++;
$modversion['config'][$i]['name'] = 'delete_tests_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_DELETE_TESTS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_DELETE_TESTS_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 100;

$i++;
$modversion['config'][$i]['name'] = 'delete_all_records';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_DELETE_ALL_RECORDS';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_DELETE_ALL_RECORDS_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = true;

$i++;
$modversion['config'][$i]['name'] = 'delete_all_records_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_DELETE_ALL_RECORDS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_DELETE_ALL_RECORDS_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1000;

$i++;
$modversion['config'][$i]['name'] = 'delete_all_tests_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_DELETE_ALL_TESTS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_DELETE_ALL_TESTS_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 100;

$i++;
$modversion['config'][$i]['name'] = 'select_records';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_SELECT_RECORDS';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_SELECT_RECORDS_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = true;

$i++;
$modversion['config'][$i]['name'] = 'select_records_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_SELECT_RECORDS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_SELECT_RECORDS_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1000;

$i++;
$modversion['config'][$i]['name'] = 'select_tests_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_SELECT_TESTS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_SELECT_TESTS_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 200;

$i++;
$modversion['config'][$i]['name'] = 'update_records';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_UPDATE_RECORDS';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_UPDATE_RECORDS_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = true;

$i++;
$modversion['config'][$i]['name'] = 'update_records_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_UPDATE_RECORDS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_UPDATE_RECORDS_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1000;

$i++;
$modversion['config'][$i]['name'] = 'update_tests_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_UPDATE_TESTS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_UPDATE_TESTS_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 200;

$i++;
$modversion['config'][$i]['name'] = 'update_all_records';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_UPDATE_ALL_RECORDS';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_UPDATE_ALL_RECORDS_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = true;

$i++;
$modversion['config'][$i]['name'] = 'update_all_records_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_UPDATE_ALL_RECORDS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_UPDATE_ALL_RECORDS_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 200;

$i++;
$modversion['config'][$i]['name'] = 'update_all_tests_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_UPDATE_ALL_TESTS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_UPDATE_ALL_TEST_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 200;

$i++;
$modversion['config'][$i]['name'] = 'smarty';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_SMARTY';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_SMARTY_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = true;

$i++;
$modversion['config'][$i]['name'] = 'smarty_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_SMARTY_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_SMARTY_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 200;

$i++;
$modversion['config'][$i]['name'] = 'smarty_tests_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_SMARTY_TESTS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_SMARTY_TESTS_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 100;

$i++;
$modversion['config'][$i]['name'] = 'engines';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_TEST_ENGINES';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_TEST_ENGINES_DESC';
$modversion['config'][$i]['formtype'] = 'select_multi';
$modversion['config'][$i]['valuetype'] = array(_MI_BENCHMARK_TEST_ENGINES_INNODB => 'INNODB', _MI_BENCHMARK_TEST_ENGINES_MYISAM => 'MYISAM');
$modversion['config'][$i]['default'] = array('INNODB', 'MYISAM');

$i++;
$modversion['config'][$i]['name'] = 'charset';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_TEST_CHARSET';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_TEST_CHARSET_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'utf8';

$i++;
$modversion['config'][$i]['name'] = 'characters';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_TEST_CHARACTERS';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_TEST_CHARACTERS_DESC';
$modversion['config'][$i]['formtype'] = 'textarea';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = _MI_BENCHMARK_TEST_CHARACTERS_DEFAULT;

$i++;
$modversion['config'][$i]['name'] = 'numbers';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_TEST_NUMBERS';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_TEST_NUMBERS_DESC';
$modversion['config'][$i]['formtype'] = 'textarea';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = _MI_BENCHMARK_TEST_NUMBERS_DEFAULT;

$options = array();
foreach(array('bigint','mediumint','smallint','int','tinyint','float','decimal','double','enum_varchar','enum_int','varchar','mediumtext','text','date','time','datetime','year') as $value) {
	$options[ucfirst($value)] = $value;
}
$i++;
$modversion['config'][$i]['name'] = 'field_types';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_FIELD_TYPES';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_FIELD_TYPES_DESC';
$modversion['config'][$i]['formtype'] = 'select_multi';
$modversion['config'][$i]['valuetype'] = $options;
$modversion['config'][$i]['default'] = array('bigint','mediumint','smallint','int','tinyint','float','decimal','double','enum_varchar','enum_int','varchar','mediumtext','text','date','time','datetime','year');

$i++;
$modversion['config'][$i]['name'] = 'spectrum';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_TEST_SPECTRUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_TEST_SPECTRUM_DESC';
$modversion['config'][$i]['formtype'] = 'select';
$modversion['config'][$i]['valuetype'] = array(_MI_BENCHMARK_TEST_SPECTRUM_RANDOM => 'random', _MI_BENCHMARK_TEST_SPECTRUM_EACH => 'each');
$modversion['config'][$i]['default'] = 'each';

$i++;
$modversion['config'][$i]['name'] = 'fields_num';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_FIELDS_NUM';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_FIELDS_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 18;

$i++;
$modversion['config'][$i]['name'] = 'table_name';
$modversion['config'][$i]['title'] = '_MI_BENCHMARK_TABLE_NAME';
$modversion['config'][$i]['description'] = '_MI_BENCHMARK_TABLE_NAME_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'benchmark_tester';

$i=0;
$i++;
$modversion['templates'][$i]['file'] = 'benchmark_index.html';
$modversion['templates'][$i]['description'] = 'Benchmark Index Template';
$i++;
$modversion['templates'][$i]['file'] = 'benchmark_create_test.html';
$modversion['templates'][$i]['description'] = 'Benchmark Create Test Template';
$i++;
$modversion['templates'][$i]['file'] = 'benchmark_edit_test.html';
$modversion['templates'][$i]['description'] = 'Benchmark Edit Test Template';
$i++;
$modversion['templates'][$i]['file'] = 'benchmark_list_test.html';
$modversion['templates'][$i]['description'] = 'Benchmark Edit Test Template';
$i++;
$modversion['templates'][$i]['file'] = 'benchmark_create_fields.html';
$modversion['templates'][$i]['description'] = 'Benchmark Create Test Template';
$i++;
$modversion['templates'][$i]['file'] = 'benchmark_edit_fields.html';
$modversion['templates'][$i]['description'] = 'Benchmark Edit Test Template';
$i++;
$modversion['templates'][$i]['file'] = 'benchmark_list_fields.html';
$modversion['templates'][$i]['description'] = 'Benchmark Edit Test Template';
$i++;
$modversion['templates'][$i]['file'] = 'benchmark_report_pdf.html';
$modversion['templates'][$i]['description'] = 'Benchmark Report PDF Template';
$i++;
$modversion['templates'][$i]['file'] = 'benchmark_report.html';
$modversion['templates'][$i]['description'] = 'Benchmark Report PDF Template';
$i++;
$modversion['templates'][$i]['file'] = 'benchmark_test.html';
$modversion['templates'][$i]['description'] = 'Benchmark Smarty Test Template';
?>