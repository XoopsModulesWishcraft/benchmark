<?php

function DecimalYesYesGetHook($object) {
	return $object;
}

function DecimalYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function DecimalYesNoGetHook($object) {
	return $object;
}

function DecimalYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function DecimalNoYesGetHook($object) {
	return $object;
}

function DecimalNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function DecimalNoNoGetHook($object) {
	return $object;
}

function DecimalNoNoInsertHook($object) {
	return $object->getVar('fid');
}

?>