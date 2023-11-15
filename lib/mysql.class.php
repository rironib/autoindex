<?php

/**
* Simple Mysqli class
* @author ionutvmi@gmail.com
*
* edit 9-May-2013 - added mysql compatibility
*/
if(extension_loaded('mysqli')) {
    class dbConn {
        var $link = null;

        function __construct($db_host,$db_user,$db_pass,$db_name){
            
            $this->link = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
            
            if (!$this->link) die('Connect Error (' . mysqli_connect_errno() . ') '.mysqli_connect_error());
            
            mysqli_select_db($this->link, $db_name) or die(mysqli_error($this->link));
            
            return true;
        }
        
        function select($q){
        
            $result = mysqli_query($this->link,$q);

            if(mysqli_num_rows($result) > 0)

                while($res = mysqli_fetch_object($result))
                
                    $arr[] = $res;
                
            if($arr) return $arr;
            
            return false;
        }
        function get_row($q){
            $result = mysqli_query($this->link,$q);

            if(mysqli_num_rows($result) == 1)

            $arr = mysqli_fetch_object($result);
                            
            if($arr) return $arr;
            
            return false;
        }
        function count($q){
            $result = mysqli_query($this->link,$q);

            return mysqli_num_rows($result);

        }

        function query($q){

            return mysqli_query($this->link,$q);

        }

        function escape($str){

            return mysqli_real_escape_string($this->link,$str);

        }
        function insert($q){

            if(mysqli_query($this->link,$q))
                return mysqli_insert_id($this->link); 
            return false;
        }
        function insert_array($table,$array){
            $q = "INSERT INTO `$table`";
            $q .=" (`".implode("`,`",array_keys($array))."`) ";
            $q .=" VALUES ('".implode("','",array_values($array))."') ";

            if(mysqli_query($this->link,$q))
                return mysqli_insert_id($this->link);
            return false;
        }
    }
} else { // we use the old mysql
    class dbConn {
        var $link = null;

        function __construct($db_host,$db_user,$db_pass,$db_name){
            
            $this->link = @mysql_connect($db_host, $db_user, $db_pass);
            
            if (!$this->link) die('Connect Error (' . mysql_errno() . ') '.mysql_error());
            
            mysql_select_db($db_name, $this->link) or die(mysql_error($this->link));
            
            return true;
        }
        
        function select($q){
        
            $result = mysql_query($q, $this->link);

            if(mysql_num_rows($result) > 0)

                while($res = mysql_fetch_object($result))
                
                    $arr[] = $res;
                
            if($arr) return $arr;
            
            return false;
        }
        function get_row($q){
            $result = mysql_query($q, $this->link);

            if(mysql_num_rows($result) == 1)

            $arr = mysql_fetch_object($result);
                            
            if($arr) return $arr;
            
            return false;
        }
        function count($q){
            $result = mysql_query($q, $this->link);

            return mysql_num_rows($result);

        }

        function query($q){

            return mysql_query($q, $this->link);

        }

        function escape($str){

            return mysql_real_escape_string($str, $this->link);

        }
        function insert($q){

            if(mysql_query($q, $this->link))
                return mysql_insert_id($this->link);
            return false;
        }
        function insert_array($table,$array){
            $q = "INSERT INTO `$table`";
            $q .=" (`".implode("`,`",array_keys($array))."`) ";
            $q .=" VALUES ('".implode("','",array_values($array))."') ";

            if(mysql_query($q, $this->link))
                return mysql_insert_id($this->link);
            return false;
        }
    }


}