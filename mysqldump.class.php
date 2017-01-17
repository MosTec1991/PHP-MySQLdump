<?php

namespace PHPSQLDUMP;

function prin()
{

}


class SQLDUMP
{
    protected $SQL_DB;
    protected $SQL_HOST;
    protected $SQL_PASSWORD;
    protected $SQL_USER;

    protected $BackupFile;


    function __construct($SQL_HOST,$SQL_USER,$SQL_PASSWORD,$SQL_DB)
    {
        $this->SQL_HOST=$SQL_HOST;
        $this->SQL_USER=$SQL_USER;
        $this->SQL_PASSWORD=$SQL_PASSWORD;
        $this->SQL_DB=$SQL_DB;
    }


    public function BackupDB()
    {
        $this->BackupFile = tmpfile();



        
        //Change This line for file name
        $FileName=$this->SQL_DB.'@'.$this->SQL_HOST.' '.date('Y-m-d').'.sql';
        //Download File
        $this->Download($FileName);
    }

    /**
     *
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
}