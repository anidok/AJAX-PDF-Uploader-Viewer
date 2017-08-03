<?php

  include "../admin/mysql_interface.php";
  include '../admin/vendor/autoload.php';

?>

<?php

  $host = "139.59.36.137";
  $port = "9001";

  class Obj
  {
    public $filename;
    public $title;
    public $author;
    public $pagecount;
    public $blob;
    //public $location;
    //public $thumbloc;
    //public $uldate;
  }

  function getAllData($conn)
  {
    #$dconn = mysqli_connect( "localhost", $user, $pass, $db ) or die("Unable to connect to database.");

    /*if(isset($_GET["term"]))
    {
      $term = $_GET["term"];
      echo $_GET;
    }
    else
    {
      $term = '';
    }*/

    $query = "SELECT filename, title, author, pagecount, thumbnail FROM pdfuploads";

    $db = new Db();

    //$result = mysqli_query( $dconn, $query ) or die("mysqli error: Unable to execute query '" . $query . "'.<br>");
    $result = $db->query( $query );

    if (!$result)
    {
      echo '<p>Unable to execute query</p>';
    }

    //sendJsonData( $conn, $result );
    sendJsonData( $conn, $result );

    //mysqli_close($dconn);
    //fclose($conn);
  }

  //////////////////////////

  function getSingleData($conn, $term)
  {
    //$user = "koushtav";
    //$pass = "qwertypass";
    //$db = "files";

    //$dconn = mysqli_connect( "localhost", $user, $pass, $db ) or die("Unable to connect to database.");

    $query = "SELECT filename, title, author, pagecount, thumbnail FROM pdfuploads WHERE filename LIKE '%$term%.pdf' OR filename LIKE '%$term%.pdf' OR author LIKE '%$term%' OR title LIKE '%$term%'";

    //$result = mysqli_query( $dconn, $query ) or die("mysqli error: Unable to execute query '" . $query . "'.<br>");

    $db = new Db();

    $result = $db->query($query);

    //if(mysqli_num_rows($result) > 0)
    if($result->num_rows > 0)
      sendJsonData( $conn, $result );
    else
    {
      $msg = '{
                "status": "OK",
                "type": "search_results",
                "result_count" : "0",
                "results": []
              }';
      fwrite($conn, $msg);
    }

    #mysqli_close($dconn);
  }

  function getPageData($conn, $file, $pgno)
  {
    $query = "SELECT filename FROM pdfuploads WHERE filename='$file.pdf'";

    //$result = mysqli_query( $dconn, $query ) or die("mysqli error: Unable to execute query '" . $query . "'.<br>");

    $db = new Db();

    $result = $db->query($query);

    //if(mysqli_num_rows($result) > 0)
    if($result->num_rows === 0)
    {
      $msg = '{
                "status": "OK",
                "type": "page_results",
                "pageno": "-1"
              }';
      fwrite($conn, $msg);
    }
    else
    {
      $parser = new \Smalot\PdfParser\Parser();
      $pdf    = $parser->parseFile( '../data/uploads/' . $file . '.pdf' );

      $details  = $pdf->getDetails();

      $pdfpgc = (int)$details['Pages'];
      //print_r($pdfpgc . ',' . $pgno);

      if ($pdfpgc < $pgno)
      {
        $msg = '{
                  "status": "OK",
                  "type": "page_results",
                  "pageno": "-1"
                }';
        fwrite($conn, $msg);
      }
      else
      {
        $msg = '{
                  "status": "OK",
                  "type": "page_results",
                  "pageno": "' . $pgno . '",
                  "blob": "';
        fwrite($conn, $msg);

        $src = '../data/uploads/' . $file . '.pdf';
        $tgt = $file . '.jpg';
        generateImage($src, $tgt, $pgno );

        $imgloc = $tgt;
        $image = fopen($imgloc, 'rb');
        $imageContent = fread($image, filesize($imgloc));
        #$imageContent = $db->quote( $imageContent );

        $msg =  base64_encode( $imageContent );

        //$msg = json_encode( $msg );
        fwrite($conn, $msg);

        $msg = '"
        }';
        fwrite($conn, $msg);
      }
    }
  }

  function generateImage($source, $target, $pageno)
  {
    $img = new imagick();
    $img->readImage($source.'[' . (string)($pageno - 1) .']');
    $img->setImageBackgroundColor('#ffffff');
    //$img->setImageCompression(imagick::COMPRESSION_LOSSLESSJPEG);
    //$img->setImageCompressionQuality(500);
    $img = $img->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
    $img->setImageFormat('jpg');
    $img->resizeImage(1000, 1400, 1, 0);
    $img->writeImage($target);
    $img->clear();
    $img->destroy();
  }

  function sendJsonData($conn, $result)
  {
    $msg = '{
              "status": "OK",
              "type": "search_results",
              "result_count" : "' . (string)mysqli_num_rows( $result ) . '",
              "results": [';
    fwrite($conn, $msg);

    $rows = mysqli_fetch_all($result);
    $size = (string)sizeof($rows);

    for ( $i = 0; $i < sizeof($rows) - 1; $i = $i + 1)
    {
      $row = $rows[$i];

      $o = new Obj();

      $o->filename = $row[0];
      $o->title = $row[1];
      $o->author = $row[2];
      $o->pagecount = $row[3];
      //$o->blob = $row[4];
      $o->blob = base64_encode( $row[4] );

      $msg = json_encode( $o, JSON_UNESCAPED_SLASHES );
      fwrite($conn, $msg);
      fwrite( $conn, ', ' );
    }

    $row = $rows[$i];

    $o = new Obj();

    $o->filename = $row[0];
    $o->title = $row[1];
    $o->author = $row[2];
    $o->pagecount = $row[3];
    $o->blob = base64_encode( $row[4] );

    #$msg = json_encode( $o );
    $msg = json_encode( $o, JSON_UNESCAPED_SLASHES );
    fwrite($conn, $msg);

    $msg = ']
    }';
    fwrite($conn, $msg);
  }

  $socket = stream_socket_server("tcp://$host:$port", $errno, $errstr);

  if (!$socket)
  {
    echo "$errstr ($errno)<br />\n";
  }
  else
  {
    echo 'Bound socket on port:' . $port . ', listening for incoming connections...';
    while (true)
    {
      $conn = stream_socket_accept($socket);

      if ($conn)
      {
        $data = fread($conn, 128);

        if(!$data)
        {
            fclose($conn);
            echo "A client disconnected\n";
            continue;
        }

        $fields = explode(":", $data);

        if ($fields[0] === "search_query" && $fields[1] === "all")
        {
          getAllData($conn);
        }
        else if ($fields[0] === "search_query" && !empty($fields[1]))
        {
          getSingleData($conn, $fields[1]); // ignore the ".pdf" part
        }
        else if ($fields[0] === "page_query" && !empty($fields[1]) && !empty($fields[2]))
        {
          getPageData($conn, $fields[1], $fields[2]);
        }

        fclose($conn);

      }
    }
    echo 'Closing socket';
    fclose($socket);
  }
?>
