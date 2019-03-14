<?php
/**
 * Created by pravin chavan
 */

include "facebook_auth.php";
include 'google.php';

//check if user is logged in or not
if (isset($_SESSION['facebook_access_token'])) {
    $fb_obj->setDefaultAccessToken($_SESSION['facebook_access_token']);

    // set googles token to save albums to drive
    $gle = new Google();
    $gle->g_client->setAccessToken($_SESSION["token"]);
    $google_service = new Google_Service_Drive($gle->g_client);

    // fetch posted data
    $postdata = file_get_contents("php://input");
    $albums_id = json_decode($postdata, true);

    // fetch logged in facebook user
    $profile_response = $fb_obj->get('/me?fields=id, name');
    $profile = $profile_response->getGraphNode()->asArray();

    // set folder name as profile id
    $profile_folder_name = $profile['id'];
    $profile_album_path="assets/$profile_folder_name";

    // check if already exist folder
    if (!file_exists($profile_album_path)) {
        mkdir($profile_album_path, 0755);
    }

    try {
        // create profiles folder inside google drive
        $gdProfileFolder = $gle->createProfileFolder($google_service,
            "facebook_".str_replace(" ", "_", $profile['name'])."_album",
            "");

        // create album folders inside profile folder
        foreach ($albums_id as $key => $value) {
            $albumID = $value["album_id"];
            $albumName = $value["album_name"];
            // fetch all images of album
            $album_image_response = $fb_obj->get("/" . $albumID . "/photos?fields=source,name,id");
            $album_images = $album_image_response->getGraphEdge()->asArray();
            // create sub folder for album inside profile folder
            $gdProfileSubFolder=$gle->createSubFolder($google_service, $gdProfileFolder, $albumName);

            // add images to album folder inside drive
            foreach ($album_images as $album_image) {
                $data=file_get_contents($album_image['source']);

                $album_folder_path = $profile_album_path . "/" . $albumName;
                if (!file_exists($album_folder_path)) {
                    mkdir($album_folder_path, 0755);
                }

                $fp = fopen($album_folder_path . "/" . $album_image['id'] . ".jpg","w");
                        if (!$fp) exit;
                        fwrite($fp, $data);
                $title=$album_image['id'];
                $filename=$album_folder_path . "/" . $album_image['id'] . ".jpg";
                $mimeType=mime_content_type ( $filename );
                // insert file into album subfolder in drive
                $gle->insertFile($google_service, $title,  $mimeType, $filename, $album_folder_path, $gdProfileSubFolder);
            }
        }

        // return success message
        echo json_encode(array('status'=>'SUCCESS', 'message'=>'Saved successfully to Google Drive!'));

    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        echo json_encode(array('status'=>'ERROR', 'message'=>'Graph returned an error: ' . $e->getMessage()));
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo json_encode(array('status'=>'ERROR', 'message'=>'Facebook SDK returned an error: ' . $e->getMessage()));
    } catch(Exception $e) {
        echo json_encode(array('status'=>'ERROR', 'message'=>'' . $e->getMessage()));
    }
} else{
    header("location:https://socialalbum.000webhostapp.com/login.php");
}