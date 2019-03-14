<?php
/**
 * Created by pravin chavan
 */
require_once __DIR__ . '/lib/google/vendor/autoload.php';

class Google
{
    // google configuration
    private $clientId = 'your_clientId'; //Google client ID
    private $clientSecret = 'your_secret'; //Google client secret
    private $redirectURL = 'https://socialalbum.000webhostapp.com/'; //Callback URL
    private $scope = array(
    'https://www.googleapis.com/auth/drive.file',
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile');

    var $g_client = "";
    function __construct()
    {
        $this->g_client = new Google_Client();
        $this->g_client->setClientId($this->clientId);
        $this->g_client->setRedirectUri($this->redirectURL);
        $this->g_client->setClientSecret($this->clientSecret);
        $this->g_client->setAccessType('offline');
        $this->g_client->setScopes($this->scope);
    }

    // return logged in user information
    function getuserinfo()
    {
        return (new Google_Service_Oauth2($this->g_client))->userinfo->get();
    }

    // set google token for logged in user to save album to drive
    function authcredentialscode($credentialscode)
    {
        try {
            $this->g_client->authenticate($credentialscode);
            $_SESSION['token'] = $this->g_client->getAccessToken();
        } catch (Exception $e) {
            print 'An error occurred: ' . $e->getMessage();
        }
    }

    // check credentials of logged in user
    function checkcredentials()
    {
        if (isset($_SESSION['token'])) {
            $this->g_client->setAccessToken($_SESSION['token']);
        }
        if ($this->g_client->getAccessToken()) {
            return true;
        } else {
            return false;
        }
    }

    // create subfolder for album inside logged in users facebook folder inside google drive
    function createSubFolder($google_service, $parentFolder, $folderName)
    {
        $files = $google_service->files->listFiles(array('q' => "trashed=false"));
        $folderId = NULL;

        // check if folder already exist with folder name
        if (!empty($files))
            foreach ($files as $item) {
                if ($item['name'] == $folderName) {
                    $folderId = $item['id'];
                    break;
                }
            }

        // if not exist create new folder
        if (empty($folderId)) {
            $subFolder = new Google_Service_Drive_DriveFile();
            $subFolder->setName($folderName);
            $subFolder->setMimeType('application/vnd.google-apps.folder');
            $subFolder->setParents(array($parentFolder));

            try {
                $subFolderMeataData = $google_service->files->create($subFolder, array(
                    'fields' => 'id'
                ));

                return $subFolderMeataData->id;
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
            }
        }
        else
            return $folderId;
    }

    // create folder related to profiles facebook album
    function createProfileFolder($google_service, $folderName, $folderDesc)
    {
        $files = $google_service->files->listFiles(array('q' => "trashed=false", 'fields' => 'files(id, name)'));
        $folderId = NULL;

        // check if folder already exist with folder name
        if (!empty($files))
            foreach ($files as $item) {
                if ($item['name'] == $folderName) {
                    $folderId = $item['id'];
                    break;
                }
            }

        // if not exist create new folder
        if (empty($folderId)) {
            $folder = new Google_Service_Drive_DriveFile();
            //Setup the folder to create
            $folder->setName($folderName);
            if (!empty($folderDesc))
                $folder->setDescription($folderDesc);
            $folder->setMimeType('application/vnd.google-apps.folder');
            //Create the Folder
            try {
                $createdFile = $google_service->files->create($folder, array(
                    'fields' => 'id'
                ));
                // Return the created folder's id
                return $createdFile->id;
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
            }
        }
        else
            return $folderId;
    }

    // insert image file inside album folder
    function insertFile($google_service, $title, $mimeType, $filename, $album_folder_path, $subFolder)
    {
        $files = $google_service->files->listFiles(array('q' => "trashed=false", 'fields' => 'files(id, name)'));
        $folderId = NULL;

        // check if file already exist
        if (!empty($files))
            foreach ($files as $item) {
                $file_path = '';
                $file_path = $album_folder_path . '/' . $item['name'] . '.jpg';

                if ($file_path == $filename) {
                    $folderId = $item['id'];
                    break;
                }
            }

        // create new if not exist
        if (empty($folderId)) {
            $file = new Google_Service_Drive_DriveFile();
            // Set the metadata
            $file->setName($title);
            $file->setDescription("");
            $file->setMimeType($mimeType);
            $file->setParents(array($subFolder));

            try {
                // Get the contents of the file uploaded
                $data = file_get_contents($filename);

                // create new file to google drive
                $createdFile = $google_service->files->create($file, array(
                    'data' => $data,
                    'mimeType' => $mimeType,
                    'uploadType' => 'multipart',
                    'fields' => 'id'
                ));
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
            }
        }
    }
}