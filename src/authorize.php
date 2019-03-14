<?php
/**
 * Created by pravin chavan
 */
	include 'facebook_auth.php';

    if(!session_id()) {
        session_start();
    }
	
	$helper = $fb_obj->getRedirectLoginHelper();

	try {
        // get access token
        $accessToken = $helper->getAccessToken();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
	}

	//check if access token is null or not null
	if (!isset($accessToken)) {
        if ($helper->getError()) {
	        //set error message
            header('HTTP/1.0 401 Unauthorized');
            echo "Error: " . $helper->getError() . "\n";
            echo "Error Code: " . $helper->getErrorCode() . "\n";
            echo "Error Reason: " . $helper->getErrorReason() . "\n";
            echo "Error Description: " . $helper->getErrorDescription() . "\n";
        } else {
            header('HTTP/1.0 400 Bad Request');
            echo 'Bad request';
	  }
	  exit;
	}

	// Logged in
//	var_dump($accessToken->getValue());

	// The OAuth 2.0 client handler helps us manage access tokens
	$oAuth2Client = $fb_obj->getOAuth2Client();

	// Get the access token metadata from /debug_token
	$tokenMetadata = $oAuth2Client->debugToken($accessToken);
//	var_dump($tokenMetadata);

	// Validation (these will throw FacebookSDKException's when they fail)
	$tokenMetadata->validateAppId('your_app_id'); // Replace {app-id} with your app id
	// If you know the user ID this access token belongs to, you can validate it here
	//$tokenMetadata->validateUserId('123');
	$tokenMetadata->validateExpiration();

	if (! $accessToken->isLongLived()) {
	  // Exchanges a short-lived access token for a long-lived one
	  try {
	    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
	  } catch (Facebook\Exceptions\FacebookSDKException $e) {
	    echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
	    exit;
	  }

//	  var_dump($accessToken->getValue());
	}

	$_SESSION['facebook_access_token'] = (string) $accessToken;

	// User is logged in with a long-lived access token.
	// You can redirect them to a index page.
	header('location:https://socialalbum.000webhostapp.com/index.php');