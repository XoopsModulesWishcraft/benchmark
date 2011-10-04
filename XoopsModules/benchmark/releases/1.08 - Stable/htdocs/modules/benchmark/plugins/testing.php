<?php
function WaitTestingTestsGetHook($object) {
	return $object;
}

function WaitTestingTestsInsertHook($object) {
	return $object->getVar('tid');
}

function CreateTestingTestsGetHook($object) {
	return $object;
}

function CreateTestingTestsInsertHook($object) {
	return $object->getVar('tid');
}

function SelectTestingTestsGetHook($object) {
	return $object;
}

function SelectTestingTestsInsertHook($object) {
	return $object->getVar('tid');
}

function InsertTestingTestsGetHook($object) {
	return $object;
}

function InsertTestingTestsInsertHook($object) {
	return $object->getVar('tid');
}

function UpdateTestingTestsGetHook($object) {
	return $object;
}

function UpdateTestingTestsInsertHook($object) {
	return $object->getVar('tid');
}

function UpdateallTestingTestsGetHook($object) {
	return $object;
}

function UpdateallTestingTestsInsertHook($object) {
	return $object->getVar('tid');
}

function DeleteTestingTestsGetHook($object) {
	return $object;
}

function DeleteTestingTestsInsertHook($object) {
	return $object->getVar('tid');
}

function DeleteallTestingTestsGetHook($object) {
	return $object;
}

function DeleteallTestingTestsInsertHook($object) {
	return $object->getVar('tid');
}

function AlterTestingTestsGetHook($object) {
	return $object;
}

function AlterTestingTestsInsertHook($object) {
	return $object->getVar('tid');
}

function RenameTestingTestsGetHook($object) {
	return $object;
}

function RenameTestingTestsInsertHook($object) {
	return $object->getVar('tid');
}

function SmartyTestingTestsGetHook($object) {
	return $object;
}

function SmartyTestingTestsInsertHook($object) {
	return $object->getVar('tid');
}

function FinishedTestingTestsGetHook($object) {
	return $object;
}

function FinishedTestingTestsInsertHook($object) {
	return $object->getVar('tid');
}

?>