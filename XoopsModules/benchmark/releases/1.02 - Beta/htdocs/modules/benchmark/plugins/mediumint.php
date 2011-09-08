<?php
function MediumintYesYesGetHook($object) {
	return $object;
}

function MediumintYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function MediumintYesNoGetHook($object) {
	return $object;
}

function MediumintYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function MediumintNoYesGetHook($object) {
	return $object;
}

function MediumintNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function MediumintNoNoGetHook($object) {
	return $object;
}

function MediumintNoNoInsertHook($object) {
	return $object->getVar('fid');
}
?>