## PHP MySQLdump

Backup your database in PHP

Backup all table in database
```php
$BackUp= new \PHPSQLDUMP\SQLDUMP(SQLHOST,SQLLOGIN,SQLPASS,SQLDB);

$BackUp->BackupDB();
```

or specific tables
```php
$BackUp= new \PHPSQLDUMP\SQLDUMP(SQLHOST,SQLLOGIN,SQLPASS,SQLDB);

$BackUp->BackupDB(array('table1','table2'));
```
