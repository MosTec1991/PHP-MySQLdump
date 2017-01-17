<?php

require 'mysqldump.class.php';

/*
define("SQLDB","MyDATABASE");
define("SQLLOGIN","MyUSER");
define("SQLPASS","MyPASSWORD");
define("SQLHOST","MyHOST");
 */

//Or Load SQL config File
require 'config.php';


$BackUp= new \PHPSQLDUMP\SQLDUMP(SQLHOST,SQLLOGIN,SQLPASS,SQLDB);

$BackUp->BackupDB();