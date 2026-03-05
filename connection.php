<?php

class Database
{

    public static $connection;

    public static function setUpConnection()
    {

<<<<<<< HEAD
        if (!isset(Database::$connection)) {
            Database::$connection = new mysqli("localhost", "root", "add your password", "flydolk", "3306"); // add your password
=======
        if(!isset(Database::$connection)){
            Database::$connection = new mysqli("localhost","root","**********","flydolk","3306");
>>>>>>> caaaffa25bd8fc408e8276e6a3df20548a32a63f
        }
    }

    public static function iud($q)
    {

        Database::setUpConnection();
        Database::$connection->query($q);
    }

    public static function search($q)
    {

        Database::setUpConnection();
        $resultset = Database::$connection->query($q);
        return $resultset;
    }
}
<<<<<<< HEAD
=======
?>
>>>>>>> caaaffa25bd8fc408e8276e6a3df20548a32a63f
