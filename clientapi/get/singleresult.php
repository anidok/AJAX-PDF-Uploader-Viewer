
<?php
$fp = stream_socket_client("tcp://139.59.36.137:9001", $errno, $errstr, 30);
#echo "goodbye, world<br>";
//if(isset($_GET["term"]))
  //echo $_GET["term"];
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    //fwrite($fp, "GET / HTTP/1.0\r\nHost: localhost\r\nAccept: */*\r\n\r\n");

    $query_type = "search_query";
    $query_params = $_GET["term"];

    $msg = $query_type . ":" . $query_params;

    fwrite( $fp, $msg );

    while (!feof($fp)) {
        echo fgets($fp, 1024);
    }
    fclose($fp);
}
?>
