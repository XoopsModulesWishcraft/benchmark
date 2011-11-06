<?php

function Enum_intYesYesGetHook($object) {
	return $object;
}

function Enum_intYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function Enum_intYesNoGetHook($object) {
	return $object;
}

function Enum_intYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function Enum_intNoYesGetHook($object) {
	return $object;
}

function Enum_intNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function Enum_intNoNoGetHook($object) {
	return $object;
}

function Enum_intNoNoInsertHook($object) {
	return $object->getVar('fid');
}

?>