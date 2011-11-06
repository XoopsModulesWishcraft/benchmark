<?php

function UpdateInnodbYesTableGetHook($object) {
	return $object;
}

function UpdateInnodbYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function UpdateInnodbNoTableGetHook($object) {
	return $object;
}

function UpdateInnodbNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function UpdateMyisamYesTableGetHook($object) {
	return $object;
}

function UpdateMyisamYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function UpdateMyisamNoTableGetHook($object) {
	return $object;
}

function UpdateMyisamNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function UpdateInnodbResultGetHook($object) {
	return $object;
}

function UpdateInnodbResultInsertHook($object) {
	return $object->getVar('rid');
}

function UpdateMyisamResultGetHook($object) {
	return $object;
}

function UpdateMyisamResultInsertHook($object) {
	return $object->getVar('rid');
}
?>