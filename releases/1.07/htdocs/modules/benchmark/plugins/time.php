<?php

function TimeYesYesGetHook($object) {
	return $object;
}

function TimeYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function TimeYesNoGetHook($object) {
	return $object;
}

function TimeYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function TimeNoYesGetHook($object) {
	return $object;
}

function TimeNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function TimeNoNoGetHook($object) {
	return $object;
}

function TimeNoNoInsertHook($object) {
	return $object->getVar('fid');
}

?>