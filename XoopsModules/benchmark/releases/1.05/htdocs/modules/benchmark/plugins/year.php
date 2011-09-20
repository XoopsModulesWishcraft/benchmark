<?php

function YearYesYesGetHook($object) {
	return $object;
}

function YearYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function YearYesNoGetHook($object) {
	return $object;
}

function YearYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function YearNoYesGetHook($object) {
	return $object;
}

function YearNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function YearNoNoGetHook($object) {
	return $object;
}

function YearNoNoInsertHook($object) {
	return $object->getVar('fid');
}

?>