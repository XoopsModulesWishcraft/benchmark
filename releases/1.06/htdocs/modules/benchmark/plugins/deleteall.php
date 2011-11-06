<?php

function DeleteallInnodbYesTableGetHook($object) {
	return $object;
}

function DeleteallInnodbYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function DeleteallInnodbNoTableGetHook($object) {
	return $object;
}

function DeleteallInnodbNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function DeleteallMyisamYesTableGetHook($object) {
	return $object;
}

function DeleteallMyisamYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function DeleteallMyisamNoTableGetHook($object) {
	return $object;
}

function DeleteallMyisamNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function DeleteallInnodbResultGetHook($object) {
	return $object;
}

function DeleteallInnodbResultInsertHook($object) {
	return $object->getVar('rid');
}

function DeleteallMyisamResultGetHook($object) {
	return $object;
}

function DeleteallMyisamResultInsertHook($object) {
	return $object->getVar('rid');
}
?>