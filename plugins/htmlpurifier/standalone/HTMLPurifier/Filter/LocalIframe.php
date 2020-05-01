<?php

class HTMLPurifier_Filter_LocalIframe extends HTMLPurifier_Filter
{
    public $name = 'LocalIframe';

    public function preFilter($sHtml, $config, $context)
    {
        if (strstr($sHtml, '<iframe')) {
            $sHtml = str_ireplace("</iframe>", "", $sHtml);
            if (preg_match_all("/<iframe(.*?)>/si", $sHtml, $aResult)) {
                foreach ($aResult[1] as $key => $sItem) {
                    preg_match('/width="([0-9]+)"/', $sItem, $width);
                    $iWidth = $width[1];
                    preg_match('/height="([0-9]+)"/', $sItem, $height);
                    $iHeight = $height[1];
                    $sWsbUrl = preg_quote(CH_WSB_URL_ROOT);
                    $sIframeUrl = '';
echoDbgLog($sItem);
                    if (preg_match("#({$sWsbUrl}[a-zA-Z0-9_=\-\?\&\/]+)#", $sItem, $aMatches))
                        $sIframeUrl = $aMatches[1];
                    if (preg_match("#src=\"(((?!//)(?![a-z]+://)(?![a-z]+://))[a-zA-Z0-9_=\-\?\&\/]+)#", $sItem, $aMatches))
                        $sIframeUrl = $aMatches[1];
echoDbgLog($sIframeUrl);
                    if ($sIframeUrl)
                        $sHtml = str_replace($aResult[0][$key], '<img class="LocalIframe" width="' . $iWidth . '" height="' . $iHeight . '" src="' . $sIframeUrl . '">', $sHtml);
                }
            }
        }
        return $sHtml;
    }

    public function postFilter($sHtml, $config, $context)
    {
        $sPostRegex = '#<img class="LocalIframe" ([^>]+)>#';
        $sHtml = preg_replace_callback($sPostRegex, array($this, 'postFilterCallback'), $sHtml);
        return $sHtml;
    }

    protected function postFilterCallback($aMatches)
    {
        return '<iframe frameborder="0" allowfullscreen ' . $aMatches[1] . '></iframe>';
    }
}
