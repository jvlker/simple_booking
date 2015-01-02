<?php

interface iData{ }

interface iDatabase{
	function createDB();
	function openDB();
	function closeDB();
	function insertData(iData $data);
	function updateData(iData $data);
	function deleteData(iData $data);
	function createTable(iData $data);
	function getData(iData $data, $where, $order="");
	function select($select, $from, $where, $order="");
	function delete($from, $where);
}

interface iDataSubject{
	function save();
	function insert();
	function delete();
	static function getObserver();
	static function getTable($where="", $order="");
	static function getById($id);
}

interface iLog{
	function log($key, $args = array() );
	function output($key, $args = array() );
	function printMessagesAndErrors();
}

interface iSession{
	static function instance();
	function start();
	function destroy();
	function setVar($name, $value);
	function getVar($name);
	function issetVar($name);
	function unsetVar($name);
	function isLogin();
	function login($name, $pwd);
	function logout();
}

interface iDataObserver{
	function updateData(iData $subject);
	function insertData(iData $subject);
	function deleteData(iData $subject);
	
	function getData(iData $data, $where, $order);
}

?>