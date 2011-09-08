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
class BenchmarkFields extends XoopsObject
{

    function BenchmarkFields($fid = null)
    {
        $this->initVar('fid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 128);
        $this->initVar('type', XOBJ_DTYPE_ENUM, null, false, false, false, array('bigint','mediumint','smallint','int','tinyint','float','decimal','double','enum_varchar','enum_int','varchar','mediumtext','text','date','time','datetime','year'));
        $this->initVar('required', XOBJ_DTYPE_ENUM, '_NO', false, false, false, array('_YES','_NO'));
        $this->initVar('primarykey', XOBJ_DTYPE_ENUM, '_NO', false, false, false, array('_YES','_NO'));
        $this->initVar('key', XOBJ_DTYPE_TXTBOX, null, false, 35);
        $this->initVar('create_sql', XOBJ_DTYPE_TXTBOX, null, false, 128);
        $this->initVar('alter_sql', XOBJ_DTYPE_TXTBOX, null, false, 128);
        $this->initVar('size', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('recipricol', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('alter_type', XOBJ_DTYPE_ENUM, null, false, false, false, array('bigint','mediumint','smallint','int','tinyint','float','decimal','double','enum_varchar','enum_int','varchar','mediumtext','text','date','time','datetime','year'));		
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
class BenchmarkFieldsHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
        parent::__construct($db, "benchmark_fields", 'BenchmarkFields', "fid", "name");
    }
}
?>