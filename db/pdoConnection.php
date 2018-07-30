<?php
namespace SlimTest\DM;

/**
 * Connection to PDO data source
 */
class PDO_Connection {
    static private $dbc=false;
    static private $settings;

    static public function setSettings($settings)
    {
        self::$settings = $settings;
    }

    static public function getConnection() 
    {
        if (self::$dbc === false) {
            try {
                $connString = self::$settings['provider'].':host='.self::$settings['host'].';dbname='.self::$settings['dbname'];
                self::$dbc = new \PDO($connString, self::$settings['user'], self::$settings['password']);
                self::$dbc->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$dbc->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            } catch (\PDOException $e) {
                print "PDO connection error: " . $e->getMessage() . "\n";
            }
        }

        return self::$dbc;
    }
}
