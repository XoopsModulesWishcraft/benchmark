<?php

function RenameInnodbYesTableGetHook($object) {
	return $object;
}

function RenameInnodbYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function RenameInnodbNoTableGetHook($object) {
	return $object;
}

function RenameInnodbNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function RenameMyisamYesTableGetHook($object) {
	return $object;
}

function RenameMyisamYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function RenameMyisamNoTableGetHook($object) {
	return $object;
}

function RenameMyisamNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function RenameInnodbResultGetHook($object) {
	return $object;
}

function RenameInnodbResultInsertHook($object) {
	return $object->getVar('rid');
}

function RenameMyisamResultGetHook($object) {
	return $object;
}

function RenameMyisamResultInsertHook($object) {
	return $object->getVar('rid');
}
?>