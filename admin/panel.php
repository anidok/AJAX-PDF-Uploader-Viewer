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
      <p class="title">Admin Panel</p>
      <p>Welcome Admin! Manipulate PDF files:</p>
      <a href="logout.php"><button>Logout</button></a>
      <br><br><br>
    </div>
    <br>
    <div align="center">
      <form action="upload.php" method="post" id="ulform" onsubmit="return Validate(this);" enctype="multipart/form-data">
        <p>Upload PDF File:</p>
        <p><input type="file" accept="application/pdf" name="fileul" placeholder="Select PDF File" id="pdffilefield"></input></p>
        <button class="button-action" name="ulbtn" id="ulbtn" onclick="return isnotempty(this)">Upload File</button>
        <p id="status"></p>
        <br><br>
        <script>
          /* Code used from: https://stackoverflow.com/a/4237161/7701566 */
          function Validate(oForm)
          {
            var _validFileExtensions = [".pdf"];
            var arrInputs = oForm.getElementsByTagName("input");

            for (var i = 0; i < arrInputs.length; i++)
            {
                var oInput = arrInputs[i];

                if (oInput.type == "file")
                {
                    var sFileName = oInput.value;

                    if (sFileName.length > 0)
                    {
                        var blnValid = false;
                        for (var j = 0; j < _validFileExtensions.length; j++)
                        {
                            var sCurExtension = _validFileExtensions[j];
                            if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase())
                            {
                                blnValid = true;
                                break;
                            }
                        }

                        if (!blnValid)
                        {
                            //alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + _validFileExtensions.join(", "));
                            document.getElementById("status").innerHTML = sFileName + " is not a valid PDF file!";
                            return false;
                        }
                    }
                }
            }

            return true;
          }

          function isnotempty()
          {
            if( document.getElementById("pdffilefield").files.length == 0 )
            {
                //alert("Please select a valid PDF file!");
                document.getElementById("status").innerHTML = "Input cannot be empty, please select a valid PDF file!";
                return false;
            }
            return true;
          }

        </script>
      </form>

      <p>See Uploaded PDF Files:</p>
      <a href="view.php"><p><button class="button-action" name="viewbtn" id="viewbtn" >View...</button></p></a>
      <br><br>
      <!--<p>Delete uploaded files:</p>
      <p><button class="button-action" name="delbtn" id="delbtn" onclick="">Delete ...</button></p>-->

    </div>
    <br><br>
  </body>
</html>
