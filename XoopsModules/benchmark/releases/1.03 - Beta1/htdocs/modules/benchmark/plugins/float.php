<?php

function FloatYesYesGetHook($object) {
	return $object;
}

function FloatYesYesInsertHook($object) {
	return $object->getVar('fid');
}

function FloatYesNoGetHook($object) {
	return $object;
}

function FloatYesNoInsertHook($object) {
	return $object->getVar('fid');
}

function FloatNoYesGetHook($object) {
	return $object;
}

function FloatNoYesInsertHook($object) {
	return $object->getVar('fid');
}

function FloatNoNoGetHook($object) {
	return $object;
}

function FloatNoNoInsertHook($object) {
	return $object->getVar('fid');
}

?>