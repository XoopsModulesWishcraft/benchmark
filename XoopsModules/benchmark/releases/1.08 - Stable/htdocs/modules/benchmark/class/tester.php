<?php

if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}
/**
 * Class for Benchmarking
 * @author Simon Roberts (simon@chronolabs.coop)
 * @copyright copyright (c) 2000-2009 XOOPS.org
 * @package kernel
 */
class BenchmarkTester extends XoopsObject
{
	var $_tHandler = NULL;
	var $_fHandler = NULL;
	
    function __construct($fields, $table)
    {
    	$this->_tHandler = xoops_getmodulehandler('tests', $GLOBALS['xoopsModule']->getVar('dirname'));
    	$this->_fHandler = xoops_getmodulehandler('fields', $GLOBALS['xoopsModule']->getVar('dirname'));
    	
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('tid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('created', XOBJ_DTYPE_INT, null, false);
        $this->init($fields, $table);
    }

    function initialise($fields, $table)
    {
        $this->__construct($fields, $table);
    }

    /**
    * Initiate variables
    * @param array $fields field information array of {@link XoopsProfileField} objects
    */
    function init($fields, $table)
    {
    	static $fld = array();
    	
        if (is_array($fields) && count($fields) > 0) {
            foreach (array_keys($fields) as $key ) {
   	    		$exploded = explode('_', $key);
	    		if (is_numeric($exploded[1]))
	    			$fid = $exploded[1];
	    		else 
	    			$fid = $exploded[2];
	    		
	    		if (!isset($fld[$fid])&&!is_object($fld[$fid]))
	    			$fld[$fid] = $this->_fHandler->get($fid);
	    			
            	if ($table->getVar('altered')=='_NO') {
	            	switch ($fld[$fid]->getVar('type')){
	            		case 'bigint':
						case 'mediumint':
						case 'smallint':
						case 'int':
						case 'tinyint':
							$this->initVar($key, XOBJ_DTYPE_INT, null, false, $fld[$fid]->getVar('size'));
							break;
						case 'float':
							$this->initVar($key, XOBJ_DTYPE_FLOAT, null, false, $fld[$fid]->getVar('size'));
							break;
						case 'decimal':
						case 'double':
							$this->initVar($key, XOBJ_DTYPE_DECIMAL, null, false, $fld[$fid]->getVar('size'));
							break;
						case 'enum_varchar':
							$this->initVar($key, XOBJ_DTYPE_ENUM, null, false, $fld[$fid]->getVar('size'), false, benchmark_getVarcharEnumeration());
							break;
						case 'enum_int':
							$this->initVar($key, XOBJ_DTYPE_ENUM, null, false, $fld[$fid]->getVar('size'), false, benchmark_getNumericEnumeration());
							break;
						case 'varchar':
							$this->initVar($key, XOBJ_DTYPE_TXTBOX, null, false, $fld[$fid]->getVar('size'));
							break;
						case 'mediumtext':
							$this->initVar($key, XOBJ_DTYPE_TXTBOX, null, false, $fld[$fid]->getVar('size'));
							break;
						case 'text':
							$this->initVar($key, XOBJ_DTYPE_OTHER, null, false, $fld[$fid]->getVar('size'));
							break;
						case 'date':
						case 'time':
						case 'datetime':
						case 'year':
							$this->initVar($key, XOBJ_DTYPE_TXTBOX, null, false, $fld[$fid]->getVar('size'));
							break;
							
	            	}
            	} else {
            		switch ($fld[$fid]->getVar('altered_type')){
	            		case 'bigint':
						case 'mediumint':
						case 'smallint':
						case 'int':
						case 'tinyint':
							$this->initVar($key, XOBJ_DTYPE_INT, null, false, $fld[$fid]->getVar('size'));
							break;
						case 'float':
							$this->initVar($key, XOBJ_DTYPE_FLOAT, null, false, $fld[$fid]->getVar('size'));
							break;
						case 'decimal':
						case 'double':
							$this->initVar($key, XOBJ_DTYPE_DECIMAL, null, false, $fld[$fid]->getVar('size'));
							break;
						case 'enum_varchar':
							$this->initVar($key, XOBJ_DTYPE_ENUM, null, false, $fld[$fid]->getVar('size'), false, benchmark_getVarcharEnumeration());
							break;
						case 'enum_int':
							$this->initVar($key, XOBJ_DTYPE_ENUM, null, false, $fld[$fid]->getVar('size'), false, benchmark_getNumericEnumeration());
							break;
						case 'varchar':
							$this->initVar($key, XOBJ_DTYPE_TXTBOX, null, false, $fld[$fid]->getVar('size'));
							break;
						case 'mediumtext':
							$this->initVar($key, XOBJ_DTYPE_TXTBOX, null, false, $fld[$fid]->getVar('size'));
							break;
						case 'text':
							$this->initVar($key, XOBJ_DTYPE_OTHER, null, false, $fld[$fid]->getVar('size'));
							break;
						case 'date':
						case 'time':
						case 'datetime':
						case 'year':
							$this->initVar($key, XOBJ_DTYPE_TXTBOX, null, false, $fld[$fid]->getVar('size'));
							break;
							
	            	}
            	}
            }
        }
    }

	function toArray() {
		$ret = parent::toArray();
		foreach($ret as $field => $value) {
			if(defined($value))
				$ret[$field] = constant($value);
		}
		if ($this->getVar('created')>0)
			$ret['dates']['created'] = date(_DATESTRING, $this->getVar('created'));	
		
		if ($this->getVar('tid')<>0) {
			$test = $this->_tHandler->get($this->getVar('tid'));
			$ret['test'] = $test->toArray(true);
		}			
		return $ret;
	}
}


/**
* XOOPS Benchmarking handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS user class objects.
*
* @author  Simon Roberts <simon@chronolabs.coop>
* @package kernel
*/
class BenchmarkTesterHandler extends XoopsPersistableObjectHandler
{
	var $_fields = array();
	var $_table = NULL;
	var $_table_exists = false;
	    
