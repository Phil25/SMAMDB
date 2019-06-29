<?php
class DB{
	static private $conn = null;
	static private $statement;

	private function __construct()
	{
	}

	public static function connect($dbinfo)
	{
		try
		{
			$dsn = 'mysql:host='.$dbinfo['host'].';dbname='.$dbinfo['dbname'].';port='.$dbinfo['port'].';charset=utf8';
			$pdo = new PDO($dsn, $dbinfo['muser'], $dbinfo['mpass'], array(PDO::ATTR_PERSISTENT => true));
			self::$conn = $pdo;
		}
		catch(Exception $e)
		{
			die("Could not connect to database:<br>" . $e->getMessage());
		}
	}

	private static function connectSelf()
	{
		$config = include(__DIR__ . "/../config.php");
		self::connect($config);
	}

	public static function quote($str)
	{
		if(empty(self::$conn)) self::connectSelf();
		return self::$conn->quote($str);
	}

	public static function query($q)
	{
		if(empty(self::$conn)) self::connectSelf();
		self::$statement = self::$conn->prepare($q);
		return self::$statement->execute();
	}

	public static function getRow()
	{
		return self::$statement->fetch(PDO::FETCH_ASSOC);
	}

	public static function getData()
	{
		return self::$statement->fetchAll(PDO::FETCH_ASSOC);
	}
}
