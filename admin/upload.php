<?php

  session_start();

?>

<?php

  if ( !isset( $_SESSION['logged'] ) )
  {
    header('Location: index.php');
  }

  /*if ( !isset( $_SESSION['uploading'] ) )
  {
    header('Location: index.php');
  }*/

  error_reporting(E_ALL);
  ini_set("display_errors","On");

  include "mysql_interface.php";
  include 'vendor/autoload.php';

  if ($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    if ( !empty( $_POST['fileul'] ) || isset( $_FILES['fileul'] ) )
    {
      $file_recvd = true;
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

function genPdfThumbnail($source, $target)
{
  $img = new imagick();
  $img->readImage($source.'[0]');
  $img->setImageBackgroundColor('#ffffff');
  $img = $img->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
  $img->setImageFormat('jpg');
  $img->resizeImage(150, 200, 1, 0);
  $img->writeImage($target);
  $img->clear();
  $img->destroy();
}

  if (isset($file_recvd))
  {
    //echo '<div align="center"><p>File uploaded!</p><br><br></div>';

    //$db = new Db();

    //$sql = "SELECT thumbnail, filename, title, uldate FROM files.pdfuploads";

    $target_dir = "../data/uploads/";
    $target_file = $target_dir . basename( $_FILES['fileul']['name'] );

    if (move_uploaded_file($_FILES['fileul']['tmp_name'], $target_file)) {
        //echo "The file ". basename( $_FILES['fileul']['name']). " has been uploaded.";
    }
    //$pdffile = $_POST['fileul'];

    genPdfThumbnail( $target_file, '../data/thumbs/' . substr( $_FILES['fileul']['name'], 0, -4 ) . '.jpg' );

    $parser = new \Smalot\PdfParser\Parser();
    $pdf    = $parser->parseFile( $target_file );

    // Retrieve all details from the pdf file.
    $details  = $pdf->getDetails();

    // Loop over each property to extract values (string or array).
    /*foreach ($details as $property => $value) {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        echo $property . ' => ' . $value . "<br>";
    }*/
    //print_r($details);

    if(isset($details['Author']))
      $pdfauthor = $details['Author'];
    else
      $pdfauthor = '';

    if(isset($details['Title']))
      $pdftitle = $details['Title'];
    else
      $pdftitle = '';

    $pdfpgc = (int)$details['Pages'];

    $db = new Db();

    //$conn =  $db->connect();

    $imgloc = '../data/thumbs/' . substr( $_FILES['fileul']['name'], 0, -4 ) . '.jpg';
    $image = fopen($imgloc, 'rb');
    $imageContent = fread($image, filesize($imgloc));
    $imageContent = $db->quote( $imageContent );

    //$sql = "INSERT INTO `pdfuploads` (`FILENAME`, `AUTHOR`, `TITLE`, `PAGECOUNT`, `LOCATION`, `THUMBNAIL`, `ULDATE`) VALUES ('" . $_FILES['fileul']['name'] . "', '" . $pdfauthor . "', '" . $pdfpgc . "', 'uploads/" .$_FILES['fileul']['name'] . "', '2017-07-15')";
    $filename = $_FILES['fileul']['name'];
    $fileloc = "../data/uploads/". (string)$_FILES['fileul']['name'];
    $sql = "INSERT INTO `pdfuploads` (`FILENAME`, `AUTHOR`, `TITLE`, `PAGECOUNT`, `LOCATION`, `THUMBNAIL`, `ULDATE`) VALUES ('$filename', '$pdfauthor', '$pdftitle', '$pdfpgc', '$fileloc', $imageContent, '2017-07-15')";

    $res = $db->query($sql);
    if ( $res )
      echo "The file ". basename( $_FILES['fileul']['name']). " has been uploaded.";

    #$sql = "insert into pdfuploads values( '$_FILES['fileul']['name']', '$pdfauthor', '$pdftitle', '$pdfpgc', 'uploads/$_FILES['fileul']['name']', $imageContent )";
    /*$sql = "insert into pdfuploads values( '', '', '', '', '', '', '' )";
    $sql = "INSERT INTO `pdfuploads` (`FILENAME`, `AUTHOR`, `TITLE`, `PAGECOUNT`, `LOCATION`, `THUMBNAIL`, `ULDATE`) VALUES ()";

    $res = $db->query($sql);
    if ( $res )
      echo "The file ". basename( $_FILES['fileul']['name']). " has been uploaded.";*/

    //echo '<img src="data:image/jpeg;base64,'. base64_encode( $value['thumbnail'] ) . '"/>' . '<br><br><br>';

    /*$sql = "INSERT INTO ";

    $fileloc = "../images/bg.jpg";
    $file = fopen($fileloc, 'rb');
    $fileContent = fread($file, filesize($file));

    //$fileContent = mysqli_real_escape_string($conn, $fileContent);

    //$conn = new mysqli($serv, $user, $pass, $db);

    $db = new Db();

    $sql = "INSERT INTO pdfuploads VALUES( $_FILES['fileul']['name'],  );";
    //$conn->query($sql);*/

    /*$rows = $db -> select( $sql );

    if (sizeof($rows) === 0)
    {
      echo '<div align="center"><p>No files to display!</p><br><br></div>';
    }
    else
    {
      //foreach ( $rows as $key => $value )
      //{
        //$title = $value['title'];
        //echo '<p>Filename: <i>' . $value['filename'] . '</i></p><p>Title: <i>' . $title . '</i></p><img src="data:image/jpeg;base64,'. base64_encode( $value['thumbnail'] ) . '"/>' . '<br><br><br>';
      //}
    }*/
  }
  else
  {
    //header('Location: index.php');
    //exit;
  }

?>
