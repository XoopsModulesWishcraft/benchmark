<?php

function DatetimeYesYesGetHook($object) {
	return $object;
}

function DatetimeYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function DatetimeYesNoGetHook($object) {
	return $object;
}

function DatetimeYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function DatetimeNoYesGetHook($object) {
	return $object;
}

function DatetimeNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function DatetimeNoNoGetHook($object) {
	return $object;
}

function DatetimeNoNoInsertHook($object) {
	return $object->getVar('fid');
}

?>