<?php

function WaitWaitTestsGetHook($object) {
	return $object;
}

function WaitWaitTestsInsertHook($object) {
	return $object->getVar('tid');
}

function CreateWaitTestsGetHook($object) {
	return $object;
}

function CreateWaitTestsInsertHook($object) {
	return $object->getVar('tid');
}

function SelectWaitTestsGetHook($object) {
	return $object;
}

function SelectWaitTestsInsertHook($object) {
	return $object->getVar('tid');
}

function InsertWaitTestsGetHook($object) {
	return $object;
}

function InsertWaitTestsInsertHook($object) {
	return $object->getVar('tid');
}

function UpdateWaitTestsGetHook($object) {
	return $object;
}

function UpdateWaitTestsInsertHook($object) {
	return $object->getVar('tid');
}

function UpdateallWaitTestsGetHook($object) {
	return $object;
}

function UpdateallWaitTestsInsertHook($object) {
	return $object->getVar('tid');
}

function DeleteWaitTestsGetHook($object) {
	return $object;
}

function DeleteWaitTestsInsertHook($object) {
	return $object->getVar('tid');
}

function DeleteallWaitTestsGetHook($object) {
	return $object;
}

function DeleteallWaitTestsInsertHook($object) {
	return $object->getVar('tid');
}

function AlterWaitTestsGetHook($object) {
	return $object;
}

function AlterWaitTestsInsertHook($object) {
	return $object->getVar('tid');
}

function RenameWaitTestsGetHook($object) {
	return $object;
}

function RenameWaitTestsInsertHook($object) {
	return $object->getVar('tid');
}

function SmartyWaitTestsGetHook($object) {
	return $object;
}

function SmartyWaitTestsInsertHook($object) {
	return $object->getVar('tid');
}

function FinishedWaitTestsGetHook($object) {
	return $object;
}

function FinishedWaitTestsInsertHook($object) {
	return $object->getVar('tid');
}

?>