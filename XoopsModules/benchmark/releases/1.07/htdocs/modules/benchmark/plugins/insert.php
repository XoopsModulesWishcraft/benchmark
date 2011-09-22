<?php

function InsertInnodbYesTableGetHook($object) {
	return $object;
}

function InsertInnodbYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function InsertInnodbNoTableGetHook($object) {
	return $object;
}

function InsertInnodbNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function InsertMyisamYesTableGetHook($object) {
	return $object;
}

function InsertMyisamYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function InsertMyisamNoTableGetHook($object) {
	return $object;
}

function InsertMyisamNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function InsertInnodbResultGetHook($object) {
	return $object;
}

function InsertInnodbResultInsertHook($object) {
	return $object->getVar('rid');
}

function InsertMyisamResultGetHook($object) {
	return $object;
}

function InsertMyisamResultInsertHook($object) {
	return $object->getVar('rid');
}
?>