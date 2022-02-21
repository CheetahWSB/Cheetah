<?php
  require_once( '../../../inc/header.inc.php' );
  require_once( CH_DIRECTORY_PATH_INC . 'profiles.inc.php' );

  if ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) {
    header("HTTP/1.1 403 No Remote Access Allowed");
    return;
  }

  $iMemberID = getLoggedId();
  @mkdir($iMemberID);
  $sImageFolder = $iMemberID . '/';

  // Don't attempt to process the upload on an OPTIONS request
  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    return;
  }

  reset ($_FILES);
  $sUploadedFiles = current($_FILES);
  if (is_uploaded_file($sUploadedFiles['tmp_name'])){

    // Sanitize input
    if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $sUploadedFiles['name'])) {
        header("HTTP/1.1 400 Invalid file name.");
        return;
    }

    // Verify extension
    if (!in_array(strtolower(pathinfo($sUploadedFiles['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
        header("HTTP/1.1 400 Invalid extension.");
        return;
    }

    // Accept upload
    $sFileToWrite = $sImageFolder . $sUploadedFiles['name'];
    move_uploaded_file($sUploadedFiles['tmp_name'], $sFileToWrite);

    // Determine the base URL
    $sProtocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "https://" : "http://";
    $sBaseUrl = $sProtocol . $_SERVER["HTTP_HOST"] . rtrim(dirname($_SERVER['REQUEST_URI']), "/") . "/";

    // Respond to the successful upload with JSON.
    // Use a location key to specify the path to the saved image resource.
    // { location : '/your/uploaded/image/file'}
    echo json_encode(array('location' => $sBaseUrl . $sFileToWrite));
  } else {
    // Notify editor that the upload failed
    header("HTTP/1.1 500 Server Error");
  }
