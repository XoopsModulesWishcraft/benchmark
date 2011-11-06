<?php

function WaitFinishedTestsGetHook($object) {
	return $object;
}

function WaitFinishedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function CreateFinishedTestsGetHook($object) {
	return $object;
}

function CreateFinishedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function SelectFinishedTestsGetHook($object) {
	return $object;
}

function SelectFinishedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function InsertFinishedTestsGetHook($object) {
	return $object;
}

function InsertFinishedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function UpdateFinishedTestsGetHook($object) {
	return $object;
}

function UpdateFinishedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function UpdateallFinishedTestsGetHook($object) {
	return $object;
}

function UpdateallFinishedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function DeleteFinishedTestsGetHook($object) {
	return $object;
}

function DeleteFinishedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function DeleteallFinishedTestsGetHook($object) {
	return $object;
}

function DeleteallFinishedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function AlterFinishedTestsGetHook($object) {
	return $object;
}

function AlterFinishedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function RenameFinishedTestsGetHook($object) {
	return $object;
}

function RenameFinishedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function SmartyFinishedTestsGetHook($object) {
	return $object;
}

function SmartyFinishedTestsInsertHook($object) {
	return $object->getVar('tid');
}

function FinishedFinishedTestsGetHook($object) {
	return $object;
}

function FinishedFinishedTestsInsertHook($object) {
	return $object->getVar('tid');
}

?>