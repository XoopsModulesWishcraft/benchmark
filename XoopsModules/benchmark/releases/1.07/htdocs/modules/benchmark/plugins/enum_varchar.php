<?php

function Enum_varcharYesYesGetHook($object) {
	return $object;
}

function Enum_varcharYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function Enum_varcharYesNoGetHook($object) {
	return $object;
}

function Enum_varcharYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function Enum_varcharNoYesGetHook($object) {
	return $object;
}

function Enum_varcharNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function Enum_varcharNoNoGetHook($object) {
	return $object;
}

function Enum_varcharNoNoInsertHook($object) {
	return $object->getVar('fid');
}

?>