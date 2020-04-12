<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$sModule = "video_comments";
require_once("../../../inc/header.inc.php");
require_once($sIncPath . "customFunctions.inc.php");

$iFileId = (int)$_GET["id"];
$s = getEmbedCode('video_comments', "player", array('id' => $iFileId));

$oAlert = new ChWsbAlerts('ch_video_comments', 'embed', $iFileId, getLoggedId(), array(
    'data' => &$s,
));
$oAlert->alert();

header('Content-type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        html, body { height:100%; background-color: transparent; }
        body { margin:0; padding:0; overflow:hidden; }
        object, object > embed, video { width:100%; height:100%; }
    </style>
</head>
<body class="ch-def-font" style="background: transparent;">
    <?=$s ?>
</body>
</html>
