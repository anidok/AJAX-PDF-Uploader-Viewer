<?php

  session_start();

?>

<?php

  if ( !isset( $_SESSION['logged'] ) )
  {
    header('Location: index.php');
  }

  error_reporting(E_ALL);
  ini_set("display_errors","On");

  include "mysql_interface.php";
  include 'vendor/autoload.php';

  if ($_SERVER['REQUEST_METHOD'] == 'GET')
  {
    $id = htmlspecialchars( stripslashes( trim( $_GET['id'] ) ) );

    if ( !empty( $id)  )
    {
      $file_del = true;
    }
    else
    {
      header('Location: panel.php');
      exit;
    }
  }
  else
  {
    header('Location: panel.php');
    exit;
  }

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" >
    <title>Web service test | Admin Control Panel</title>
    <link rel="stylesheet" href="style.css"></link>
    <script
      src="https://code.jquery.com/jquery-3.2.1.min.js"
      integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
      crossorigin="anonymous">
    </script>
  </head>
  <body>
    <div align="center">
      <p class="title">Admin Panel | File Upload</p>
      <a href="logout.php">Logout</a><br>
      <a href="panel.php">Go back toPanel</a>
      <br><br><br>
    </div>
    <br>
    <div align="center">
      <p>PDF File upload status:</p>
      <br>
    </div>
    <br><br>
  </body>
</html>

<?php

  if ($file_del === true)
  {
    $db = new Db();
    $sql = "DELETE FROM pdfuploads WHERE FILENAME='" . $id . "'";
    $res = $db->query($sql);
    if($res)
    {
      echo '<div align="center"><br>Deleted file "'.$id.'"<br><br></div>';
    }
  }
  else
  {
    header('Location: panel.php');
    exit;
  }

?>
