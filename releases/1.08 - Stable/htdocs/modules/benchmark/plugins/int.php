<?php

function IntYesYesGetHook($object) {
	return $object;
}

function IntYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function IntYesNoGetHook($object) {
	return $object;
}

function IntYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function IntNoYesGetHook($object) {
	return $object;
}

function IntNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function IntNoNoGetHook($object) {
	return $object;
}

function IntNoNoInsertHook($object) {
	return $object->getVar('fid');
}

?>