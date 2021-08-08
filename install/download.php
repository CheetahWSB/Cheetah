<?php
//output buffer
ob_start();

echo '<html><head></head><body>';
ob_flush();
flush();

//save progress to variable instead of a file
$temp_progress = '';
$targetFile = fopen('../plugins/ffmpeg/ffmpeg', 'w');
echo '<script>window.parent.document.getElementById(\'plabel\').innerHTML  = "<b>Downloading FFmpeg</b>";</script>';
ob_flush();
flush();
$ch = curl_init('https://www.cheetahwsb.com/downloads/ffmpeg');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOPROGRESS, false);
curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback');
curl_setopt($ch, CURLOPT_FILE, $targetFile);
curl_exec($ch);
fclose($targetFile);


$temp_progress = '';
$targetFile = fopen('../plugins/ffmpeg/ffprobe', 'w');
echo '<script>window.parent.document.getElementById(\'plabel\').innerHTML  = "<b>Downloading FFProbe</b>";</script>';
ob_flush();
flush();
$ch = curl_init('https://www.cheetahwsb.com/downloads/ffprobe');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOPROGRESS, false);
curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback');
curl_setopt($ch, CURLOPT_FILE, $targetFile);
curl_exec($ch);
fclose($targetFile);

//must add $resource to the function after a newer php version. Previous comments states php 5.5
function progressCallback($resource, $download_size, $downloaded_size, $upload_size, $uploaded_size)
{
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
    //update javacsript progress bar to show download progress
    echo '<script>window.parent.document.getElementById(\'prog\').value = ' . $progress . ';</script>';
    ob_flush();
    flush();
    //sleep(1); // just to see effect
}


//if we get here, the download has completed
echo '<script>window.parent.document.getElementById(\'plabel\').innerHTML  = "<b>Download Complete</b>";</script>';
ob_flush();
flush();
echo '
    <script>
        window.parent.document.getElementById("nextBtn").disabled = false;
        window.parent.document.getElementById("nextBtn").classList.remove("ch-btn-disabled");
        window.parent.document.getElementById("nextBtn").classList.add("ch-btn-primary");
    </script>

    </body></html>

    ';
//flush just to be sure
ob_flush();
flush();
ob_end_clean();
