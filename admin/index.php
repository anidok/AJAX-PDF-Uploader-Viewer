<?php

  session_start();

?>

<?php

  if ( isset( $_SESSION['logged'] ) )
  {
    header('Location: panel.php');
  }

?>

<html lang="en">
  <head>
    <meta charset="utf-8" >
    <title>Web service test | Admin Login</title>
    <link rel="stylesheet" href="style.css"></link>
    <script
      src="https://code.jquery.com/jquery-3.2.1.min.js"
      integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
      crossorigin="anonymous">
    </script>
  </head>
  <body>
    <div align="center">
      <p class="title">Admin Login</p>
      <p>Please enter login credentials below:</p>
    </div>

    <div align="center">
      <form action="login.php" method="post" id="loginform">
        <p><input type="text" name="username" placeholder="Username" id="usernamefield"></input></p>
        <p><input type="password" name="password" placeholder="Password" id="passwordfield"></input></p>
        <button class="button-action" name="login" id="loginbutton" onclick="return func()">Login</button>
        <script>
          function func()
          {
            if (document.getElementById('usernamefield').value === '' ||
                 document.getElementById('passwordfield').value === '' )
            {
              document.getElementById("status").innerHTML = "Cannot leave either login fields blank!";
              return false;
            }
            return true;
          }
        </script>
      </form>
      <p id="status"></p>
    </div>
  </body>
</html>
