<?php
/**
 * Created by pravin chavan
 */
	include 'facebook_auth.php';
	
	$helper = $fb_obj->getRedirectLoginHelper();

	// login url to redirect
	$permissions = ['email','user_photos']; // Optional permissions
	$loginUrl = $helper->getLoginUrl('https://socialalbum.000webhostapp.com/authorize.php', $permissions);
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

        <!-- login container -->
        <div class="card login-container">
            <div class="card-header">
                SocialAlbum
            </div>
            <div class="card-body">
                <a href="<?php echo $loginUrl; ?>">
                    <img src="theme/images/facebook-sign-in-button.png" alt="Login with Facebook" class="login-fb-img">
                </a>
            </div>
            <div class="card-footer text-muted footer-copyright">
                © 2018 Copyright: <a href="#"> SocialAlbum</a>
            </div>
        </div>


        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    </body>
</html>