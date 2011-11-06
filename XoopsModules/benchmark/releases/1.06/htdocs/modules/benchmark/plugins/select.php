<?php
function SelectInnodbYesTableGetHook($object) {
	return $object;
}

function SelectInnodbYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function SelectInnodbNoTableGetHook($object) {
	return $object;
}

function SelectInnodbNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function SelectMyisamYesTableGetHook($object) {
	return $object;
}

function SelectMyisamYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function SelectMyisamNoTableGetHook($object) {
	return $object;
}

function SelectMyisamNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function SelectInnodbResultGetHook($object) {
	return $object;
}

function SelectInnodbResultInsertHook($object) {
	return $object->getVar('rid');
}

function SelectMyisamResultGetHook($object) {
	return $object;
}

function SelectMyisamResultInsertHook($object) {
	return $object->getVar('rid');
}
?>