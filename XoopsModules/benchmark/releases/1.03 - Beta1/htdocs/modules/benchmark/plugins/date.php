<?php

function DateYesYesGetHook($object) {
	return $object;
}

function DateYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function DateYesNoGetHook($object) {
	return $object;
}

function DateYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function DateNoYesGetHook($object) {
	return $object;
}

function DateNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function DateNoNoGetHook($object) {
	return $object;
}

function DateNoNoInsertHook($object) {
	return $object->getVar('fid');
}

?>