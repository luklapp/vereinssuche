<?php

require_once(__DIR__."/../includes/config/config.php");

/**
 * Datenbank-Klasse
 * @author    Lukas Klappert
 * @copyright 2014
 */

class Database
{

  private static $db;

  public static function getDB()
  {
    if (!self::$db) {
      self::$db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";port=".DB_PORT, DB_USER, DB_PASS, array(
          PDO::ATTR_PERSISTENT               => true,
          PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE       => PDO::FETCH_OBJ,
          PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
          PDO::MYSQL_ATTR_INIT_COMMAND       => "SET NAMES 'utf8'"
        )
      );
    }

    return self::$db;
  }
}
