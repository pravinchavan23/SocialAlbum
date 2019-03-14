<?php
/**
 * Created by pravin chavan
 */
	include "facebook_auth.php";

    // check if logged in or not
	if (isset($_SESSION['facebook_access_token'])) {
		$fb_obj->setDefaultAccessToken($_SESSION['facebook_access_token']);

		$album_id = $_GET['album_id'];
		$album_response = $fb_obj->get("/" . $album_id . "/photos?fields=source,name,id");
		$album_photos = $album_response->getGraphEdge()->asArray();	
	}
	else {
		header("location:https://socialalbum.000webhostapp.com/login.php");
	}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Social Album</title>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="theme/css/styles.css">
        <link rel="stylesheet" href="theme/lightGallery/css/lightgallery.css">

    </head>
    <body>
        <!-- navbar -->
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="#">SocialAlbum</a>

            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mr-auto">
                </ul>
                <span class="navbar-text">
                    <a class="nav-link" href="https://socialalbum.000webhostapp.com/logout.php">Logout</a>
                </span>
            </div>
        </nav>

        <div class="container">
            <!-- back button -->
            <div class="row">
                <div class="col-12 card-li">
                    <button type="button" class="btn btn-link" id="backBtn">Back</button>
                    <hr>
                </div>
            </div>

            <div class="row" id="gallery">
                <!-- gallery -->
                <?php
                foreach ($album_photos as $album_photo) {
                    ?>
                    <a href="<?= $album_photo['source'] ?>" class="col-xs-12 col-sm-6 col-md-6 col-lg-4 card-li">
                        <div class="card">
                            <div class="hd-image-div">
                                <img class="hd-image gappery-img"
                                     src="<?= $album_photo['source'] ?>"
                                     alt="Facebook album image">
                            </div>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>

        <!-- Footer -->
        <footer class="page-footer font-small blue">
            <!-- Copyright -->
            <div class="footer-copyright text-center py-3">© 2018 Copyright:
                <a href="#">SocialAlbum</a>
            </div>
            <!-- Copyright -->
        </footer>
        <!-- Footer -->

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>

        <script src="theme/lightGallery/js/lightgallery-all.js"></script>
        <script type="text/javascript" src="theme/js/album_photos.js"></script>

    </body>
</html>