    function __construct(&$db, $table) 
    {
    	if (is_object($table)) {
	        parent::__construct($db, $table->getVar('name'), 'BenchmarkTester', "id", "tid");
	        $this->_table = $table;
	        $this->_fields = $table->getVar('fids');
	        if ($table->getVar('created')>0)
	        	$this->_table_exists = true;
    	}
    }

	function intialise(&$db, $table) 
    {
    	$this->__construct($db, $table);
    }
    
    function &create($isNew = true)
    {
    	$GLOBALS['timer']['create']['start'] = microtime(true);
        $obj = new BenchmarkTester($this->_fields, $this->_table);
        $obj->initialise($this->_fields, $this->_table);
        if ($isNew === true) {
            $obj->setNew();
        }
        $GLOBALS['timer']['create']['end'] = microtime(true);
        return $obj;
    }
    
    function getObjects($criteria, $id_as_key=true, $as_object=true) {
		$GLOBALS['timer']['getobjects']['start'] = microtime(true);
    	$select = "*";
        $limit = null;
        $start = null;
        $sql = "SELECT {$select} FROM `".$GLOBALS['xoopsDB']->prefix($this->_table->getVar('name'))."`";
        if (isset($criteria) && is_subclass_of($criteria, "criteriaelement")) {
            $sql .= " " . $criteria->renderWhere();
            if ($sort = $criteria->getSort()) {
                $sql .= " ORDER BY {$sort} " . $criteria->getOrder();
                $orderSet = true;
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        if (empty($orderSet)) {
            // $sql .= " ORDER BY `{'id'}` DESC";
        }
        $result = $GLOBALS['xoopsDB']->query($sql, $limit, $start);
        $ret = array();
        if ($as_object) {
            while ($myrow = $GLOBALS['xoopsDB']->fetchArray($result)) {
                $object =& $this->create(false);
                $object->assignVars($myrow);
                if ($id_as_key) {
                    $ret[$myrow['id']] = $object;
                } else {
                    $ret[] = $object;
                }
                unset($object);
            }
        } else {
            $object =& $this->create(false);
            while ($myrow = $GLOBALS['xoopsDB']->fetchArray($result)) {
                $object->assignVars($myrow);
                if ($id_as_key) {
                    $ret[$myrow['id']] = $object->getValues(array_keys($myrow));
                } else {
                    $ret[] = $object->getValues(array_keys($myrow));
                }
            }
            unset($object);
        }
        $GLOBALS['timer']['getobjects']['end'] = microtime(true);
        return $ret;
    }
    
 	function cleanVars(&$object)
    {
        $ts =& MyTextSanitizer::getInstance();
        $errors = array();

        $vars = $object->getVars();
        $object->cleanVars = array();
        foreach ($vars as $k => $v) {
            if (!$v["changed"]) {
                continue;
            }
            $cleanv = $v['value'];
            switch ($v["data_type"]) {
                case XOBJ_DTYPE_UNICODE_TXTBOX:
                    if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue;
                    }
                    $cleanv = xoops_convert_encode($cleanv);
                    if (isset($v['maxlength']) && strlen($cleanv) > intval($v['maxlength'])) {
                        $errors[] = sprintf(_XOBJ_ERR_SHORTERTHAN, $k, intval($v['maxlength']));
                        continue;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                    } else {
                        $cleanv = $ts->censorString($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $GLOBALS['xoopsDB']->quote($cleanv));
                    break;

                case XOBJ_DTYPE_UNICODE_TXTAREA:
                    if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue;
                    }
                    $cleanv = xoops_convert_encode($cleanv);
                    if (!$v['not_gpc']) {
                        if (!empty($vars['dohtml']['value'])) {
                            $cleanv = $ts->textFilter($cleanv);
                        }
                        $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                    } else {
                        $cleanv = $ts->censorString($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $GLOBALS['xoopsDB']->quote($cleanv));
                    break;

                case XOBJ_DTYPE_TXTBOX:
                    if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue;
                    }
                    if (isset($v['maxlength']) && strlen($cleanv) > intval($v['maxlength'])) {
                        $errors[] = sprintf(_XOBJ_ERR_SHORTERTHAN, $k, intval($v['maxlength']));
                        continue;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                    } else {
                        $cleanv = $ts->censorString($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $GLOBALS['xoopsDB']->quote($cleanv));
                    break;

                case XOBJ_DTYPE_TXTAREA:
                    if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue;
                    }
                    if (!$v['not_gpc']) {
                        if (!empty($vars['dohtml']['value'])) {
                            $cleanv = $ts->textFilter($cleanv);
                        }
                        $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                    } else {
                        $cleanv = $ts->censorString($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $GLOBALS['xoopsDB']->quote($cleanv));
                    break;

                case XOBJ_DTYPE_SOURCE:
                    $cleanv = trim($cleanv);
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($cleanv);
                    } else {
                        $cleanv = $cleanv;
                    }
                    $cleanv = str_replace('\\"', '"', $GLOBALS['xoopsDB']->quote($cleanv));
                    break;
                // Should not be used!
                case XOBJ_DTYPE_UNICODE_EMAIL:
                    $cleanv = trim($cleanv);
                    if ($v['required'] && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $GLOBALS['xoopsDB']->quote(xoops_convert_encode($cleanv)));
                    break;

                case XOBJ_DTYPE_EMAIL:
                    $cleanv = trim($cleanv);
                    if ($v['required'] && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue;
                    }
                    if ($cleanv != '' && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $cleanv)) {
                        $errors[] = "Invalid Email";
                        continue;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $GLOBALS['xoopsDB']->quote($cleanv));
                    break;

                // Should not be used!
                case XOBJ_DTYPE_UNICODE_URL:
                    $cleanv = trim($cleanv);
                    if ($v['required'] && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue;
                    }
                    if ($cleanv != '' && !preg_match("/^http[s]*:\/\//i", $cleanv)) {
                        $cleanv = 'http://' . $cleanv;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $GLOBALS['xoopsDB']->quote(xoops_convert_encode($cleanv)));
                    break;
                case XOBJ_DTYPE_URL:
                    $cleanv = trim($cleanv);
                    if ($v['required'] && $cleanv == '') {
                        $errors[] = sprintf(_XOBJ_ERR_REQUIRED, $k);
                        continue;
                    }
                    if ($cleanv != '' && !preg_match("/^http[s]*:\/\//i", $cleanv)) {
                        $cleanv = 'http://' . $cleanv;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($cleanv);
                    }
                    $cleanv = str_replace('\\"', '"', $GLOBALS['xoopsDB']->quote($cleanv));
                    break;

                // Should not be used!
                case XOBJ_DTYPE_UNICODE_OTHER:
                    $cleanv = str_replace('\\"', '"', $GLOBALS['xoopsDB']->quote(xoops_convert_encode($cleanv)));
                    break;

                case XOBJ_DTYPE_OTHER:
                    $cleanv = str_replace('\\"', '"', $GLOBALS['xoopsDB']->quote($cleanv));
                    break;

                case XOBJ_DTYPE_INT:
                    $cleanv = intval($cleanv);
                    break;

                case XOBJ_DTYPE_FLOAT:
                    $cleanv = floatval($cleanv);
                    break;

                case XOBJ_DTYPE_DECIMAL:
                    $cleanv = doubleval($cleanv);
                    break;

                // Should not be used!
                case XOBJ_DTYPE_UNICODE_ARRAY:
                    if (!$v['not_gpc']) {
                        $cleanv = array_map(array(&$ts , "stripSlashesGPC"), $cleanv);
                    }
                    foreach (array_keys($cleanv) as $key) {
                        $cleanv[$key] = str_replace('\\"', '"', addslashes($cleanv[$key]));
                    }
                    // TODO: Not encoding safe, should try base64_encode -- phppp
                    $cleanv = "'" . serialize(array_walk($cleanv, 'xoops_aw_encode')) . "'";
                    break;

                case XOBJ_DTYPE_ARRAY:
                    if (!$v['not_gpc']) {
                        $cleanv = array_map(array(&$ts , "stripSlashesGPC"), $cleanv);
                    }
                    foreach (array_keys($cleanv) as $key) {
                        $cleanv[$key] = str_replace('\\"', '"', addslashes($cleanv[$key]));
                    }
                    // TODO: Not encoding safe, should try base64_encode -- phppp
                    $cleanv = "'" . serialize($cleanv) . "'";
                    break;

                case XOBJ_DTYPE_STIME:
                case XOBJ_DTYPE_MTIME:
                case XOBJ_DTYPE_LTIME:
                    $cleanv = !is_string($cleanv) ? intval($cleanv) : strtotime($cleanv);
                    break;

                default:
                    $cleanv = str_replace('\\"', '"', $GLOBALS['xoopsDB']->quote($cleanv));
                    break;
            }
            $object->cleanVars[$k] = $cleanv;
        }
        if (!empty($errors)) {
            $object->setErrors($errors);
        }
        $object->unsetDirty();
        return empty($errors) ? true : false;
    }
    
