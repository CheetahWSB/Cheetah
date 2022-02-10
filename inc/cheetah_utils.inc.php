<?php

/**
 * Cheetah - Social Network Software Platform. Copyright (c) Dean J. Bassett Jr. - https://www.cheetahwsb.com
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

 /**
  * Contains function Unique to Cheetah as of 1/23/2022 that are not in Dolphin.
  *
  * Add this line to the top of any script that requires these new functions.
  * require_once( CH_DIRECTORY_PATH_INC . 'cheetah_utils.inc.php' );
  */

require_once("header.inc.php");

// Similar to strpos except it takes and array of needles to search for.
function strposa($sHaystack, $aNeedles=array(), $iOffset=0) {
    $aChr = array();
    foreach($aNeedles as $sNeedle) {
            $bResult = strpos($sHaystack, $sNeedle, $iOffset);
            if ($bResult !== false) $aChr[$sNeedle] = $bResult;
    }
    if(empty($aChr)) return false;
    return min($aChr);
}

/**
 * @param {Integer} $iNumBars - Number of progress bars. 1 or 2. (Current function supports a maximum of 2 progress bars)
 * @param {Boolean} $bHasHeader - Include text header above progress bar. true or false.
 * @param {Integer} $iMin - The mininum value of the progress bar.
 * @param {Integer} $iMax - The maximum value of the progress bar.
 *
 * @return {String} The ID of the progress bar. (Progress bars ID is a string prefixed with ch_pb_ followed by a number generated with the php uniqid function)
 */
function generateProgressBar($iNumBars = 1, $bHasHeader = true, $iMin = 0, $iMax = 100) {
    $sBarId = str_replace('.', '_', uniqid('ch_pb_', true));





    return $sBarId;
}

function chCurlPb($sBarId, $sDownloadUrl, $sTargetFilePath) {
  $sTargetFile = fopen($sTargetFilePath, 'w');
  $ch = curl_init($sDownloadUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_NOPROGRESS, false);
  curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $download_size, $downloaded_size, $upload_size, $uploaded_size) use ($sBarId) {
      chCurlPbCallback($sBarId, $resource, $download_size, $downloaded_size, $upload_size, $uploaded_size);
  });
  curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'chCurlPbCallback');
  curl_setopt($ch, CURLOPT_FILE, $sTargetFile);
  curl_exec($ch);
  curl_close($ch);
  fclose($sTargetFile);
}

/**
 * @param {Sting} $sBarId - Id of the progress bar to update. (use string returned from the generateProgressBar function)
 * @param {Integer} $iValue - Set progress to passed value.
 */
function updateProgressBar($sBarId, $iValue) {
  // Expects download script to be in a iframe, and the progress bar to be in the parent window of the iframe.
  echo '<script>window.parent.document.getElementById(\'' . $sBarId . '\').value = ' . $iValue . ';</script>';
  ob_flush();
  flush();
}

function chCurlPbCallback($sBarId, $resource, $download_size, $downloaded_size, $upload_size, $uploaded_size) {
  static $previousProgress = 0;

  if ($download_size == 0) {
      $progress = 0;
  } else {
      $progress = round($downloaded_size * 100 / $download_size);
  }

  if ($progress > $previousProgress) {
      $previousProgress = $progress;
      $temp_progress = $progress;
  }
  updateProgressBar($sBarId, $progress);
}
