<?php

include_once('chenliujin/mysql/Model.class.php');
include_once('chenliujin/mysql/Page.php');

require('/data/z/conf/configure.php');

if (empty(Model::$dbo)) {
	Model::$host 	= DB_SERVER;
	Model::$dbName 	= DB_DATABASE;
	Model::$dbUser 	= DB_SERVER_USERNAME;
	Model::$dbPwd   = DB_SERVER_PASSWORD;

	Model::$dbo = new Model;
}

