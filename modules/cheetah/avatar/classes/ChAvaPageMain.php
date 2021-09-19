<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChAvaPageMain extends ChWsbPageView
{
    var $_oMain;
    var $_oTemplate;
    var $_oConfig;
    var $_oDb;

    function __construct(&$oMain)
    {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oConfig = $oMain->_oConfig;
        $this->_oDb = $oMain->_oDb;

        parent::__construct('ch_avatar_main');

        $GLOBALS['oTopMenu']->setCurrentProfileID($this->_oMain->_iProfileId);
    }

    function getBlockCode_Tight()
    {
        $aMyAvatars = array ();
        $aVars = array (
            'my_avatars' => $this->_oMain->serviceGetMyAvatars ($this->_oMain->_iProfileId),
            'ch_if:is_site_avatars_enabled' => array (
                'condition' => 'on' == getParam('ch_avatar_site_avatars'),
                'content' => array (
                    'site_avatars' => getParam('ch_avatar_site_avatars') ? $this->_oMain->serviceGetSiteAvatars (0) : _t('_Empty'),
                ),
            ),
        );
        return array($this->_oTemplate->parseHtmlByName('block_tight', $aVars), array(), array(), false);
    }

    function getBlockCode_Wide()
    {
        $sUploadErr = '';

        if (isset($_FILES['image'])) {
            $sUploadErr = $this->_oMain->_uploadImage () ? '' : _t('_ch_ava_upload_error');
            if (!$sUploadErr)
                send_headers_page_changed();
        }

        $aVars = array (
            'avatar' => $GLOBALS['oFunctions']->getMemberThumbnail ($this->_oMain->_iProfileId),
            'ch_if:allow_upload' => array (
                'condition' => $this->_oMain->isAllowedAdd(),
                'content' => array (
                    'action' => $this->_oConfig->getBaseUri(),
                    'upload_error' => $sUploadErr,
                ),
            ),
            'ch_if:allow_crop' => array (
                'condition' => $this->_oMain->isAllowedAdd(),
                'content' => array (
                    'crop_tool' => $this->_oMain->serviceCropTool (array (
                        'dir_image' => CH_AVA_DIR_TMP . $this->_oMain->_iProfileId . CH_AVA_EXT,
                        'url_image' => CH_AVA_URL_TMP . $this->_oMain->_iProfileId . CH_AVA_EXT . '?' . time(),
                    )),
                ),
            ),
            'ch_if:display_premoderation_notice' => array (
                'condition' => getParam('autoApproval_ifProfile') != 'on',
                'content' => array (),
            ),
            'ch_if:allow_upload_to_photos' => array (
              'condition' => getParam('ch_avatar_allow_upload_to_photos') == 'on',
              'content' => array (),
            ),
        );

        return array($this->_oTemplate->parseHtmlByName('block_wide', $aVars), array(), array(), false);
    }
}
