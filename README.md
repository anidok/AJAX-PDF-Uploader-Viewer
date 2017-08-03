AJAX PDF Uploader & Viewer
===========================

This is a simple page-by-page PDF uploader and viewer that can be used to upload, view PDF files and delete them from a server. The PDF pages are served to the client as JPEG images.

Implemented using PHP7, MySQL and JavaScript.


## Features


* The software is modular and divided into `client` and `admin` modules.
* `admin` module contains functionality for directly interacting with the server and database that stores the PDF files. An admin may upload and delete files.

* `client` module contains a search functionality for searching PDF files (using AJAX) with matching metadata and viewing them page by page (also using AJAX). The entire PDF isn't loaded, but rather only the pages requested are loaded.

* `client` views the PDF using a REST like service. The service can be used to fetch metadata of matching PDF files (searching) and requesting individual pages (viewing).

## Service

The service is a PHP script that runs on a custom port (8000 is used here). The Apache webserver's reqrite module is used to achieve redirection. Assume the service is running on the host `www.example.com`. The returned by the service is in JSON format.

* Search requests are sent as follows:

`www.example.com/webservice/client/get/term/<search-term>`

E.g., `www.example.com/webservice/client/get/term/syllabus` will return a list of JSON objects which contain the term `syllabus` in it's metadata.

Also, details of all the files in the server can be retrieved using the URL:

`www.example.com/webservice/client/get`

* Page requests are sent as follows:

`www.example.com/webservice/client/get/<pdf-file-name>/<page-number>`

This will return a JPEG image, enoded within a JSON response, of the requested page of the specific PDF file.
