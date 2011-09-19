<?php

function CreateInnodbYesTableGetHook($object) {
	return $object;
}

function CreateInnodbYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function CreateInnodbNoTableGetHook($object) {
	return $object;
}

function CreateInnodbNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function CreateMyisamYesTableGetHook($object) {
	return $object;
}

function CreateMyisamYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function CreateMyisamNoTableGetHook($object) {
	return $object;
}

function CreateMyisamNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function CreateInnodbResultGetHook($object) {
	return $object;
}

function CreateInnodbResultInsertHook($object) {
	return $object->getVar('rid');
}

function CreateMyisamResultGetHook($object) {
	return $object;
}

function CreateMyisamResultInsertHook($object) {
	return $object->getVar('rid');
}
?>