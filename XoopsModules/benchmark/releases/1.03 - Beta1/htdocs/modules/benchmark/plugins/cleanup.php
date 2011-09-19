<?php

function WaitCleanupTestsGetHook($object) {
	return $object;
}

function WaitCleanupTestsInsertHook($object) {
	return $object->getVar('tid');
}

function CreateCleanupTestsGetHook($object) {
	return $object;
}

function CreateCleanupTestsInsertHook($object) {
	return $object->getVar('tid');
}

function SelectCleanupTestsGetHook($object) {
	return $object;
}

function SelectCleanupTestsInsertHook($object) {
	return $object->getVar('tid');
}

function InsertCleanupTestsGetHook($object) {
	return $object;
}

function InsertCleanupTestsInsertHook($object) {
	return $object->getVar('tid');
}

function UpdateCleanupTestsGetHook($object) {
	return $object;
}

function UpdateCleanupTestsInsertHook($object) {
	return $object->getVar('tid');
}

function UpdateallCleanupTestsGetHook($object) {
	return $object;
}

function UpdateallCleanupTestsInsertHook($object) {
	return $object->getVar('tid');
}

function DeleteCleanupTestsGetHook($object) {
	return $object;
}

function DeleteCleanupTestsInsertHook($object) {
	return $object->getVar('tid');
}

function DeleteallCleanupTestsGetHook($object) {
	return $object;
}

function DeleteallCleanupTestsInsertHook($object) {
	return $object->getVar('tid');
}

function AlterCleanupTestsGetHook($object) {
	return $object;
}

function AlterCleanupTestsInsertHook($object) {
	return $object->getVar('tid');
}

function RenameCleanupTestsGetHook($object) {
	return $object;
}

function RenameCleanupTestsInsertHook($object) {
	return $object->getVar('tid');
}

function SmartyCleanupTestsGetHook($object) {
	return $object;
}

function SmartyCleanupTestsInsertHook($object) {
	return $object->getVar('tid');
}

function FinishedCleanupTestsGetHook($object) {
	return $object;
}

function FinishedCleanupTestsInsertHook($object) {
	return $object->getVar('tid');
}

?>