<?php

  print("------------------------\n");
  print("   PDF reader service\n");
  print("------------------------\n");

  $addr = "localhost";
  $port = "8000";

  $client_sock = stream_socket_client( "tcp://$addr:$port", $errno, $errorMessage );

  if ( $client_sock === false )
  {
    throw new UnexpectedValueException("Could not connect to server! Reason: $errorMessage");
  }

  print("Conncted to server...\n");

  fwrite( $client_sock, "GET / HTTP/1.0\r\nHost: localhost\r\nAccept: */*\r\n\r\n" );
  //echo stream_get_contents( $client_sock );
  while (!feof($client_sock))
  {
    echo fgets($client_sock, 1024);
  }
  fclose( $client_sock );

?>
