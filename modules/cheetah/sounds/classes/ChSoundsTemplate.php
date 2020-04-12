<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbFilesTemplate');

class ChSoundsTemplate extends ChWsbFilesTemplate
{
    function __construct (&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);
    }

    function getFileConcept ($iFileId, $aExtra = array())
    {
        $sOverride = false;
        $oAlert = new ChWsbAlerts($this->_oConfig->getMainPrefix(), 'display_player', $iFileId, getLoggedId(), array('extra' => $aExtra, 'override' => &$sOverride));
        $oAlert->alert();
        if ($sOverride)
            return $sOverride;

        $iFileId = (int)$iFileId;
        return '<div class="viewFile" style="width:100%">' . getApplicationContent('mp3', 'player', array('id' => $iFileId, 'user' => (int)$_COOKIE['memberID'], 'password' => clear_xss($_COOKIE['memberPassword'])), true) . '</div>';
    }

    function getViewFile (&$aInfo)
    {
        $oVotingView = new ChTemplVotingView('ch_' . $this->_oConfig->getUri(), $aInfo['medID']);
        $iWidth = (int)$this->_oConfig->getGlParam('file_width');
        if ($aInfo['prevItem'] > 0)
            $aPrev = $this->_oDb->getFileInfo(array('fileId'=>$aInfo['prevItem']), true, array('medUri', 'medTitle'));
        if ($aInfo['nextItem'] > 0)
            $aNext = $this->_oDb->getFileInfo(array('fileId'=>$aInfo['nextItem']), true, array('medUri', 'medTitle'));
        $aUnit = array(
            'file' => $this->getFileConcept($aInfo['medID']),
            'width' => $iWidth,
            'fileUrl' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aInfo['medUri'],
            'fileTitle' => $aInfo['medTitle'],
            'fileDescription' => nl2br($aInfo['medDesc']),
            'rate' => $oVotingView->isEnabled() ? $oVotingView->getBigVoting(1, $aInfo['Rate']): '',
            'favInfo' => isset($aInfo['favCount']) ? $aInfo['favCount'] : '',
            'viewInfo' => $aInfo['medViews'],
            'albumUri' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/album/' . $aInfo['albumUri'] . '/owner/' . $aInfo['NickName'],
            'albumCaption' => $aInfo['albumCaption'],
            'ch_if:prev' => array(
                'condition' => $aInfo['prevItem'] > 0,
                'content' => array(
                    'linkPrev'  => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aPrev['medUri'],
                    'titlePrev' => $aPrev['medTitle'],
                    'percent' => $aInfo['nextItem'] > 0 ? 50 : 100,
                )
            ),
            'ch_if:next' => array(
                'condition' => $aInfo['nextItem'] > 0,
                'content' => array(
                    'linkNext'  => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aNext['medUri'],
                    'titleNext' => $aNext['medTitle'],
                    'percent' => $aInfo['prevItem'] > 0 ? 50 : 100,
                )
            ),
        );
        return $sCode = $this->parseHtmlByName('view_unit.html', $aUnit);
    }

    function getCompleteFileInfoForm (&$aInfo, $sUrlPref = '')
    {
        $aMain = $this->getBasicFileInfoForm($aInfo, $sUrlPref);
        if ($aInfo['AllowAlbumView'] == CH_WSB_PG_ALL)
        {
            $aAdd = array(
                'embed' => array(
                    'type' => 'text',
                    'value' => $this->getEmbedCode($aInfo['medID']),
                    'attrs' => array(
                      'onclick' => 'this.focus(); this.select();',
                      'readonly' => 'readonly',
                    ),
                    'caption'=> _t('_Embed')
                ),
            );
            $aMain = array_merge($aMain, $aAdd);
        }
        return $aMain;
    }

    function getEmbedCode ($iFileId)
    {
        $sOverride = false;
        $oAlert = new ChWsbAlerts($this->_oConfig->getMainPrefix(), 'embed_code', $iFileId, getLoggedId(), array('override' => &$sOverride));
        $oAlert->alert();
        if ($sOverride)
            return $sOverride;

        $iFileId = (int)$iFileId;
        return getEmbedCode('mp3', 'player', array('id'=>$iFileId));
    }
}
