<?php

function MediumtextYesYesGetHook($object) {
	return $object;
}

function MediumtextYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function MediumtextYesNoGetHook($object) {
	return $object;
}

function MediumtextYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function MediumtextNoYesGetHook($object) {
	return $object;
}

function MediumtextNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function MediumtextNoNoGetHook($object) {
	return $object;
}

function MediumtextNoNoInsertHook($object) {
	return $object->getVar('fid');
}

?>