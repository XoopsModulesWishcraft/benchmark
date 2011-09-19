<?php


function DoubleYesYesGetHook($object) {
	return $object;
}

function DoubleYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function DoubleYesNoGetHook($object) {
	return $object;
}

function DoubleYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function DoubleNoYesGetHook($object) {
	return $object;
}

function DoubleNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function DoubleNoNoGetHook($object) {
	return $object;
}

function DoubleNoNoInsertHook($object) {
	return $object->getVar('fid');
}

?>