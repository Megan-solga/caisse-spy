<?php

class Database
{
    private static $dbHost = 'localhost'; //static = qui appartient à la classe, c'est pour utiliser la classe elle même et pas une instance. Private = pas accessible de l'extérieur de la classe
    private static $dbName = 'hommes_spy';
    private static $dbUser = 'root';
    private static $dbUserPassword = '';

    private static $connection = null;

    public static function connect()
    { // public = accecssible de l'extérieur de la classe
        try {
            self::$connection = new PDO("mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName, self::$dbUser, self::$dbUserPassword);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        return self::$connection;
    }

    public static function disconnect()
    {
        self::$connection = null;
    }
}

// Database::connect(); // On appelle la méthode connect pour établir la connexion à la base de données
