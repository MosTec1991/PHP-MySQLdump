<?php

namespace PHPSQLDUMP;
class SQLDUMP
{
    //SQL HOST
    protected $SQL_DB;
    protected $SQL_HOST;
    protected $SQL_PASSWORD;
    protected $SQL_USER;

    //SQL Backup File
    protected $BackupFile;

    //Script Version
    protected $Version='-- v0.0.0';


    function __construct($SQL_HOST,$SQL_USER,$SQL_PASSWORD,$SQL_DB)
    {
        $this->SQL_HOST=$SQL_HOST;
        $this->SQL_USER=$SQL_USER;
        $this->SQL_PASSWORD=$SQL_PASSWORD;
        $this->SQL_DB=$SQL_DB;
    }


    public function BackupDB($ForceDownload=true)
    {
        $this->BackupFile = tmpfile();

        $this->HeaderComment();
        $this->DatabaseGlobalVar();




        if($ForceDownload)
        {
            //Change This line for file name
            $FileName=$this->SQL_DB.'@'.$this->SQL_HOST.' '.date('Y-m-d').'.sql';
            //Download File
            $this->Download($FileName);
        }
    }

    /**
     * Force User Web Browser To Download File
     *
     * @param $file_name
     */
    protected function Download($file_name)
    {
        // hide notices
        @ini_set('error_reporting', E_ALL & ~ E_NOTICE);

        // turn off compression on the server
        @apache_setenv('no-gzip', 1);
        @ini_set('zlib.output_compression', 'Off');

        //HTML header
        header("Pragma: public");
        header("Expires: -1");
        header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
        header("Content-Disposition: attachment; filename=\"$file_name\"");
        header("Content-Type: text/x-sql;");

        set_time_limit(0);
        fseek($this->BackupFile, 0);

        while(!feof($this->BackupFile))
        {
            print(@fread($this->BackupFile, 1024*8));
            ob_flush();
            flush();
            if (connection_status()!=0)
            {
                @fclose($this->BackupFile);
                exit;
            }
        }

        @fclose($this->BackupFile);
        exit;
    }

    /**
     * Create Header For SQL File
     */
    private function HeaderComment()
    {
        fwrite($this->BackupFile,'-- PHP SQL DUMP'.PHP_EOL);
        fwrite($this->BackupFile,$this->Version.PHP_EOL);
        fwrite($this->BackupFile,'-- https://github.com/MosTec1991/PHP-MySQLdump'.PHP_EOL);
        fwrite($this->BackupFile,'-- '.PHP_EOL);
        fwrite($this->BackupFile,'-- Host: '.$this->SQL_HOST.PHP_EOL);
        fwrite($this->BackupFile,'-- Generation Time: '.date('Y-M-d').' at '.date('H:i:s').PHP_EOL);
        fwrite($this->BackupFile,'-- Server version: '.$this->SQLQuery("SELECT VERSION()")[0]['VERSION()'].PHP_EOL);
        fwrite($this->BackupFile,'-- PHP Version: '.phpversion().PHP_EOL.PHP_EOL);
    }

    /**
     * Generate Database Global Variable
     */
    private function DatabaseGlobalVar()
    {
        fwrite($this->BackupFile,'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";'.PHP_EOL);
        fwrite($this->BackupFile,"SET time_zone = \"+00:00\";".PHP_EOL);
        fwrite($this->BackupFile,PHP_EOL.PHP_EOL);
        fwrite($this->BackupFile,"--".PHP_EOL."-- Database: `".$this->SQL_DB."`".PHP_EOL."--".PHP_EOL.PHP_EOL."-- --------------------------------------------------------".PHP_EOL.PHP_EOL);
    }

    /**
     * Execute SQL Query
     *
     * @param $QueryString
     * @return array Result
     */
    private function SQLQuery($QueryString)
    {
        $LinkID=mysqli_connect($this->SQL_HOST, $this->SQL_USER, $this->SQL_PASSWORD, $this->SQL_DB);
        mysqli_set_charset($LinkID,"utf8");

        for ($i = 1; $i < 6; $i++)
        {
            $QueryID = mysqli_query($LinkID, $QueryString);
            if (!in_array(mysqli_errno($LinkID), array(1213, 1205)))
            {
                break;
            }
            trigger_error("Database deadlock, attempt $i");

            sleep($i * rand(2, 5)); // Wait longer as attempts increase
        }

        $Return = array();

        while ($Row = mysqli_fetch_array($QueryID, MYSQLI_ASSOC))
        {
            $Return[] = $Row;
        }
        mysqli_data_seek($QueryID, 0);
        return $Return;
    }

}
