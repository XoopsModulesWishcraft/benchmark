<?php

function UpdateallInnodbYesTableGetHook($object) {
	return $object;
}

function UpdateallInnodbYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function UpdateallInnodbNoTableGetHook($object) {
	return $object;
}

function UpdateallInnodbNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function UpdateallMyisamYesTableGetHook($object) {
	return $object;
}

function UpdateallMyisamYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function UpdateallMyisamNoTableGetHook($object) {
	return $object;
}

function UpdateallMyisamNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function UpdateallInnodbResultGetHook($object) {
	return $object;
}

function UpdateallInnodbResultInsertHook($object) {
	return $object->getVar('rid');
}

function UpdateallMyisamResultGetHook($object) {
	return $object;
}

function UpdateallMyisamResultInsertHook($object) {
	return $object->getVar('rid');
}
?>