<?php

  include "../admin/mysql_interface.php";

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
    /*$user = "koushtav";
    $pass = "qwertypass";
    $db = "files";*/

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

      $msg = json_encode( $o );
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

    $msg = json_encode( $o );
    fwrite($conn, $msg);

    $msg = ']
    }';
    fwrite($conn, $msg);
  }

  function generateImage($source, $target, $pageno)
  {
    $img = new imagick();
    $img->readImage($source.'['.(string)$pageno.']');
    $img->setImageBackgroundColor('#ffffff');
    $img = $img->mergeImageLayers(Imagick::LAYEREDMETHOD_FLATTEN);
    $img->setImageFormat('jpg');
    $img->resizeImage(800, 1000, 1, 0);
    $img->writeImage($target);
    $img->clear();
    $img->destroy();
  }

  $socket = stream_socket_server("tcp://$host:$port", $errno, $errstr);

  if (!$socket)
  {
    echo "$errstr ($errno)<br />\n";
  }
  else
  {
    print('Bound socket on port' .  $port . ', listening for incoming requests...\n');
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

        fclose($conn);

      }
    }
    echo 'Closing socket';
    fclose($socket);
  }
?>
