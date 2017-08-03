
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale = 1.0, user-scalable=no">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/preview_style.css" />

</head>
<body>

<div id="container">

	<div id="pdf-thumb-container">
		<ul id="pdf-thumb-results">
		</ul>
	</div>

	<div id="pdf-main-container">
		<div id="pdf-contents">
			<div id="pdf-meta">	
				<div id="pdf-filename"></div>
				<div id="pdf-title"></div>		
				<div id="pdf-author"></div>
			</div>
			<div id="page-loader"><img src="gif/page_loading.gif"/></div>
			
			<div id="imageDiv">
				<img id="myImg"/>
			</div>
			<br><br>
			<div id="pdf-buttons">
				<button id="pdf-prev">Previous</button>
				<button id="pdf-next">Next</button>
			</div>
			<div id="page-count-container">Page <div id="pdf-current-page"></div> of <div id="pdf-total-pages"></div></div>

			<br><br>
		</div>
	</div>
</div>
	
<script>

var __PDF_DOC,
	__CURRENT_PAGE,
	__PAGE_RENDERING_IN_PROGRESS = 0,
	__COUNT=1,
	__INITIAL_COUNT=4,	//used to load few thumbnails in the starting.
	
	
	__PAGE_COUNT= '<?php echo $_GET["pagecount"];?>',
	__FILENAME= '<?php echo $_GET["filename"]; ?>',
	__BASENAME= '<?php echo basename($_GET["filename"],".pdf"); ?>',
	__AUTHOR= '<?php echo $_GET["author"];?>',
	__TITLE= '<?php echo $_GET["title"];?>';
	
	
	//Load the meta data of the pdf.
	loadMeta();
	
		
	function loadMeta(){
		console.log("meta loaded");
		$("#pdf-contents").show();
		$("#pdf-total-pages").text(__PAGE_COUNT);
		$("#pdf-filename").text(__FILENAME);
		$("#pdf-title").text(__TITLE);
		$("#pdf-author").text(__AUTHOR);
		
		if(__PAGE_COUNT< __INITIAL_COUNT){
			__INITIAL_COUNT = __PAGE_COUNT;
		}
		console.log(__BASENAME);
		loadFewThumbnails();
		showPage(1);
	}	
	
	
	function loadFewThumbnails(){			
		$.ajax({
			url: "https://fleptic.eu/webservice/clientapi/get/page/" + __BASENAME + "/" + __COUNT ,
			type: "GET",
			dataType: "html",
			success: function(data) {
				var json = JSON.parse(data)["blob"];
				var page_src = 'data:image/jpg;base64,' + json;
				var html = '<li onclick="showPage(this.value)" value="' + __COUNT + '">'
				+ '<img src="' + page_src + '"/></li>';
				$("#pdf-thumb-results").append(html);
				__COUNT++;
				if(__COUNT<= __INITIAL_COUNT) loadFewThumbnails();
			}
		});	
	}
	
	
	function showPage(page_no) {
		__PAGE_RENDERING_IN_PROGRESS = 1;
		__CURRENT_PAGE = page_no;
				
		$.ajax({
			url: "https://fleptic.eu/webservice/clientapi/get/page/" + __BASENAME + "/" + page_no,
			type: "GET",
			dataType: "html",
			beforeSend: function () {
				// Disable Prev & Next buttons while page is being loaded
				$("#pdf-next, #pdf-prev").attr('disabled', 'disabled');
				
				// While page is being rendered hide the image and show a loading message
				$("#page-loader").show();
				$("#myImg").hide();
			},
			success: function(data) {
				__PAGE_RENDERING_IN_PROGRESS = 0;
				// Update current page in HTML
				$("#pdf-current-page").text(page_no);
				
				// Re-enable Prev & Next buttons
				$("#pdf-next, #pdf-prev").removeAttr('disabled');
				
				// Show the image and hide the page loader
				var page_src = JSON.parse(data)["blob"];
				$("#page-loader").hide();
				$("#myImg").show();
				$("#myImg").attr('src', 'data:image/jpg;base64,' + page_src);
			}
		});
	}	
	
// Previous page of the PDF
$("#pdf-prev").on('click', function() {
	if(__CURRENT_PAGE != 1)
		showPage(--__CURRENT_PAGE);
});

// Next page of the PDF
$("#pdf-next").on('click', function() {
	if(__CURRENT_PAGE != __PAGE_COUNT)
		showPage(++__CURRENT_PAGE);
});	

$("#pdf-thumb-container").scroll( function() {
					
	if( $(this).scrollTop() >= $(this)[0].scrollHeight - $(this).outerHeight()+4 && __COUNT <= __PAGE_COUNT){
		//console.log("end here");
		//var page_no=  $("#pdf-thumb-container li").length + 1;
		var page_no= __COUNT;
		
		$.ajax({
			url: "https://fleptic.eu/webservice/clientapi/get/page/" + __BASENAME + "/" + __COUNT,
			type: "GET",
			dataType: "html",
			success: function(data) {
				var json = JSON.parse(data)["blob"];
				var page_src = 'data:image/jpg;base64,' + json;
				var html = '<li onclick="showPage(this.value)" value="' + page_no + '">'
				+ '<img src="' + page_src + '"/></li>';
				$("#pdf-thumb-results").append(html);
				__COUNT++;
			}
		});
	}
				
});
		
</script>

</body>
</html>