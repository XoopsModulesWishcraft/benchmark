<?php
function WaitPreparedTestsGetHook($object) {
	return $object;
}

function WaitPreparedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function CreatePreparedTestsGetHook($object) {
	return $object;
}

function CreatePreparedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function SelectPreparedTestsGetHook($object) {
	return $object;
}

function SelectPreparedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function InsertPreparedTestsGetHook($object) {
	return $object;
}

function InsertPreparedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function UpdatePreparedTestsGetHook($object) {
	return $object;
}

function UpdatePreparedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function UpdateallPreparedTestsGetHook($object) {
	return $object;
}

function UpdateallPreparedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function DeletePreparedTestsGetHook($object) {
	return $object;
}

function DeletePreparedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function DeleteallPreparedTestsGetHook($object) {
	return $object;
}

function DeleteallPreparedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function AlterPreparedTestsGetHook($object) {
	return $object;
}

function AlterPreparedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function RenamePreparedTestsGetHook($object) {
	return $object;
}

function RenamePreparedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function SmartyPreparedTestsGetHook($object) {
	return $object;
}

function SmartyPreparedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function FinishedPreparedTestsGetHook($object) {
	return $object;
}

function FinishedPreparedTestsInsertHook($object) {
	return $object->getVar('tid');
}

?>