    function insert(&$object, $force = true)
    {
    	$GLOBALS['timer']['insert']['start'] = microtime(true);
       	if (!$object->isDirty()) {
            trigger_error("Data entry is not inserted - the object '" . get_class($object) . "' is not dirty", E_USER_NOTICE);
            $GLOBALS['timer']['insert']['end'] = microtime(true);
            return $object->getVar('id');
        }
        if (!$this->cleanVars($object)) {
            trigger_error("Insert failed in method 'cleanVars' of object '" . get_class($object) . "'", E_USER_WARNING);
            $GLOBALS['timer']['insert']['end'] = microtime(true);
            return $object->getVar('id');
        }
        $queryFunc = empty($force) ? "query" : "queryF";

        if ($object->isNew()) {
            $sql = "INSERT INTO `" . $GLOBALS['xoopsDB']->prefix($this->_table->getVar('name')) . "`";
            if (!empty($object->cleanVars)) {
                $keys = array_keys($object->cleanVars);
                $vals = array_values($object->cleanVars);
                $sql .= " (`" . implode("`, `", $keys) . "`) VALUES (" . implode(",", $vals) . ")";
            } else {
                trigger_error("Data entry is not inserted - no variable is changed in object of '" . get_class($object) . "'", E_USER_NOTICE);
                $GLOBALS['timer']['insert']['end'] = microtime(true);
                return $object->getVar('id');
            }
            if (!$result = $GLOBALS['xoopsDB']->{$queryFunc}($sql)) {
            	xoops_error($sql, $GLOBALS['xoopsDB']->error().' - Error Number '.$GLOBALS['xoopsDB']->errno());
            	$GLOBALS['timer']['insert']['end'] = microtime(true);
                return false;
            }
            if (!$object->getVar('id') && $object_id = $GLOBALS['xoopsDB']->getInsertId()) {
                $object->assignVar('id', $object_id);
            }
        } else if (!empty($object->cleanVars)) {
            $keys = array();
            foreach ($object->cleanVars as $k => $v) {
                $keys[] = " `{$k}` = {$v}";
            }
            $sql = "UPDATE `" . $GLOBALS['xoopsDB']->prefix($this->_table->getVar('name')) . "` SET " . implode(",", $keys) . " WHERE `id` = " . $GLOBALS['xoopsDB']->quote($object->getVar('id'));
            if (!$result = $GLOBALS['xoopsDB']->{$queryFunc}($sql)) {
            	xoops_error($sql, $GLOBALS['xoopsDB']->error().' - Error Number '.$GLOBALS['xoopsDB']->errno());
            	$GLOBALS['timer']['insert']['end'] = microtime(true);
                return false;
            }
        }
        $GLOBALS['timer']['insert']['end'] = microtime(true);
        return $object->getVar('id');
    }

