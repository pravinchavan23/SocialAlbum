<?php
/**
 * Created by pravin chavan
 */
  include "facebook_auth.php";
  include "google.php";

  $google_auth = new Google();

  // check whether user is logged into google for saving album to google drive
  if (!$google_auth->checkcredentials()) {
    if (isset($_GET['code'])) {
        $google_auth->authcredentialscode($_GET['code']);
    }
    $authUrl = $google_auth->g_client->createAuthUrl();
  }

  // check if user logged in using facebook
  if (isset($_SESSION['facebook_access_token'])) {
    $fb_obj->setDefaultAccessToken($_SESSION['facebook_access_token']);
    try {

      // get logged in users information
      $profile_response = $fb_obj->get('/me?fields=picture.width(200).height(200),id,name,cover');
      $profile = $profile_response->getGraphNode()->asArray();
      $profile_albums_response = $fb_obj->get("/" . $profile["id"] . "/albums?fields=picture,name,id");
      $albums = $profile_albums_response->getGraphEdge()->asArray();

     } catch (Facebook\Exceptions\FacebookResponseException $e) {
      echo 'Graph returned an error: ' . $e->getMessage();
      header("Location: https://socialalbum.000webhostapp.com/login.php");
      exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }
  } else {
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

    </head>

    <body>

        <!-- top nav bar -->
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

        <!-- loader image -->
        <div class="loader-parent" id="loader">
            <img src="theme/images/loader.gif" class="loader">
        </div>

        <!-- album container -->
        <div class="container">
            <div class="row">
                <?php if (!isset($_SESSION['token'])) { ?>
                    <div class="col-12 card-li">
                        <a class="navbar-toggle"  href="#" onclick='googleauth("<?=$authUrl?>")'>
                            Sign in
                        </a>
                        to Google to move albums to Google Drive.
                    </div>
                <?php } ?>
                <!-- top action bar for album -->
                <div class="col-12 card-li">
                    <div class="top-action-container">
                        <div id="downloadAlbumsParent" class="download-action-parent">
                            <button type="button" class="btn btn-secondary btn-sm" id="downloadAllBtn" onclick='downloadAll()'>
                                Download All Albums
                            </button>
                            <a href="#" class="btn btn-secondary btn-sm" id='downloadAllBtnLink' style="display:none;">Download All Albums</a>
                        </div>
                        <?php if (isset($_SESSION['token'])) { ?>
                            <div id="moveAlbumsParent" class="move-action-parent">
                                <button type="button" class="btn btn-secondary btn-sm" id="moveAllBtn" onclick='moveAll()'>
                                    Move All Albums
                                </button>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if (isset($_SESSION['token'])) { ?>
                        <div>
                            <small class="form-text text-muted">
                                <b>NOTE : </b>Move action will save all or selected albums to logged in Google Drive account!
                            </small>
                        </div>
                    <?php } ?>
                    <div id="alertDiv"></div>
                </div>
                <div class="col-12 card-li">
                    <hr>
                </div>
                <!-- logged in users albums -->
                <?php foreach($albums as $album) { ?>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 card-li">
                        <div class="card">
                            <div class="hd-image-div">
                                <a href="album_photos.php?album_id=<?=$album['id']?>">
                                    <img class="hd-image card-img-top"
                                         src="https://graph.facebook.com/<?=$album['id']?>/picture?access_token=<?=$_SESSION['facebook_access_token']?>"
                                         alt="Facebook album image">
                                </a>
                            </div>

                            <input type="hidden" id='<?=$album["id"]?>name' value="<?=$album['name']?>">

                            <div class="card-body row">
                                <div class="col-11"><?=$album["name"]?></div>
                                <div class="pull-right">
                                    <input type="checkbox" value="<?=$album["id"]?>" name="selectAlbums">
                                </div>
                            </div>

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <button type="button" class="btn btn-secondary btn-sm" id='<?=$album["id"]?>download'
                                            onclick='downloadAlbum(<?=$album["id"]?>, "<?=$album["name"]?>")'>
                                        Download
                                    </button>
                                    <a href="#" class="btn btn-secondary btn-sm" id='<?=$album["id"]?>link'
                                       style="display:none;">
                                        Download
                                    </a>

                                    <?php if (isset($_SESSION['token'])) { ?>
                                        <button type="button" class="btn btn-secondary btn-sm pull-right"
                                                onclick='moveAlbum(<?=$album["id"]?>, "<?=$album["name"]?>")'>
                                            Move to Drive
                                        </button>
                                    <?php } ?>
                                </li>
                            </ul>
                        </div>
                    </div>
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

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <script type="text/javascript" src="theme/js/download_zip.js"></script>
        <script type="text/javascript" src="theme/js/move_album.js"></script>

    </body>
</html>