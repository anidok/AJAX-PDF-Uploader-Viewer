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
      <p class="title">Admin Panel | View</p>
      <p>Welcome Admin! Manipulate PDF files:</p>
      <a href="logout.php">Logout</a><br>
      <a href="panel.php">Go back toPanel</a>
      <br><br><br>
    </div>
    <br>
    <div align="center">
      <p>PDF Files on the server:</p>
      <br>
    </div>
    <br><br>
  </body>
</html>

<?php

  $db = new Db();

  $sql = "SELECT thumbnail, filename, title, uldate FROM files.pdfuploads";

  $rows = $db -> select( $sql );

  if (sizeof($rows) === 0)
  {
    echo '<div align="center"><p>No files to display!</p><br><br></div>';
  }
  else
  {
    foreach ( $rows as $key => $value )
    {
      //print_r($key);
      //print_r($value);
      /*if ( $value['title']) )
      {
        $title = '<No title specified>';
      }
      else
      {
        $title = $value['title'];
      }*/
      $title = $value['title'];
      //echo '<p>Filename: <i>' . $value['filename'] . '</i></p><p>Title: <i>' . $title . '</i></p><img src="data:image/jpeg;base64,'. base64_encode( $value['thumbnail'] ) . '"/>';
      //echo '<p><a href="delete.php?id= ' . urlencode($value['filename']) . '"><button>Delete</button></a></p>' . '<br><br><br>';

      /*echo '<div align="center">
        <p>Filename: <i>' . $value['filename'] . '</i></p><p>Title: <i>' . $title . '</i></p><img src="data:image/jpeg;base64,'. base64_encode( $value['thumbnail'] ) . '"/>
        <p><a href="delete.php?id= ' . urlencode($value['filename']) . '"><button>Delete</button></a></p>
        <br><br><br>
      </div>';*/

      echo '<div align="center">
      <p style="color:#086024"><strong style="color:#f2f2f2">Filename:</strong> ' . $value['filename'] . '</p><p style="color:#086024"><strong style="color:#f2f2f2">Title:</strong> ' . $title . '</p><img src="data:image/jpeg;base64,'. base64_encode( $value['thumbnail'] ) . '"/>
      <br>
      <p><a href="delete.php?id= ' . urlencode($value['filename']) . '"><button>Delete</button></a></p>
      </div><br><br>';
    }
  }

?>
