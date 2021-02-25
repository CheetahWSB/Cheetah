<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    require_once(CH_DIRECTORY_PATH_BASE . 'scripts/ChBaseFunctions.php');

    class ChTemplFunctions extends ChBaseFunctions
    {
        /**
         * class constructor
        */
        public function __construct()
        {
            parent::__construct();
        }

        public function genSiteSearch($sText = '')
        {
            $sContent = parent::genSiteSearch($sText);

            return $GLOBALS['oSysTemplate']->parseHtmlByContent($sContent, array(
                ''
            ));
        }

        public function transBox($content, $isPlaceInCenter = false)
        {
            return
                ($isPlaceInCenter ? '<div class="login_ajax_wrap">' : '') .
                    $GLOBALS['oSysTemplate']->parseHtmlByName('service_transBox.html', array('content' => $content)) .
                ($isPlaceInCenter ? '</div>' : '');
        }

        public function genSiteServiceMenu()
        {
            $bLogged = isLogged();

            $aMenuItem = array();
            $sMenuPopupId = '';
            $sMenuPopupContent = '';
            $bShowVisitor = false;

            ch_import('ChTemplMenuService');
            $oMenu = new ChTemplMenuService();

            if ($bLogged) {
                $aProfile = getProfileInfo($oMenu->aMenuInfo['memberID']);

                $sThumbSetting = getParam('sys_member_info_thumb_icon');
                ch_import('ChWsbMemberInfo');

                $o = ChWsbMemberInfo::getObjectInstance($sThumbSetting);
                $sThumbUrl = $o ? $o->get($aProfile) : '';
                $bThumb = !empty($sThumbUrl);

                $o = ChWsbMemberInfo::getObjectInstance($sThumbSetting . '_2x');
                $sThumbTwiceUrl = $o ? $o->get($aProfile) : '';
                if (!$sThumbTwiceUrl) {
                    $sThumbTwiceUrl = $sThumbUrl;
                }

                $aMenuItem = array(
                    'ch_if:show_fu_thumb_image' => array(
                        'condition' => $bThumb,
                        'content' => array(
                            'image' => $sThumbUrl,
                            'image_2x' => $sThumbTwiceUrl,
                        )
                    ),
                    'ch_if:show_fu_thumb_icon' => array(
                        'condition' => !$bThumb,
                        'content' => array()
                    ),
                    'title' => getNickName($oMenu->aMenuInfo['memberID'])
                );

                $sMenuPopupId = 'sys-service-menu-' . time();
                $sMenuPopupContent = $this->transBox($oMenu->getCode());
            } else {
                $aItems = $oMenu->getItemsArray();
                if (!empty($aItems)) {
                    $bShowVisitor = true;
                    $bLoginOnly = ($aItems[0]['name'] == 'LoginOnly');
                    $aMenuItem = array(
                        'caption' => $bLoginOnly ? $aItems[0]['caption'] : _t('_sys_sm_join_or_login'),
                        'icon' => $bLoginOnly ? $aItems[0]['icon'] : 'user',
                        'script' => $aItems[0]['script'],

                        'ch_if:show_fu_thumb_image' => array(
                            'condition' => false,
                            'content' => array()
                        ),
                        'ch_if:show_fu_thumb_icon' => array(
                            'condition' => false,
                            'content' => array()
                        ),
                        'title' => ''
                    );
                }
            }

            return $GLOBALS['oSysTemplate']->parseHtmlByName('extra_service_menu_wrapper.html', array(
                'ch_if:show_for_visitor' => array(
                    'condition' => !$bLogged && $bShowVisitor,
                    'content' => $aMenuItem,
                ),
                'ch_if:show_for_user' => array(
                    'condition' => $bLogged,
                    'content' => $aMenuItem,
                ),
                'menu_popup_id' => $sMenuPopupId,
                'menu_popup_content' => $sMenuPopupContent,
            ));
        }
    }
    $oFunctions = new ChTemplFunctions();
