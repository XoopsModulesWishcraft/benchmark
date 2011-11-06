<?php
function TextYesYesGetHook($object) {
	return $object;
}

function TextYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function TextYesNoGetHook($object) {
	return $object;
}

function TextYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function TextNoYesGetHook($object) {
	return $object;
}

function TextNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function TextNoNoGetHook($object) {
	return $object;
}

function TextNoNoInsertHook($object) {
	return $object->getVar('fid');
}
?>