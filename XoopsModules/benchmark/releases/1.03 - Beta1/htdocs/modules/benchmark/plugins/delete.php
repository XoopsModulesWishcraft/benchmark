<?php

function DeleteInnodbYesTableGetHook($object) {
	return $object;
}

function DeleteInnodbYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function DeleteInnodbNoTableGetHook($object) {
	return $object;
}

function DeleteInnodbNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function DeleteMyisamYesTableGetHook($object) {
	return $object;
}

function DeleteMyisamYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function DeleteMyisamNoTableGetHook($object) {
	return $object;
}

function DeleteMyisamNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function DeleteInnodbResultGetHook($object) {
	return $object;
}

function DeleteInnodbResultInsertHook($object) {
	return $object->getVar('rid');
}

function DeleteMyisamResultGetHook($object) {
	return $object;
}

function DeleteMyisamResultInsertHook($object) {
	return $object->getVar('rid');
}
?>