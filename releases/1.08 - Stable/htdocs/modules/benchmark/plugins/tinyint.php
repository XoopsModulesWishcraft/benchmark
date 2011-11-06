<?php

function TinyintYesYesGetHook($object) {
	return $object;
}

function TinyintYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function TinyintYesNoGetHook($object) {
	return $object;
}

function TinyintYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function TinyintNoYesGetHook($object) {
	return $object;
}

function TinyintNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function TinyintNoNoGetHook($object) {
	return $object;
}

function TinyintNoNoInsertHook($object) {
	return $object->getVar('fid');
}

?>