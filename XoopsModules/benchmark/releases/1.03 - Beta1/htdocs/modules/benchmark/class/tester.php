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
	
    function __construct($fields, $table)
    {
    	$this->_tHandler = xoops_getmodulehandler('tests', $GLOBALS['xoopsModule']->getVar('dirname'));
    	
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('tid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('created', XOBJ_DTYPE_INT, null, false);
        $this->init($fields, $table);
    }

    function BenchmarkTester($fields, $table)
    {
        $this->__construct($fields, $table);
    }

    /**
    * Initiate variables
    * @param array $fields field information array of {@link XoopsProfileField} objects
    */
    function init($fields, $table)
    {
        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $key => $field ) {
            	if ($table->getVar('altered')=='_NO') {
	            	switch ($field['type']){
	            		case 'bigint':
						case 'mediumint':
						case 'smallint':
						case 'int':
						case 'tinyint':
							$this->initVar($key, XOBJ_DTYPE_INT, null, false, $field['size']);
							break;
						case 'float':
							$this->initVar($key, XOBJ_DTYPE_FLOAT, null, false, $field['size']);
							break;
						case 'decimal':
						case 'double':
							$this->initVar($key, XOBJ_DTYPE_DECIMAL, null, false, $field['size']);
							break;
						case 'enum_varchar':
							$this->initVar($key, XOBJ_DTYPE_ENUM, null, false, $field['size'], false, benchmark_getVarcharEnumeration());
							break;
						case 'enum_int':
							$this->initVar($key, XOBJ_DTYPE_ENUM, null, false, $field['size'], false, benchmark_getNumericEnumeration());
							break;
						case 'varchar':
							$this->initVar($key, XOBJ_DTYPE_TXTBOX, null, false, $field['size']);
							break;
						case 'mediumtext':
							$this->initVar($key, XOBJ_DTYPE_TXTBOX, null, false, $field['size']);
							break;
						case 'text':
							$this->initVar($key, XOBJ_DTYPE_OTHER, null, false, $field['size']);
							break;
						case 'date':
						case 'time':
						case 'datetime':
						case 'year':
							$this->initVar($key, XOBJ_DTYPE_TXTBOX, null, false, $field['size']);
							break;
							
	            	}
            	} else {
            		switch ($field['altered_type']){
	            		case 'bigint':
						case 'mediumint':
						case 'smallint':
						case 'int':
						case 'tinyint':
							$this->initVar($key, XOBJ_DTYPE_INT, null, false, $field['size']);
							break;
						case 'float':
							$this->initVar($key, XOBJ_DTYPE_FLOAT, null, false, $field['size']);
							break;
						case 'decimal':
						case 'double':
							$this->initVar($key, XOBJ_DTYPE_DECIMAL, null, false, $field['size']);
							break;
						case 'enum_varchar':
							$this->initVar($key, XOBJ_DTYPE_ENUM, null, false, $field['size'], false, benchmark_getVarcharEnumeration());
							break;
						case 'enum_int':
							$this->initVar($key, XOBJ_DTYPE_ENUM, null, false, $field['size'], false, benchmark_getNumericEnumeration());
							break;
						case 'varchar':
							$this->initVar($key, XOBJ_DTYPE_TXTBOX, null, false, $field['size']);
							break;
						case 'mediumtext':
							$this->initVar($key, XOBJ_DTYPE_TXTBOX, null, false, $field['size']);
							break;
						case 'text':
							$this->initVar($key, XOBJ_DTYPE_OTHER, null, false, $field['size']);
							break;
						case 'date':
						case 'time':
						case 'datetime':
						case 'year':
							$this->initVar($key, XOBJ_DTYPE_TXTBOX, null, false, $field['size']);
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
			$ret['test'] = $test->toArray();
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

    function setTable($table) 
    {
         if (is_object($table)) {
         	parent::__construct($GLOBALS['xoopsDB'], $table->getVar('name'), 'BenchmarkTester', "id", "tid");
        	$this->_table = $table;
        	$this->_fields = $table->getVar('fids');
        	if ($table->getVar('created')>0)
        		$this->_table_exists = true;
        }
    }
    
    function &create($isNew = true)
    {
    	$GLOBAL['timer']['create']['start'] = microtime(true);
        $obj = new BenchmarkTester($this->_fields, $this->_table);
        if ($isNew === true) {
            $obj->setNew();
        }
        $GLOBAL['timer']['create']['end'] = microtime(true);
        return $obj;
    }
    
    function getObjects($criteria, $id_as_key=true, $as_object=true) {
		$GLOBAL['timer']['getobjects']['start'] = microtime(true);
    	$select = "*";
        $limit = null;
        $start = null;
        $sql = "SELECT {$select} FROM `{$this->handler->table}`";
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
            // $sql .= " ORDER BY `{$this->handler->keyName}` DESC";
        }
        $result = $this->handler->db->query($sql, $limit, $start);
        $ret = array();
        if ($asObject) {
            while ($myrow = $this->handler->db->fetchArray($result)) {
                $object =& $this->create(false);
                $object->assignVars($myrow);
                if ($id_as_key) {
                    $ret[$myrow[$this->handler->keyName]] = $object;
                } else {
                    $ret[] = $object;
                }
                unset($object);
            }
        } else {
            $object =& $this->create(false);
            while ($myrow = $this->handler->db->fetchArray($result)) {
                $object->assignVars($myrow);
                if ($id_as_key) {
                    $ret[$myrow[$this->handler->keyName]] = $object->getValues(array_keys($myrow));
                } else {
                    $ret[] = $object->getValues(array_keys($myrow));
                }
            }
            unset($object);
        }
        $GLOBAL['timer']['getobjects']['end'] = microtime(true);
        return $ret;
    }
    
    function insert(&$object, $force = true)
    {
    	$GLOBAL['timer']['insert']['start'] = microtime(true);
        $ret = parent::insert($object, $force);
    	$GLOBAL['timer']['insert']['end'] = microtime(true);
        return $ret;
    }

    function delete(&$object, $force = false)
    {
    	$GLOBAL['timer']['delete']['start'] = microtime(true);
        $ret = parent::delete($object, $force);
        $GLOBAL['timer']['delete']['end'] = microtime(true);
        return $ret;
    }

    function deleteAll($criteria = null, $force = true, $asObject = false)
    {
    	$GLOBAL['timer']['deleteall']['start'] = microtime(true);
        $ret = parent::deleteAll($criteria, $force, $asObject);
        $GLOBAL['timer']['deleteall']['end'] = microtime(true);
        return $ret;
    }

    function updateAll($fieldname, $fieldvalue, $criteria = null, $force = false)
    {
        $GLOBAL['timer']['updateall']['start'] = microtime(true);
        $ret = parent::updateAll($fieldname, $fieldvalue, $criteria, $force);
        $GLOBAL['timer']['updateall']['end'] = microtime(true);
        return $ret;
    }
}
?>