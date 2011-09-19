<?php
function SmallintYesYesGetHook($object) {
	return $object;
}

function SmallintYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function SmallintYesNoGetHook($object) {
	return $object;
}

function SmallintYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function SmallintNoYesGetHook($object) {
	return $object;
}

function SmallintNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function SmallintNoNoGetHook($object) {
	return $object;
}

function SmallintNoNoInsertHook($object) {
	return $object->getVar('fid');
}
?>