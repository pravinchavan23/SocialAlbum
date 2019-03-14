/**
 * Created by pravin chavan
 */

// download album
function downloadAlbum(albumId, albumName) {
	var data = [];
	data.push({ album_id : albumId, album_name: albumName });

	$('#loader').css("display", "block");

	$.ajax({
		url: 'download_zip.php', 
		type: 'POST',
		data: JSON.stringify(data),		
		success: function(result){
			$('#loader').css("display", "none");

	    	$("#" + albumId + "download").remove();
	    	$("#" + albumId + "link").css("display", "");
	    	$("#" + albumId + "link").attr("href", result);
	    	if($("#" + albumId + "link")[0])
	    		document.getElementById($("#" + albumId + "link")[0].id).click();
	  	}
  	});
}

// check wheather check box is checked and perform actions accordingly
$(":checkbox[name^=selectAlbums]").on("click", function(){

	// if checked show download selected album button
	if(this.checked) {
		if(!$('#selectedDownloadBtn')[0]) {
			$('#downloadAlbumsParent').append('<button type="button" class="btn btn-secondary btn-sm" ' +
				'id="selectedDownloadBtn" onclick="downloadSelectedAlbum()">Download Selected Albums</button>');
			$('#downloadAlbumsParent').append('<a href="#" class="btn btn-secondary btn-sm" ' +
				'id="selectedDownloadBtnLink" style="display:none;">Download Selected Albums</button>');
		}			
	}

	// else remove download select album button
	if(!$('input[name^=selectAlbums]').is(':checked')) {
		if($('#selectedDownloadBtn')[0]) {
			$('#selectedDownloadBtn').remove();
			$('#selectedDownloadBtnLink').remove();
		}
	}
} );

// download selected albums
function downloadSelectedAlbum() {
	var albumsSelected = [];
  	$.each($("input[name='selectAlbums']:checked"), function(){
		albumsSelected.push({ album_id : $(this).val(), album_name: $('#' + $(this).val() + 'name').val() });
 	});

	$('#loader').css("display", "block");

 	$.ajax({
		url: 'download_zip.php', 
		type: 'POST',
		data: JSON.stringify(albumsSelected),		
		success: function(result){

			$('#loader').css("display", "none");

	    	$("#selectedDownloadBtnLink").attr("href", result);
	    	if($("#selectedDownloadBtnLink")[0])
	    		document.getElementById($("#selectedDownloadBtnLink")[0].id).click();
	  	}
  	});
}

// download all albums
function downloadAll() {
	var albumsSelected = [];
	$.each($("input[type='hidden']"), function(){
		albumsSelected.push({ album_id : this.id.replace('name', ''),
			album_name: $(this).val() });
	});

	$('#loader').css("display", "block");

	$.ajax({
		url: 'download_zip.php',
		type: 'POST',
		data: JSON.stringify(albumsSelected),
		success: function(result){

			$('#loader').css("display", "none");

			$("#downloadAllBtn").remove();
			$("#downloadAllBtnLink").css("display", "");
			$("#downloadAllBtnLink").attr("href", result);
			if($("#downloadAllBtnLink")[0])
				document.getElementById($("#downloadAllBtnLink")[0].id).click();
		}
	});
}