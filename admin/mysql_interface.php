<?php

  error_reporting(E_ALL);
  ini_set("display_errors","On");
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  /* Class to interface with the MySQL database on the server.
     Provides an abstract OO way of connecting to and querying the database.
   */
  class Database
  {
    private $configFile = "../../../ws_config.ini";

    // We connect to the database just once per interaction
    private static $connection = none;

    // Initialize the connection to the database
    public function __construct()
    {
      if ( !isset(  self::$connection ) )
      {
        $config = parse_ini_file( $this->configFile );

        $host = $config['hostname'];
        $user = $config['username'];
        $pass = $config['password'];
        $db   = $config['dbname'];

        self::$connection = new mysqli( $host, $user, $pass, $db );

        if ( !self::$connection )
        {
          header('Location: error.php');
          exit;
        }
      }
    }

    // Get the database instance
    public function get()
    {
      return self::$connection;
    }

    // Escape and quote the user supplied variables on a query
    public function quote($value)
    {
      return "'" . self::$connection->real_escape_string($value) . "''";
    }

    // Perform an insert query on the database
    public function execInsertQuery($sql)
    {
      return $result = self::$connection->query($sql);
    }

    // Perform a select query on the database
    public function execSelectQuery($sql)
    {
      /*$result = self::$connection->query($sql);

      if ( !$result || $result->num_rows() <= 0)
      {
        return false;
      }

      // Return all rows from the select query as an associative array
      return $result->fetch_all( MYSQLI_ASSOC );*/

      $rows = array();
      $result = self::$connection->query($query);
      if($result === false) {
         return false;
      }
      while ($row = $result->fetch_assoc()) {
         $rows[] = $row;
      }
      return $rows;
    }

    function __destruct()
    {
      self::$connection->close();
    }

  }

  class Db {
    protected static $connection;

    public function connect()
    {
        if(!isset(self::$connection))
        {
            $config = parse_ini_file('../../../ws_config.ini');
            self::$connection = new mysqli('localhost',$config['username'],$config['password'],$config['dbname']);
        }

        if(self::$connection === false)
        {
            return false;
        }
        return self::$connection;
    }

    public function query($query)
    {
        $connection = $this -> connect();

        $result = $connection -> query($query);

        return $result;
    }

    /*public function insertquery($query)
    {
        $connection = $this -> connect();

        $result = $connection -> query($query);

        return $result;
    }*/

    public function select($query)
    {
        $rows = array();
        $result = $this -> query($query);
        if($result === false)
        {
            return false;
        }
        while ($row = $result -> fetch_assoc())
        {
            $rows[] = $row;
        }
        return $rows;
    }

    public function error()
    {
        $connection = $this -> connect();
        return $connection -> error;
    }

    public function quote($value)
    {
        $connection = $this -> connect();
        return "'" . $connection -> real_escape_string($value) . "'";
    }
}

?>
