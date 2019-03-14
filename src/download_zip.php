<?php
/**
 * Created by pravin chavan
 */
	include "facebook_auth.php";

    // check if logged in  or not
	if (isset($_SESSION['facebook_access_token'])) {
		$fb_obj->setDefaultAccessToken($_SESSION['facebook_access_token']);

		// fetch and decode post request data
		$postdata = file_get_contents("php://input");
		$albums_id = json_decode($postdata, true);

		// fecth logged in profile from fb
		$profile_response = $fb_obj->get('/me?fields=id');	
      	$profile = $profile_response->getGraphNode()->asArray();

      	// set folder name as profile id
      	$profile_folder_name = $profile['id'];
      	$profile_album_path="assets/$profile_folder_name";

      	// check if already exist folder
      	if (!file_exists($profile_album_path)) {
		    mkdir($profile_album_path, 0755);
		}

      	// create zip filename
		$zip_album_filename = "";
		foreach ($albums_id as $key => $value) {
			if (count($albums_id) == 1) {
				$zip_album_filename = strval($value["album_id"]);
			}
			else {
				$zip_album_filename = $zip_album_filename . '' . substr(strval($value["album_id"]), 4, 7);
			}
		}

		$zip = new ZipArchive;
		$zip_album_filename = $zip_album_filename . '.zip' ;
		// create zip file
		if($zip->open($profile_album_path . '/' . $zip_album_filename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
			try {
		        foreach ($albums_id as $key => $value) {
		            $albumID = $value["album_id"];
		            $albumName = $value["album_name"];

		            // get all photos of selected album
		            $album_image_response = $fb_obj->get("/" . $albumID . "/photos?fields=source,name,id");
		            $album_images = $album_image_response->getGraphEdge()->asArray();

		            foreach ($album_images as $album_image) {
		                $data = file_get_contents($album_image['source']);

		                // create path for saving image file to albums directory
		                $album_folder_path = $profile_album_path . "/" . $albumName;
		                if (!file_exists($album_folder_path)) {
						    mkdir($album_folder_path, 0755);
						}

		                // create image file
		                $file = fopen($album_folder_path . "/" . $album_image['id'] . ".jpg", "w");
		                   	if (!$file) exit;
		                   	fwrite($file, $data);
		                   	fclose($file);

		                // add image file to zip folder
		                $filename = $album_folder_path . "/" . $album_image['id'] . ".jpg";
		                $path = $album_image['id'] . '.jpg';
		                $zip->addFile($filename, $path);
		            }
		        }
		    } catch (Facebook\Exceptions\FacebookResponseException $e) {
		        // When Graph returns an error
		        echo 'Graph returned an error: ' . $e->getMessage();
		        // redirecting user back to app login page
		        header("Location: ./");
		        exit;
		    } catch (Facebook\Exceptions\FacebookSDKException $e) {
		        // When validation fails or other lqocal issues
		        echo 'Facebook SDK returned an error: ' . $e->getMessage();
		        exit;
		    }
		    $zip->close();
		}

		// return zip folder path
		echo $profile_album_path . '/' . $zip_album_filename;
	} else {
    	header("location:https://socialalbum.000webhostapp.com/login.php");
  	}