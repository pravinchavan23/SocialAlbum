/**
 * Created by pravin chavan
 */

// redirect to google auth url
function googleauth(url) {
	window.location = url;
}

// move single album to drive
function moveAlbum(albumId, albumName) {
	var data = [];
	data.push({ album_id : albumId, album_name: albumName });

	move(data);
}

// check whether check box is checked or not and perform action accordingly
$(":checkbox[name^=selectAlbums]").on("click", function(){

	// show move button if checked
	if(this.checked) {
		if(!$('#selectedMoveBtn')[0]) {
			$('#moveAlbumsParent').append('<button type="button" class="btn btn-secondary btn-sm" id="selectedMoveBtn" ' +
				'onclick="moveSelectedAlbum()">Move Selected Albums</button>');
		}
	}

	// else remove move button
	if(!$('input[name^=selectAlbums]').is(':checked')) {
		if($('#selectedMoveBtn')[0]) {
			$('#selectedMoveBtn').remove();
		}
	}
});

// move selected albums to drive
function moveSelectedAlbum() {
	var albumsSelected = [];
  	$.each($("input[name='selectAlbums']:checked"), function(){
		albumsSelected.push({ album_id : $(this).val(), album_name: $('#' + $(this).val() + 'name').val() });
 	});

	move(albumsSelected);
}

// move all albums to drive
function moveAll() {
	var albumsSelected = [];
	$.each($("input[type='hidden']"), function(){
		albumsSelected.push({ album_id : this.id.replace('name', ''),
			album_name: $(this).val() });
	});

	move(albumsSelected);
}

// move function to make ajax call
function move(albumsSelected) {
	$('#alertDiv').empty();
	$('#loader').css("display", "block");

	$.ajax({
		url: 'move_album.php',
		type: 'POST',
		data: JSON.stringify(albumsSelected),
		success: function(result){
			$('#loader').css("display", "none");

			let message = JSON.parse(result);
			if(message.status === 'SUCCESS')
				$('#alertDiv').append('<div class="alert alert-success" role="alert">\n' +
					message.message +
					'<button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
					'    <span aria-hidden="true">&times;</span>\n' +
					'</button>' +
					'</div>');
			else
				$('#alertDiv').append('<div class="alert alert-danger" role="alert">\n' +
					message.message +
					'<button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
					'    <span aria-hidden="true">&times;</span>\n' +
					'</button>' +
					'</div>');
		},
		error: function (error) {
			$('#loader').css("display", "none");

			let message = JSON.parse(error);
			$('#alertDiv').append('<div class="alert alert-danger" role="alert">\n' +
				message.message +
				'<button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
				'    <span aria-hidden="true">&times;</span>\n' +
				'</button>' +
				'</div>');
		}
	});
}