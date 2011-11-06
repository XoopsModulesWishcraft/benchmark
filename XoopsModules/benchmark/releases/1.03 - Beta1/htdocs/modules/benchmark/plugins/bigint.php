<?php
function BigintYesYesGetHook($object) {
	return $object;
}

function BigintYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function BigintYesNoGetHook($object) {
	return $object;
}

function BigintYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function BigintNoYesGetHook($object) {
	return $object;
}

function BigintNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function BigintNoNoGetHook($object) {
	return $object;
}

function BigintNoNoInsertHook($object) {
	return $object->getVar('fid');
}
?>