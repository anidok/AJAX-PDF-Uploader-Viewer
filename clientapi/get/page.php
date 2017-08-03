<?php
  $fp = stream_socket_client("tcp://139.59.36.137:9001", $errno, $errstr, 30);

  if (!$fp)
  {
      echo "$errstr ($errno)<br />\n";
  }
  else
  {
      $query_type = "page_query";
      $msg = $query_type . ":" . $_GET["file"] . ":" . $_GET["pageno"];

      fwrite( $fp, $msg );

      while (!feof($fp)) {
          echo fgets($fp, 1024);
      }
      fclose($fp);
  }
?>
