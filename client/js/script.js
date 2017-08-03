var max;
var html;
google.load("jquery", "1.3.1");
google.setOnLoadCallback(function()
{
	// Safely inject CSS3 and give the search results a shadow
	var cssObj = { 'box-shadow' : '#888 5px 10px 10px', // Added when CSS3 is standard
		'-webkit-box-shadow' : '#888 5px 10px 10px', // Safari
		'-moz-box-shadow' : '#888 5px 10px 10px'}; // Firefox 3.5+
	$("#suggestions").css(cssObj);
	
	// Fade out the suggestions box when not active
	 $("input").blur(function(){
	 	$('#suggestions').fadeOut();
		html = '<ul id="searchresults">';
		
	 });
});

function lookup(inputString) {
	if(inputString.length == 0) {
		$('#suggestions').fadeOut(); // Hide the suggestions box
		$("#suggestions").html("");
		html = '<ul id="searchresults">';
	} else {
		$('#suggestions').html("");
		html = '<ul id="searchresults">';
		console.log("html= " +html);
		loadResults(inputString);			
	}
}

function loadResults(inputString) {
    $.ajax({
        url: "https://fleptic.eu/webservice/clientapi/get/term/" + inputString,
        type: "GET",
        dataType: "html",
		beforeSend: function () {				
			$("#loader-gif").show();
		},
        success: function(data) {
			max = JSON.parse(data)["result_count"];
			$("#loader-gif").hide();
			$("#suggestions").fadeIn(); // Show the suggestions box
			fillContents(data, max);
        },
		
		error: function (xhr, ajaxOptions, thrownError) {
			console.log("xhr:  " + xhr.status);
			console.log("thrown:  " + thrownError);
		}
    });
}

function fillContents(data, count){
	var json = JSON.parse(data);
	var a= json["results"];
			
	for(var i = 0; i < count; i++){
		var obj = a[i];
		var params = 'filename=' + obj.filename + '&title=' + obj.title + '&author=' + obj.author + '&pagecount=' + obj.pagecount;
		var url = 'view.php?' + params;
		console.log(url);
		html+= '<li><a href="' + url + '">' //'"><img src="data:image/jpg;base64,' + obj.blob + '">'
				+ '<span class="pdffilename">' + obj.filename + '</span></br>'
			    +  '<span class="pdftitle">' + obj.title + '</span></a></li>';
	}
	html+= '</ul>';
	console.log(html);
	$("#suggestions").html(html); // Fill the suggestions box   
	html = "";
}
