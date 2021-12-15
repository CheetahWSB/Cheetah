<?php
//output buffer
ob_start();

echo '<html><head></head><body>';
ob_flush();
flush();

// Check OS.
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $sFFmpegLink = 'https://github.com/CheetahWSB/Cheetah/releases/download/V1.1.0/ffmpeg.exe';
    $sFFprobeLink = 'https://github.com/CheetahWSB/Cheetah/releases/download/V1.1.0/ffprobe.exe';
    $sTarget1 = '../plugins/ffmpeg/ffmpeg.exe';
    $sTarget2 = '../plugins/ffmpeg/ffprobe.exe';
    $sMsg1 = PHP_OS . ' detected. Downloading FFmpeg for Windows.';
    $sMsg2 = PHP_OS . ' detected. Downloading FFmpeg for Windows.';
} else {
    $sFFmpegLink = 'https://github.com/CheetahWSB/Cheetah/releases/download/V1.1.0/ffmpeg';
    $sFFprobeLink = 'https://github.com/CheetahWSB/Cheetah/releases/download/V1.1.0/ffprobe';
    $sTarget1 = '../plugins/ffmpeg/ffmpeg';
    $sTarget2 = '../plugins/ffmpeg/ffprobe';
    $sMsg1 = PHP_OS . ' detected. Downloading FFmpeg for Linux.';
    $sMsg2 = PHP_OS . ' detected. Downloading FFmpeg for Linux.';
}

//save progress to variable instead of a file
$temp_progress = '';
$targetFile = fopen($sTarget1, 'w');
echo '<script>window.parent.document.getElementById(\'plabel\').innerHTML  = "<b>' . $sMsg1 . '</b>";</script>';
ob_flush();
flush();
$ch = curl_init($sFFmpegLink);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_NOPROGRESS, false);
curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback');
curl_setopt($ch, CURLOPT_FILE, $targetFile);
curl_exec($ch);
curl_close($ch);
fclose($targetFile);

// Set proper permissions.
chmod($sTarget1, 0777);

$temp_progress = '';
$targetFile = fopen($sTarget2, 'w');
echo '<script>window.parent.document.getElementById(\'plabel\').innerHTML  = "<b>' . $sMsg2 . '</b>";</script>';
ob_flush();
flush();
$ch = curl_init($sFFprobeLink);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_NOPROGRESS, false);
curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback');
curl_setopt($ch, CURLOPT_FILE, $targetFile);
curl_exec($ch);
curl_close($ch);
fclose($targetFile);

// Set proper permissions.
chmod($sTarget2, 0777);


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
    //time_nanosleep(0, 100000); // Sleep
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