    function delete(&$object, $force = true)
    {
    	$GLOBALS['timer']['delete']['start'] = microtime(true);
        $whereclause = "`" . 'id' . "` = " . $GLOBALS['xoopsDB']->quote($object->getVar('id'));
        $sql = "DELETE FROM `" . $GLOBALS['xoopsDB']->prefix($this->_table->getVar('name')) . "` WHERE " . $whereclause;
        $queryFunc = empty($force) ? "query" : "queryF";
        $result = $GLOBALS['xoopsDB']->{$queryFunc}($sql);
        return empty($result) ? false : true;
        $GLOBALS['timer']['delete']['end'] = microtime(true);
        return $ret;
    }

    function deleteAll($criteria = null, $force = true, $asObject = false)
    {
    	$GLOBALS['timer']['deleteall']['start'] = microtime(true);
        if ($asObject) {
            $objects = $this->getAll($criteria);
            $num = 0;
            foreach (array_keys($objects) as $key) {
                $num += $this->delete($objects[$key], $force) ? 1 : 0;
            }
            unset($objects);
            $GLOBALS['timer']['deleteall']['end'] = microtime(true);
            return $num;
        }
        $queryFunc = empty($force) ? 'query' : 'queryF';
        $sql = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix($this->_table->getVar('name'));
        if (!empty($criteria)) {
            if (is_subclass_of($criteria, 'criteriaelement')) {
                $sql .= ' ' . $criteria->renderWhere();
            } else {
            	$GLOBALS['timer']['deleteall']['end'] = microtime(true);
                return false;
            }
        }
        if (!$GLOBALS['xoopsDB']->{$queryFunc}($sql)) {
       		xoops_error($sql, $GLOBALS['xoopsDB']->error().' - Error Number '.$GLOBALS['xoopsDB']->errno());
        	$GLOBALS['timer']['deleteall']['end'] = microtime(true);
            return false;
        }
        $GLOBALS['timer']['deleteall']['end'] = microtime(true);
        return $GLOBALS['xoopsDB']->getAffectedRows();
    }

