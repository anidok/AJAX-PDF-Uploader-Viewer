<?php session_start(); ?>

<?php

  error_reporting(E_ALL);
  ini_set("display_errors","On");

  include "mysql_interface.php";

  if ( isset( $_SESSION['logged'] ) )
  {
    header('Location: panel.php');
  }

  if ($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    if ( !empty( $_POST['username'] ) &&
          !empty( $_POST['password'] ) )
    {
      /*$config = parse_ini_file('../../ws_config.ini');

      //$user = htmlspecialchars( stripslashes( trim( $_POST['username'] ) ) );
      //$pass = htmlspecialchars( stripslashes( trim( $_POST['password'] ) ) );
      //$db = 'users';

      $host = 'localhost';
      $user = $config['username'];
      $pass = $config['password'];
      $db = $config['dbname'];

      echo 'asdasdsa';

      //$db= new Database();

      $conn = mysqli_connect( $host, $user, $pass, $db );

      if ( !$conn )
      {
        header('Location: error.php');
        exit;
      }
      else
      {
        mysqli_close($conn);
        $_SESSION['logged'] = $user;
        header('Location: panel.php');

        exit;
      }*/

      // !!!!!

      $user = htmlspecialchars( stripslashes( trim( $_POST['username'] ) ) );
      $pass = htmlspecialchars( stripslashes( trim( $_POST['password'] ) ) );

      //$db = new Database();

      $db = new Db();

      $sql = "SELECT USERNAME, PASSWORD FROM admin WHERE USERNAME = '" . $user .
             "' AND PASSWORD = '" . $pass ."'";

      $rows = $db -> select( $sql );

      if (sizeof($rows) === 1)
      {
        $_SESSION['logged'] = $user;
        header('Location: panel.php');
        exit;
      }
      else
      {
        header('Location: error.php');
        exit;
      }

      /*$conn = $db->get();

      $result =  $conn->query($sql);
      print_r($result);*/

      /*$row = $db.execSelectQuery($sql)

      if ( !$row || sizeof( $row ) !== 1 )
      {
        header('Location: error.php');
        exit;
      }*/

      //$_SESSION['logged'] = $user;
      //header('Location: panel.php');
      //exit;
    }
    else
    {
      header('Location: index.php');
      exit;
    }
  }
  header('Location: index.php');
  exit;
?>
