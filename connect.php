<?php
        //hide defautlt errors
        error_reporting(0);

        try{
                $db=new PDO("mysql:host=127.0.0.1;dbname=authentic;charset=utf8;","root","");		
        }
        catch(exception $e){
                echo "Unable to connect to the database at this time";
        }
?>