    function updateAll($fieldname, $fieldvalue, $criteria = null, $force = false)
    {
        $GLOBALS['timer']['updateall']['start'] = microtime(true);
         $set_clause = "`{$fieldname}` = ";
        if (is_numeric($fieldvalue)) {
            $set_clause .= $fieldvalue;
        } else if (is_array($fieldvalue)) {
            $set_clause .= $GLOBALS['xoopsDB']->quote(implode(',', $fieldvalue));
        } else {
            $set_clause .= $GLOBALS['xoopsDB']->quote($fieldvalue);
        }
        $sql = 'UPDATE `' . $GLOBALS['xoopsDB']->prefix($this->_table->getVar('name')) . '` SET ' . $set_clause;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $queryFunc = empty($force) ? 'query' : 'queryF';
        $result = $GLOBALS['xoopsDB']->{$queryFunc}($sql);
        $GLOBALS['timer']['updateall']['end'] = microtime(true);
        return empty($result) ? false : true;
        
    }
	
	function getCount($criteria = null)
    {
        $field = '';
        $groupby = false;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            if ($criteria->groupby != '') {
                $groupby = true;
                $field = $criteria->groupby . ", ";
            }
        }
        $sql = "SELECT {$field} COUNT(*) FROM `" . $GLOBALS['xoopsDB']->prefix($this->_table->getVar('name')) . "`";
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $sql .= $criteria->getGroupby();
        }
        $result = $GLOBALS['xoopsDB']->query($sql);
        if (!$result) {
            return 0;
        }
        if ($groupby == false) {
            list ($count) = $GLOBALS['xoopsDB']->fetchRow($result);
            return $count;
        } else {
            $ret = array();
            while (list ($id, $count) = $GLOBALS['xoopsDB']->fetchRow($result)) {
                $ret[$id] = $count;
            }
            return $ret;
        }
    }
}
?>