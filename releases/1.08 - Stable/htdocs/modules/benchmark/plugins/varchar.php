<?php

function VarcharYesYesGetHook($object) {
	return $object;
}

function VarcharYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function VarcharYesNoGetHook($object) {
	return $object;
}

function VarcharYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function VarcharNoYesGetHook($object) {
	return $object;
}

function VarcharNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function VarcharNoNoGetHook($object) {
	return $object;
}

function VarcharNoNoInsertHook($object) {
	return $object->getVar('fid');
}

?>