<?php

function SmartyInnodbYesTableGetHook($object) {
	return $object;
}

function SmartyInnodbYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function SmartyInnodbNoTableGetHook($object) {
	return $object;
}

function SmartyInnodbNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function SmartyMyisamYesTableGetHook($object) {
	return $object;
}

function SmartyMyisamYesTableInsertHook($object) {
	return $object->getVar('tbid');
}

function SmartyMyisamNoTableGetHook($object) {
	return $object;
}

function SmartyMyisamNoTableInsertHook($object) {
	return $object->getVar('tbid');
}

function SmartyInnodbResultGetHook($object) {
	return $object;
}

function SmartyInnodbResultInsertHook($object) {
	return $object->getVar('rid');
}

function SmartyMyisamResultGetHook($object) {
	return $object;
}

function SmartyMyisamResultInsertHook($object) {
	return $object->getVar('rid');
}
?>