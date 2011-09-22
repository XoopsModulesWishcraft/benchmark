<?php
function AlterInnodbYesTableGetHook($object) {
	return $object;
}

function AlterInnodbYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function AlterInnodbNoTableGetHook($object) {
	return $object;
}

function AlterInnodbNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function AlterMyisamYesTableGetHook($object) {
	return $object;
}

function AlterMyisamYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function AlterMyisamNoTableGetHook($object) {
	return $object;
}

function AlterMyisamNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function AlterInnodbResultGetHook($object) {
	return $object;
}

function AlterInnodbResultInsertHook($object) {
	return $object->getVar('rid');
}

function AlterMyisamResultGetHook($object) {
	return $object;
}

function AlterMyisamResultInsertHook($object) {
	return $object->getVar('rid');
}
?>