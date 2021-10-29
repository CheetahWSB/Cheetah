<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChBaseMenuService');

/**
* @see ChBaseMenuService;
*/
class ChTemplMenuService extends ChBaseMenuService
{
    /**
    * Class constructor;
    */
    public function __construct()
    {
        parent::__construct();
    }


    public function getCode()
    {
        $iMemberID = getLoggedId();

        $aItems = $GLOBALS['MySQL']->getAll("SELECT * FROM `sys_menu_service` WHERE FIND_IN_SET('memb', `Visible`) > 0");

        $sItems = '';
        foreach ($aItems as $aItem) {
            if ($aItem['Name'] == 'Logout') {
                continue;
            }
            if ($aItem['Name'] == 'Profile') {
                continue;
            }

            list($aItem['Link']) = explode('|', $aItem['Link']);

            $aItem['Caption'] = _t($this->replaceMetas($aItem['Caption']));
            $aItem['Link'] = $this->replaceMetas($aItem['Link']);
            $aItem['Script'] = $this->replaceMetas($aItem['Script']);

            $sItems .= '
              <div class="sys-sm-link" style="padding: 2px 0px 2px 15px;">
                  <a class="sys-sm-link" href="' . $aItem['Link'] . '" title="' . $aItem['Caption'] . '">
                    <i class="sys-icon ' . $aItem['Icon'] . '"></i><span>' . $aItem['Caption'] . '</span>
                  </a>
              </div>
              ';
        }

        return $GLOBALS['oSysTemplate']->parseHtmlByName('service_menu.html', array(
              'title' => getParam('site_title'),
              'logout' => '<a href="logout.php?action=member_logout">' . _t('_Log Out') . '</a>',
              'thumb' => $GLOBALS['oFunctions']->getMemberThumbnail($iMemberID),
              'display_name' => getNickName(),
              'profile_link' => getProfileLink($iMemberID),
              'email' => $GLOBALS['MySQL']->getOne("SELECT `Email` FROM `Profiles` WHERE `ID` = '$iMemberID' LIMIT 1"),
              'content' => $sItems,
            ));
    }


    public function getItems()
    {
        $sContent = parent::getItems();

        return $GLOBALS['oSysTemplate']->parseHtmlByContent($sContent, array(
            'ch_if:show_profile_link' => array(
                'condition' => $this->aMenuInfo['memberID'] != 0,
                'content' => array(
                    'link' => getProfileLink($this->aMenuInfo['memberID']),
                    'title' => getNickName($this->aMenuInfo['memberID'])
                )
            )
        ));
    }
}
