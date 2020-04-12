<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

class ChWsbGzip
{
    var $_sType;
    var $_bGzip;
    var $_sEncoding;
    var $_iExpirationOffset;

    var $_sInFile;

    var $_sOutFile;
    var $_sOutContent;
    var $_sOutContentZipped;

    function __construct($sFile)
    {
        $this->_iExpirationOffset = 3600 * 24 * 10;

        $this->_sInFile = CH_DIRECTORY_PATH_CACHE_PUBLIC . $sFile;
        $this->_sOutFile = CH_DIRECTORY_PATH_CACHE_PUBLIC . $sFile . '.gz';

        $aMatches = array();
        if(!preg_match("/^([a-z0-9_-]+)\.(js|css)$/", $sFile, $aMatches))
            die();

        switch($aMatches[2]) {
            case 'css':
                $this->_sType = 'css';
                break;
            case 'js':
                $this->_sType = 'javascript';
                break;
        }

        $this->_sOutContent = "";
        $this->_sOutContentZipped = "";
    }

    public static function load($sFile)
    {
        $oLoader = new ChWsbGzip($sFile);

        $oLoader->prepare();
        $oLoader->read();
        $oLoader->output();
    }

    function prepare()
    {
        header("Content-type: text/" . $this->_sType);
        header("Vary: Accept-Encoding");
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + $this->_iExpirationOffset) . " GMT");

        $encodings = array ();
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
            $encodings = explode(',', strtolower(preg_replace("/\s+/", "", $_SERVER['HTTP_ACCEPT_ENCODING'])));

        if ((in_array('gzip', $encodings) || in_array('x-gzip', $encodings) || isset($_SERVER['---------------'])) && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression')) {
            $this->_sEncoding = in_array('x-gzip', $encodings) ? "x-gzip" : "gzip";
            $this->_bGzip = true;
        }
    }

    function read()
    {
        if($this->_bGzip && file_exists($this->_sOutFile))
            $this->_sOutContentZipped = $this->getFileContents($this->_sOutFile);
        else
            $this->_sOutContent = $this->getFileContents($this->_sInFile);
    }
    function output()
    {
        if(!$this->_bGzip) {
            echo $this->_sOutContent;
            return;
        }

        header("Content-Encoding: " . $this->_sEncoding);
        if(!empty($this->_sOutContentZipped)) {
            echo $this->_sOutContentZipped;
            return;
        }

        if (!$this->_sOutContent)
            return;

        $this->_sOutContentZipped = gzencode($this->_sOutContent, 9, FORCE_GZIP);
        $this->putFileContents($this->_sOutFile, $this->_sOutContentZipped);

        echo $this->_sOutContentZipped;
    }
    function getFileContents($sPath)
    {
        $sPath = realpath($sPath);

        if(!$sPath || !@is_file($sPath))
            return "";

        if(function_exists("file_get_contents"))
            return @file_get_contents($sPath);

        $sContent = "";
        if(!($rHandler = @fopen($sPath, "r")))
            return "";

        while(!feof($rHandler))
            $sContent .= fgets($rHandler);

        fclose($rHandler);

        return $sContent;
    }

    function putFileContents($sPath, $sContent)
    {
        if (function_exists("file_put_contents"))
            return @file_put_contents($sPath, $sContent);

        if(!($rHandler = @fopen($sPath, "wb")))
            return 0;

        $iResult = (int)fwrite($rHandler, $sContent);
        fclose($rHandler);

        return $iResult;
    }
}
