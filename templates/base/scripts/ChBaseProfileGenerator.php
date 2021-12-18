<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbProfile');
ch_import('ChWsbPaginate');
ch_import('ChWsbProfileFields');
ch_import('ChTemplVotingView');
ch_import('ChWsbCmtsProfile');

class ChBaseProfileGenerator extends ChWsbProfile
{
    public $oTemplConfig;
    //var $sColumnsOrder;
    public $oPF; // profile fields object
    public $aPFBlocks; //profile fields blocks
    public $aCoupleMutualItems;
    public $bPFEditable = false;

    public $iCountMutFriends;
    public $iFriendsPerPage;

    public function __construct($ID)
    {
        global $site;

        $this->aMutualFriends = array();

        parent::__construct($ID, 0);

        $this->oVotingView = new ChTemplVotingView('profile', (int)$ID);
        $this->oCmtsView   = new ChWsbCmtsProfile('profile', (int)$ID);

        //$this->ID = $this->_iProfileID;

        $this->oTemplConfig = new ChTemplConfig($site);
        //$this->sColumnsOrder = getParam( 'profile_view_cols' );
        //INSERT INTO `sys_options` VALUES('profile_view_cols', 'thin,thick', 0, 'Profile view columns order', 'digit', '', '', NULL, '');

        if ($this->_iProfileID) {
            $this->getProfileData();

            if ($this->_aProfile) {
                if (isMember()) {
                    $iMemberId = getLoggedId();
                    if ($iMemberId == $this->_iProfileID) {
                        $this->owner = true;

                        if ($_REQUEST['editable']) {
                            $this->bPFEditable = true;
                            $iPFArea           = 2; // Edit Owner
                        } else {
                            $iPFArea = isAdmin() ? 5 : 6;
                        } // View Owner
                    } else {
                        $iPFArea = isAdmin() ? 5 : 6;
                    }
                } elseif (isModerator()) {
                    $iPFArea = 7;
                } else {
                    $iPFArea = 8;
                }

                $this->oPF = new ChWsbProfileFields($iPFArea);
                if (!$this->oPF->aBlocks) {
                    return false;
                }

                $this->aPFBlocks = $this->oPF->aBlocks;

                if ($this->bCouple) {
                    $this->aCoupleMutualItems = $this->oPF->getCoupleMutualFields();
                }

                $this->iFriendsPerPage = (int)getParam('friends_per_page');
                $this->FindMutualFriends($iMemberId, $_GET['page'], $_GET['per_page']);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function genColumns($sOldStyle = false)
    {
        ob_start(); ?>
        <div id="thin_column">
            <?php $this->showColumnBlocks(1, $sOldStyle); ?>
        </div>

        <div id="thick_column">
            <?php $this->showColumnBlocks(2, $sOldStyle); ?>
        </div>
        <?php

        return ob_get_clean();
    }

    public function showColumnBlocks($column, $sOldStyle = false)
    {
        $sVisible = ($GLOBALS['logged']['member']) ? 'memb': 'non';

        $sAddSQL = ($sOldStyle == true) ? " AND `Func`='PFBlock' " : '';
        $rBlocks = db_res("SELECT * FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Column`=$column AND FIND_IN_SET( '$sVisible', `Visible` ) {$sAddSQL} ORDER BY `Order`");
        while ($aBlock = $rBlocks ->fetch()) {
            $func = 'showBlock' . $aBlock['Func'];
            $this->$func($aBlock['Caption'], $aBlock['Content']);
        }
    }

    public function showBlockEcho($sCaption, $sContent)
    {
        echo DesignBoxContent(_t($sCaption), $sContent, 1);
    }

    public function showBlockPFBlock($iPageBlockID, $sCaption, $sContent, $bNoDB = false)
    {
        $iPFBlockID = (int)$sContent;

        $bMayEdit = ((isMember() || isAdmin()) && ($this->_iProfileID == getLoggedId()));

        $sRet = $this->getViewValuesTable($iPageBlockID, $iPFBlockID);

        if ($bNoDB) {
            if ($bMayEdit && $sRet) {
                return array(
                    '<div class="ch-def-bc-margin">' . $sRet . '</div>',
                    array(
                        _t('_Edit') => array(
                            //'caption' => _t('_Edit'),
                            'href'         => 'pedit.php?ID=' . $this->_iProfileID,
                            'dynamicPopup' => false,
                            'active'       => $this->bPFEditable,
                        ),
                    ),
                    array(),
                    '',
                );
            } else {
                return empty($sRet) ? $sRet : array('<div class="ch-def-bc-margin">' . $sRet . '</div>', array(), array(), '');
            }
        } else {
            echo DesignBoxContent(_t($sCaption), $sRet, 1);
        }
    }

    public function getViewValuesTable($iPageBlockID, $iPFBlockID)
    {
        if (!isset($this->aPFBlocks[$iPFBlockID]) or empty($this->aPFBlocks[$iPFBlockID]['Items'])) {
            return '';
        }

        // get parameters
        $bCouple = $this->bCouple;
        $aItems  = $this->aPFBlocks[$iPFBlockID]['Items'];

        // collect inputs
        $aInputs       = array();
        $aInputsSecond = array();

        foreach ($aItems as $aItem) {
            $sItemName = $aItem['Name'];
            $sValue1   = $this->_aProfile[$sItemName];
            $sValue2   = $this->_aCouple[$sItemName];

            if ($aItem['Name'] == 'Age') {
                $sValue1 = $this->_aProfile['DateOfBirth'];
                $sValue2 = $this->_aCouple['DateOfBirth'];
            }

            if ($this->bPFEditable) {
                $aParams = array(
                    'couple' => $this->bCouple,
                    'values' => array(
                        $sValue1,
                        $sValue2
                    ),
                    'profile_id' => $this->_iProfileID,
                );

                $aInputs[] = $this->oPF->convertEditField2Input($aItem, $aParams, 0);

                if ($aItem['Type'] == 'pass') {
                    $aItem_confirm = $aItem;

                    $aItem_confirm['Name'] .= '_confirm';
                    $aItem_confirm['Caption'] = '_Confirm password';
                    $aItem_confirm['Desc']    = '_Confirm password descr';

                    $aInputs[] = $this->oPF->convertEditField2Input($aItem_confirm, $aParams, 0);

                    if ($this->bCouple and !in_array($sItemName, $this->aCoupleMutualItems)) {
                        $aInputsSecond[] = $this->oPF->convertEditField2Input($aItem_confirm, $aInputParams, 1);
                    }
                }

                if ($this->bCouple and !in_array($sItemName, $this->aCoupleMutualItems) and $sValue2) {
                    $aInputsSecond[] = $this->oPF->convertEditField2Input($aItem, $aParams, 1);
                }
            } else {
                if ($sValue1 || $aItem['Type'] == 'bool') { //if empty, do not draw
                    $aInputs[] = array(
                        'type'      => 'value',
                        'name'      => $aItem['Name'],
                        'caption'   => _t($aItem['Caption']),
                        'value'     => $this->oPF->getViewableValue($aItem, $sValue1),
                        'wrap_text' => $aItem['Type'] == 'area',
                    );
                }

                if ($this->bCouple and !in_array($sItemName, $this->aCoupleMutualItems) and ($sValue2 || $aItem['Type'] == 'bool')) {
                    $aInputsSecond[] = array(
                        'type'    => 'value',
                        'name'    => $aItem['Name'],
                        'caption' => _t($aItem['Caption']),
                        'value'   => $this->oPF->getViewableValue($aItem, $sValue2),
                    );
                }
            }
        }

        // merge with couple
        if (!empty($aInputsSecond)) {
            $aHeader1 = array( // wrapper for merging
                array( // input itself
                    'type'    => 'block_header',
                    'caption' => _t('_First Person')
                )
            );

            $aHeader2 = array(
                array(
                    'type'    => 'block_header',
                    'caption' => _t('_Second Person'),
                )
            );

            $aInputs = array_merge($aHeader1, $aInputs, $aHeader2, $aInputsSecond);
        }

        if (empty($aInputs)) {
            return '';
        }

        if ($this->bPFEditable) {
            // add submit button
            $aInputs[] = array(
                'type'    => 'submit',
                'colspan' => 'true',
                'value'   => _t('_Save'),
            );

            // add hidden inputs
            // profile id
            $aInputs[] = array(
                'type'  => 'hidden',
                'name'  => 'ID',
                'value' => $this->_iProfileID,
            );

            $aInputs[] = array(
                'type'  => 'hidden',
                'name'  => 'force_ajax_save',
                'value' => '1',
            );

            $aInputs[] = array(
                'type'  => 'hidden',
                'name'  => 'pf_block',
                'value' => $iPFBlockID,
            );

            $aInputs[] = array(
                'type'  => 'hidden',
                'name'  => 'do_submit',
                'value' => '1',
            );

            $aFormAttrs = array(
                'method'   => 'post',
                'action'   => CH_WSB_URL_ROOT . 'pedit.php',
                'onsubmit' => "submitViewEditForm(this, $iPageBlockID, " . ch_html_attribute($_SERVER['PHP_SELF']) . "'?ID={$this->_iProfileID}'); return false;",
                'name'     => 'edit_profile_form',
            );

            $aFormParams = array();
        } else {
            $aFormAttrs = array(
                'name' => 'view_profile_form',
            );

            $aFormParams = array(
                'remove_form' => true,
            );
        }

        // generate form array
        $aForm = array(
            'form_attrs' => $aFormAttrs,
            'params'     => $aFormParams,
            'inputs'     => $aInputs,
        );

        $oForm = new ChTemplFormView($aForm);

        return $oForm->getCode();
    }

    /**
    ** @description : function will generate profiles's cover
    ** @param  : $sCaption (string) caption of returned block
    ** @param  : $bNoDB (boolean) if isset this param block will return with design box
    ** @return : HTML presentation data
    */
    public function showBlockCover($sCaption, $bNoDB = false)
    {
        global $p_arr;

        $bProfileOwner    = $p_arr['ID'];
        $sProfileNickname = getNickName($p_arr['ID']);

        $sProfileThumbnail     = '';
        $sProfileThumbnail2x   = '';
        $sProfileThumbnailHref = '';

        $bProfileThumbnail     = false;
        $bProfileThumbnailHref = false;

        $iAvatarId = $GLOBALS['MySQL']->getOne("SELECT `Avatar` FROM `Profiles` WHERE `ID` = " . (int)$bProfileOwner);

        if(getParam('sys_member_info_thumb') == 'sys_avatar') {
            if($iAvatarId) {
                $sProfileThumbnail = CH_WSB_URL_ROOT . 'modules/cheetah/avatar/data/images/' . $iAvatarId . 'b.jpg';
                $sProfileThumbnail2x = $sProfileThumbnail;
                $bProfileThumbnail = true;
                $bProfileThumbnailHref = true;
            } else {
                $sProfileThumbnail = '';
                $bProfileThumbnail = false;
                $bProfileThumbnailHref = true;
            }
        } else {
            $aProfileThumbnail = ChWsbService::call('photos', 'profile_photo', array($p_arr['ID'], 'browse', 'full'), 'Search');
            if (!empty($aProfileThumbnail) && is_array($aProfileThumbnail)) {
                $sProfileThumbnail     = $aProfileThumbnail['file_url'];
                $sProfileThumbnailHref = $aProfileThumbnail['view_url'];

                $bProfileThumbnail     = true;
                $bProfileThumbnailHref = true;

                $aProfileThumbnail2x = ChWsbService::call('photos', 'profile_photo', array($p_arr['ID'], 'browse2x', 'full'), 'Search');
                if (!empty($aProfileThumbnail2x) && is_array($aProfileThumbnail2x)) {
                    $sProfileThumbnail2x = $aProfileThumbnail['file_url'];
                }
            }
        }

        if(getParam('sys_member_info_thumb') == 'sys_avatar') {
            $oPermalinks = new ChWsbPermalinks();
            if($p_arr['ID'] == getLoggedId()) {
                $sProfileThumbnailHref = CH_WSB_URL_ROOT . $oPermalinks->permalink('modules/?r=avatar/');
                $bProfileThumbnailHref = true;
            } else {
                $sProfileThumbnailHref = '';
                $bProfileThumbnailHref = false;
            }
        } else {
            if ($bProfileOwner && ChWsbRequest::serviceExists('photos', 'get_manage_profile_photo_url')) {
                $sProfileThumbnailHref = ChWsbService::call('photos', 'get_manage_profile_photo_url', array($p_arr['ID'], 'profile_album_name'));
                $bProfileThumbnailHref = !empty($sProfileThumbnailHref);
            }
        }
        $sProfileCoverHref = '';
        $bProfileCoverHref = false;

        if (ChWsbRequest::serviceExists('photos', 'profile_cover', 'Search')) {
            $sDeleteOrig = getParam('ch_photos_delete_orig');
            if($sDeleteOrig == 'on') {
                $sProfileCoverHref = ChWsbService::call('photos', 'profile_cover', array($p_arr['ID'], 'file'), 'Search');
            } else {
                $sProfileCoverHref = ChWsbService::call('photos', 'profile_cover', array($p_arr['ID'], 'original'), 'Search');
                if(!file_exists($sProfileCoverHref)) {
                    $sProfileCoverHref = ChWsbService::call('photos', 'profile_cover', array($p_arr['ID'], 'file'), 'Search');
                }
            }
            $bProfileCoverHref = !empty($sProfileCoverHref);
        }

        if(empty($sProfileCoverHref)) {
            $sProfileCoverHref = CH_WSB_URL_ROOT . 'media/images/covers/' . mt_rand(1, 10) . '.jpg';
            $bProfileCoverHref = true;
        }

        $sProfileCoverChangeHref = '';
        $bProfileCoverChangeHref = false;
        if ($bProfileOwner && ChWsbRequest::serviceExists('photos', 'get_album_uploader_url')) {
            $sProfileCoverChangeHref = ChWsbService::call('photos', 'get_manage_profile_photo_url', array($p_arr['ID'], 'profile_cover_album_name'));
            $bProfileCoverChangeHref = !empty($sProfileCoverChangeHref);
        }

        //ch_import('ChWsbMemberInfo');
        //$o              = ChWsbMemberInfo::getObjectInstance('sys_status_message');
        //$sProfileStatus = $o ? $o->get($p_arr) : '';
        // Due to problem with user file caching, i pull this from the database until i can find the source of the problem.
        $sProfileStatus = $GLOBALS['MySQL']->getOne("SELECT `UserStatusMessage` FROM `Profiles` WHERE `ID` = '$bProfileOwner'");


        $sBackground      = '';
        $sBackgroundClass = '';
        if ($bProfileCoverHref) {
            $sBackground      = $sProfileCoverHref;
            $sBackgroundClass = ' sys-pcb-cover';
        } elseif ($bProfileThumbnail) {
            $sBackground      = $sProfileThumbnail;
            $sBackgroundClass = ' sys-pcb-thumbnail';
        }

        $aTmplVarsMenu = array();
        // I don't want the menu on the cover, so i commented this one out.
        /*
            	$aMenuItems = $GLOBALS['oTopMenu']->getSubItems();
            	foreach($aMenuItems as $aMenuItem)
                    $aTmplVarsMenu[] = array(
                        'href' => $aMenuItem['Link'],
                        'ch_if:show_onclick' => array(
                            'condition' => !empty($aMenuItem['Onclick']),
                            'content' => array(
                                'onclick' => $aMenuItem['Onclick']
                            )
                        ),
                        'ch_if:show_target' => array(
                            'condition' => !empty($aMenuItem['Target']),
                            'content' => array(
                                'target' => $aMenuItem['Target']
                            )
                        ),
                        'caption' => _t($aMenuItem['Caption'])
                    );
        */
        $sContent = $GLOBALS['oSysTemplate']->parseHtmlByName('profile_cover.html', array(
            'background_class'      => $sBackgroundClass,
            'ch_if:show_background' => array(
                'condition' => !empty($sBackground),
                'content'   => array(
                    'background' => $sBackground
                )
            ),
            'ch_if:show_actions' => array(
                'condition' => $bProfileOwner,
                'content'   => array(
                    'ch_if:show_action_thumbnail' => array(
                        'condition' => $bProfileThumbnailHref,
                        'content'   => array(
                            'href_upload_thumbnail' => $sProfileThumbnailHref
                        ),
                    ),
                    'ch_if:show_action_cover' => array(
                        'condition' => $bProfileCoverChangeHref,
                        'content'   => array(
                            'href_upload' => $sProfileCoverChangeHref,
                        )
                    )
                )
            ),
            'ch_if:show_thumbnail_image' => array(
                'condition' => $bProfileThumbnail && !$bProfileThumbnailHref,
                'content'   => array(
                    'thumbnail'      => $sProfileThumbnail,
                    'thumbnail2x'    => $sProfileThumbnail2x,
                )
            ),
            'ch_if:show_thumbnail_image_link' => array(
                'condition' => $bProfileThumbnail && $bProfileThumbnailHref,
                'content'   => array(
                    'thumbnail_href' => $sProfileThumbnailHref,
                    'thumbnail'      => $sProfileThumbnail,
                    'thumbnail2x'    => $sProfileThumbnail2x,
                )
            ),
            'ch_if:show_thumbnail_letter_text' => array(
                'condition' => !$bProfileThumbnail && !$bProfileThumbnailHref,
                'content'   => array(
                    'letter' => mb_substr($sProfileNickname, 0, 1)
                )
            ),
            'ch_if:show_thumbnail_letter_link' => array(
                'condition' => !$bProfileThumbnail && $bProfileThumbnailHref,
                'content'   => array(
                    'thumbnail_href' => $sProfileThumbnailHref,
                    'letter'         => mb_substr($sProfileNickname, 0, 1)
                )
            ),
            'nickname'             => $sProfileNickname,
            'status'               => $sProfileStatus,
            'ch_repeat:menu_items' => $aTmplVarsMenu,
        ));

        return array($sContent, array(), array(), true);
    }

    /**
    ** @description : function will generate user's actions
    ** @param  : $sCaption (string) caption of returned block
    ** @param  : $bNoDB (boolean) if isset this param block will return with design box
    ** @return : HTML presentation data
    */
    public function showBlockActionsMenu($sCaption, $bNoDB = false)
    {
        global $p_arr;

        /*
        if( (!$iMemberID  or !$iViewedMemberID) or ($iMemberID == $iViewedMemberID) )
            return null;
        */

        $sActions = $GLOBALS['oFunctions']->getProfileViewActions($p_arr['ID'], 'Profile');

        if ($bNoDB) {
            return  $sActions;
        } else {
            echo DesignBoxContent(_t($sCaption), $sActions, 1);
        }
    }

    public function showBlockFriendRequest($sCaption, $bNoDB = false)
    {
        if (!isMember()) {
            return "";
        }
        $aViewer    = getProfileInfo();
        $sQuery     = "SELECT `ID` FROM `sys_friend_list` WHERE `Profile`='" . $aViewer['ID'] . "' AND `Check`=0 LIMIT 1";
        $mixedCheck = $GLOBALS['MySQL']->getOne($sQuery);
        if ((int)$mixedCheck == 0) {
            return "";
        }
        $sContent = _t('_pending_friend_request_answer', CH_WSB_URL_ROOT . "communicator.php?person_switcher=to&communicator_mode=friends_requests");
        $sContent = MsgBox($sContent);
        return array($sContent, array(), array(), false);
    }

    public function showBlockRateProfile($sCaption, $bNoDB = false)
    {
        $votes = getParam('votes');

        // Check if profile votes enabled
        if (!$votes || !$this->oVotingView->isEnabled() || isBlocked($this -> _iProfileID, getLoggedId())) {
            return;
        }

        $ret = $this->oVotingView->getBigVoting();
        $ret = $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array('content' => $ret));

        if ($bNoDB) {
            return $ret;
        } else {
            echo DesignBoxContent(_t($sCaption), $ret, 1);
        }
    }

    public function showBlockCmts()
    {
        if (!$this->oCmtsView->isEnabled() || isBlocked($this -> _iProfileID, getLoggedId())) {
            return '';
        }
        return $this->oCmtsView->getCommentsFirst();
    }

    public function showBlockFriends($sCaption, $oParent, $bNoDB = false)
    {
        $iLimit = $this->iFriendsPerPage;

        $sAllFriends  = 'viewFriends.php?iUser=' .  $this -> _iProfileID;
        $sProfileLink = getProfileLink($this -> _iProfileID);

        // count all friends ;
        $iCount = getFriendNumber($this->_iProfileID);

        $sPaginate = '';
        if ($iCount) {
            $iPages = ceil($iCount / $iLimit);
            $iPage  = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;

            if ($iPage < 1) {
                $iPage = 1;
            }
            if ($iPage > $iPages) {
                $iPage = $iPages;
            }

            $sqlFrom = ($iPage - 1) * $iLimit;
            if ($sqlFrom < 1) {
                $sqlFrom = 0;
            }
            $sqlLimit = "LIMIT {$sqlFrom}, {$iLimit}";
        } else {
            return ;
        }

        $aAllFriends = getMyFriendsEx($this->_iProfileID, '', 'image', $sqlLimit);
        $iCurrCount  = count($aAllFriends);

        $aTmplVars = array(
            'ch_repeat:friends' => array()
        );
        foreach ($aAllFriends as $iFriendID => $aFriendsPrm) {
            $aTmplVars['ch_repeat:friends'][] = array(
                'content' => get_member_thumbnail($iFriendID, 'none', true, 'visitor', array('is_online' => $aFriendsPrm[5]))
            );
        }
        $sOutputHtml = $GLOBALS['oSysTemplate']->parseHtmlByName('profile_friends.html', $aTmplVars);

        $oPaginate = new ChWsbPaginate(array(
            'page_url'       => CH_WSB_URL_ROOT . 'profile.php',
            'count'          => $iCount,
            'per_page'       => $iLimit,
            'page'           => $iPage,
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' .  $sProfileLink. '?page={page}&per_page={per_page}\');',
        ));

        $sPaginate = $oPaginate->getSimplePaginate($sAllFriends);
        return array( $sOutputHtml, array(), $sPaginate, true);
    }

    public function showBlockMutualFriends($sCaption, $bNoDB = false)
    {
        $iViewer = getLoggedId();
        if ($this->_iProfileID == $iViewer) {
            return;
        }
        if ($this->iCountMutFriends > 0) {
            $sCode = $sPaginate = '';

            $iPerPage = $this->iFriendsPerPage;
            $iPage    = (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;

            $aTmplVars = array(
                'ch_repeat:friends' => array()
            );
            foreach ($this->aMutualFriends as $iKey => $sValue) {
                $aTmplVars['ch_repeat:friends'][] = array(
                    'content' => get_member_thumbnail($iKey, 'none', true)
                );
            }
            $sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('profile_friends.html', $aTmplVars);

            if ($this->iCountMutFriends > $iPerPage) {
                $oPaginate = new ChWsbPaginate(array(
                    'page_url'       => CH_WSB_URL_ROOT . 'profile.php',
                    'count'          => $this->iCountMutFriends,
                    'per_page'       => $iPerPage,
                    'page'           => $iPage,
                    'on_change_page' => 'return !loadDynamicBlock({id}, \'' .  getProfileLink($this->_iProfileID). '?page={page}&per_page={per_page}\');',
                ));
                $sPaginate = $oPaginate->getSimplePaginate('', -1, -1, false);
            }

            if ($bNoDB) {
                return array($sCode, array(), $sPaginate, true);
            } else {
                return DesignBoxContent(_t($sCaption), $sCode, 1);
            }
        }
    }

    public function CountMutualFriends($iViewer)
    {
        return getMutualFriendsCount($this->_iProfileID, $iViewer);
    }

    public function FindMutualFriends($iViewer, $iPage = 1, $iPerPage = 14)
    {
        $iViewer                = (int)$iViewer;
        $this->iCountMutFriends = $this->CountMutualFriends($iViewer);
        if ($this->iCountMutFriends > 0) {
            $iPage    = $iPage    > 0 ? (int)$iPage : 1;
            $iPerPage = $iPerPage > 0 ? (int)$iPerPage : $this->iFriendsPerPage;
            $sLimit   = "LIMIT " . ($iPage - 1) * $iPerPage . ", $iPerPage";

            $sQuery = "
            SELECT p.ID AS `friendID` , p.NickName
            FROM `Profiles` AS p
            INNER JOIN (SELECT `ID` AS `ID`, `When` FROM `sys_friend_list` WHERE `Profile` = '{$this->_iProfileID}' AND `Check` =1
                UNION SELECT `Profile` AS `ID`, `When` FROM `sys_friend_list` WHERE `ID` = '{$this->_iProfileID}' AND `Check` =1) AS `f1`
                ON (`f1`.`ID` = `p`.`ID`)
            INNER JOIN (SELECT `ID` AS `ID`, `When` FROM `sys_friend_list` WHERE `Profile` = '{$iViewer}' AND `Check` =1
                UNION SELECT `Profile` AS `ID`, `When` FROM `sys_friend_list` WHERE `ID` = '{$iViewer}' AND `Check` =1) AS `f2`
                ON (`f2`.`ID` = `p`.`ID`)
            ORDER BY p.`Avatar` DESC
            $sLimit
            ";

            $vResult = db_res($sQuery);
            while ($aRow = $vResult ->fetch()) {
                $this->aMutualFriends[ $aRow['friendID'] ] = $aRow['NickName'];
            }
        }
    }

    public function GenSqlConditions(&$aSearchBlocks, &$aRequestParams, $aFilterSortSettings = array())
    {
        $aWhere         = array();
        $sJoin          = '';
        $sPossibleOrder = '';

        // --- cut 1
        //collect where request array
        foreach ($aSearchBlocks as $iBlockID => $aBlock) {
            foreach ($aBlock['Items'] as $aItem) {
                if (!isset($aRequestParams[ $aItem['Name'] ])) {
                    continue;
                }

                $sItemName = $aItem['Name'];
                $mValue    = $aRequestParams[$sItemName];

                switch ($aItem['Type']) {
                    case 'text':
                    case 'area':
                        if ($sItemName == 'Tags') {
                            $sJoin .= " INNER JOIN `sys_tags` ON (`sys_tags`.`Type` = 'profile' AND `sys_tags`.`ObjID` = `Profiles`.`ID`) ";
                            $aWhere[] = "`sys_tags`.`Tag` = '" . process_db_input($mValue, CH_TAGS_STRIP) . "'";
                        } else {
                            $aWhere[] = "`Profiles`.`$sItemName` LIKE '%" . process_db_input($mValue, CH_TAGS_STRIP) . "%'";
                        }
                    break;

                    case 'num':
                        $mValue[0] = (int)$mValue[0];
                        $mValue[1] = (int)$mValue[1];
                        $aWhere[]  = "`Profiles`.`$sItemName` >= {$mValue[0]} AND `Profiles`.`$sItemName` <= {$mValue[1]}";
                    break;

                    case 'date':
                        $iMin = floor($mValue[0] * 365.25); //for leap years
                        $iMax = floor($mValue[1] * 365.25);

                        $aWhere[] = "DATEDIFF( NOW(), `Profiles`.`$sItemName` ) >= $iMin AND DATEDIFF( NOW(), `Profiles`.`$sItemName` ) <= $iMax"; // TODO: optimize it, move static sql part to the right part and leave db field only in the left part

                        //$aWhere[] = "DATE_ADD( `$sItemName`, INTERVAL {$mValue[0]} YEAR ) <= NOW() AND DATE_ADD( `$sItemName`, INTERVAL {$mValue[1]} YEAR ) >= NOW()"; //is it correct statement?
                    break;

                    case 'select_one':
                        if (is_array($mValue)) {
                            $sValue   = implode(',', $mValue);
                            $aWhere[] = "FIND_IN_SET( `Profiles`.`$sItemName`, '" . process_db_input($sValue, CH_TAGS_STRIP) . "' )";
                        } else {
                            $aWhere[] = "`Profiles`.`$sItemName` = '" . process_db_input($mValue, CH_TAGS_STRIP) . "'";
                        }
                    break;

                    case 'select_set':
                        $aSet = array();

                        $aMyValues = is_array($mValue) ? $mValue : array($mValue);

                        foreach ($aMyValues as $sValue) {
                            $sValue = process_db_input($sValue, CH_TAGS_STRIP);
                            $aSet[] = "FIND_IN_SET( '$sValue', `Profiles`.`$sItemName` )";
                        }

                        $aWhere[] = '( ' . implode(' OR ', $aSet) . ' )';
                    break;

                    case 'range':
                        //impl
                    break;

                    case 'bool':
                        $aWhere[] = "`Profiles`.`$sItemName`";
                    break;

                    case 'system':
                        switch ($aItem['Name']) {
                            case 'Couple':
                                if ($mValue == '-1') {
                                } elseif ($mValue) {
                                    $aWhere[] = "`Profiles`.`Couple` > `Profiles`.`ID`";
                                } else {
                                    $aWhere[] = "`Profiles`.`Couple` = 0";
                                }
                            break;

                            case 'Keyword':
                            case 'Location':
                                $aFields = explode("\n", $aItem['Extra']);
                                $aKeyw   = array();
                                $sValue  = process_db_input($mValue, CH_TAGS_STRIP);

                                foreach ($aFields as $sField) {
                                    $aKeyw[] = "`Profiles`.`$sField` LIKE '%$sValue%'";
                                }

                                $aWhere[] = '( ' . implode(' OR ', $aKeyw) . ')';
                            break;

                            case 'ID':
                                $aWhere[] = "`ID` = $mValue";
                            break;
                        }
                    break;
                }
            }
        }

        // --- cut 2

        if (getParam("ch_zip_enabled") == "on" && $aRequestParams['distance'] > 0) {
            ChWsbService::call('zipcodesearch', 'get_sql_parts', array($_REQUEST['Country'], $_REQUEST['zip'], $_REQUEST['metric'], $_REQUEST['distance'], &$sJoin, &$aWhere));
        }

        // --- cut 3

        // collect query string
        $aWhere[] = "`Profiles`.`Status` = 'Active'";

        // add online only
        if ($_REQUEST['online_only']) {
            $iOnlineTime = (int)getParam('member_online_time');
            $aWhere[]    = "`DateLastNav` >= DATE_SUB(NOW(), INTERVAL $iOnlineTime MINUTE)";
        }

        // --- cut 4

        $sPossibleOrder = '';
        switch ($_REQUEST['show']) {
            case 'featured':
                $aWhere[] = "`Profiles`.`Featured` = '1'";
                break;
            case 'birthdays':
                $aWhere[] = "MONTH(`DateOfBirth`) = MONTH(CURDATE()) AND DAY(`DateOfBirth`) = DAY(CURDATE())";
                break;
            case 'top_rated':
                $sPossibleOrder = ' ORDER BY `Profiles`.`Rate` DESC, `Profiles`.`RateCount` DESC';
                break;
            case 'popular':
                $sPossibleOrder = ' ORDER BY `Profiles`.`Views` DESC';
                break;
            case 'moderators':
                $sJoin .= " INNER JOIN `" . DB_PREFIX . "ChatProfiles` ON `Profiles`.`ID`= `" . DB_PREFIX . "ChatProfiles`.`ID` ";
                $aWhere[] = "`" . DB_PREFIX . "ChatProfiles`.`Type`='moder'";
                break;
        }

        switch ($aFilterSortSettings['sort']) {
            case 'activity':
                $sPossibleOrder = ' ORDER BY `Profiles`.`DateLastNav` DESC';
                break;
            case 'date_reg':
                $sPossibleOrder = ' ORDER BY `Profiles`.`DateReg` DESC';
                break;
            case 'rate':
                $sPossibleOrder = ' ORDER BY `Profiles`.`Rate` DESC, `Profiles`.`RateCount` DESC';
                break;
            default:
                break;
        }

        // --- cut 5
        if ($_REQUEST['photos_only']) {
            $aWhere[] = "`Profiles`.`Avatar`";
        }

        $aWhere[] = "(`Profiles`.`Couple`='0' OR `Profiles`.`Couple`>`Profiles`.`ID`)";

        return array($aWhere, $sJoin, $sPossibleOrder);
    }

    public function GenSearchResultBlock($aSearchBlocks, $aRequestParams, $aFilterSortSettings = array(), $sPgnRoot = 'profile.php')
    {
        if (empty($aSearchBlocks)) { // the request is empty. do not search.
            return array('', array(), '', '');
        }

        // status uptimization
        $iOnlineTime  = (int)getParam("member_online_time");
        $sIsOnlineSQL = ", if(`DateLastNav` > SUBDATE(NOW(), INTERVAL {$iOnlineTime} MINUTE ), 1, 0) AS `is_online`";

        $sQuery    = 'SELECT DISTINCT SQL_CALC_FOUND_ROWS IF( `Profiles`.`Couple`=0, `Profiles`.`ID`, IF( `Profiles`.`Couple`>`Profiles`.`ID`, `Profiles`.`ID`, `Profiles`.`Couple` ) ) AS `ID` ' . $sIsOnlineSQL . ' FROM `Profiles` ';
        $sQueryCnt = 'SELECT COUNT(DISTINCT IF( `Profiles`.`Couple`=0, `Profiles`.`ID`, IF( `Profiles`.`Couple`>`Profiles`.`ID`, `Profiles`.`ID`, `Profiles`.`Couple` ) )) AS "Cnt" FROM `Profiles` ';

        list($aWhere, $sJoin, $sPossibleOrder) = $this->GenSqlConditions($aSearchBlocks, $aRequestParams, $aFilterSortSettings);

        $sWhere = ' WHERE ' . implode(' AND ', $aWhere);

        //collect the whole query string
        $sQuery    = $sQuery . $sJoin . $sWhere . $sPossibleOrder;
        $sQueryCnt = $sQueryCnt . $sJoin . $sWhere . $sPossibleOrder;

        //echo $sQuery;

        $iCountProfiles = (int)(db_value($sQueryCnt));

        $sResults = $sTopFilter = '';
        if ($iCountProfiles) {
            //collect pagination
            $iCurrentPage    = isset($_GET['page']) ? (int)$_GET['page']         : 1;
            $iResultsPerPage = isset($_GET['res_per_page']) ? (int)$_GET['res_per_page'] : 10;

            if ($iCurrentPage < 1) {
                $iCurrentPage = 1;
            }
            if ($iResultsPerPage < 1) {
                $iResultsPerPage = 10;
            }

            $iTotalPages = ceil($iCountProfiles / $iResultsPerPage);

            if ($iTotalPages > 1) {
                if ($iCurrentPage > $iTotalPages) {
                    $iCurrentPage = $iTotalPages;
                }

                $sLimitFrom = ($iCurrentPage - 1) * $iResultsPerPage;
                $sQuery .= " LIMIT {$sLimitFrom}, {$iResultsPerPage}";

                list($sPagination, $sTopFilter) = $this->genSearchPagination($iCountProfiles, $iCurrentPage, $iResultsPerPage, $aFilterSortSettings, $sPgnRoot);
            } else {
                $sPagination = '';
            }

            //make search
            $aProfiles        = array();
            $aProfileStatuses = array();
            $rProfiles        = db_res($sQuery);
            while ($aProfile = $rProfiles->fetch()) {
                $aProfiles[]                       = $aProfile['ID'];
                $aProfileStatuses[$aProfile['ID']] = $aProfile['is_online'];
            }

            $sOutputMode = (isset($_REQUEST['search_result_mode']) && $_REQUEST['search_result_mode'] == 'ext') ? 'ext' : 'sim';

            $aDBTopMenu = array();
            foreach (array( 'sim', 'ext' ) as $myMode) {
                switch ($myMode) {
                    case 'sim':
                        $modeTitle = _t('_Simple');
                    break;
                    case 'ext':
                        $modeTitle = _t('_Extended');
                    break;
                }

                $aGetParams = $_GET;
                unset($aGetParams['search_result_mode']);
                $sRequestString         = $this->collectRequestString($aGetParams);
                $aDBTopMenu[$modeTitle] = array('href' => ch_html_attribute($_SERVER['PHP_SELF']) . "?search_result_mode={$myMode}{$sRequestString}", 'dynamic' => false, 'active' => ($myMode == $sOutputMode));
            }

            if ($sOutputMode == 'sim') {
                $sBlockWidthSQL  = "SELECT `PageWidth`, `ColWidth` FROM `sys_page_compose` WHERE `Page`='profile' AND `Func`='ProfileSearch'";
                $aBlockWidthInfo = db_arr($sBlockWidthSQL);

                $iBlockWidth = (int)((int)$aBlockWidthInfo['PageWidth'] /* * (int)$aBlockWidthInfo['ColWidth'] / 100*/) - 20;

                $iMaxThumbWidth = getParam('max_thumb_width') + 6;

                $iDestWidth = $iCountProfiles * ($iMaxThumbWidth + 6);

                if ($iDestWidth > $iBlockWidth) {
                    $iMaxAllowed = (int)floor($iBlockWidth / ($iMaxThumbWidth + 6));
                    $iDestWidth  = $iMaxAllowed * ($iMaxThumbWidth + 6);
                }
            }
            $sWidthCent = ($iDestWidth > 0) ? "width:{$iDestWidth}px;" : '';

            $sResults .= '<div class="block_rel_100 ch-def-bc-margin' . ($sOutputMode == 'sim' ? '-thd' : '') . '">';

            //output search results
            require_once(CH_DIRECTORY_PATH_ROOT . 'templates/tmpl_'.$GLOBALS['tmpl'].'/scripts/ChTemplSearchProfile.php');
            $oChTemplSearchProfile = new ChTemplSearchProfile();
            $iCounter              = 0;

            foreach ($aProfiles as $iProfID) {
                $aProfileInfo = getProfileInfo($iProfID);

                //attaching status value
                $aProfileStatus = array(
                    'is_online' => $aProfileStatuses[$iProfID]
                );
                $aProfileInfo = array_merge($aProfileStatus, $aProfileInfo);

                $sResults .= $oChTemplSearchProfile->displaySearchUnit($aProfileInfo);
                $iCounter++;
            }

            $sResults .= <<<EOF
                    <div id="ajaxy_popup_result_div" style="display: none;"></div>
                    <div class="clear_both"></div>
                </div>
EOF;

            return array($sResults, $aDBTopMenu, $sPagination, $sTopFilter);
        } else {
            return array(MsgBox(_t('_Empty')), array(), '', '');
        }
    }

    public function GenProfilesCalendarBlock()
    {
        ch_import('ChWsbProfilesCalendar');

        $aDateParams = array();
        $sDate       = $_REQUEST['date'];
        if ($sDate) {
            $aDateParams = explode('/', $sDate);
        }
        $oCalendar = new ChWsbProfilesCalendar((int)$aDateParams[0], (int)$aDateParams[1], $this);

        $sOutputMode = (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'dob') ? 'dob' : 'dor';
        $aDBTopMenu  = array();
        foreach (array( 'dob', 'dor' ) as $myMode) {
            switch ($myMode) {
                case 'dob':
                    if ($sOutputMode == $myMode) {
                        $oCalendar->setMode('dob');
                    }
                    $modeTitle = _t('Date of birth');
                break;
                case 'dor':
                    $modeTitle = _t('Date of registration');
                break;
            }

            $aGetParams = $_GET;
            unset($aGetParams['mode']);
            $sRequestString         = $this->collectRequestString($aGetParams);
            $aDBTopMenu[$modeTitle] = array('href' => ch_html_attribute($_SERVER['PHP_SELF']) . "?mode={$myMode}{$sRequestString}", 'dynamic' => true, 'active' => ($myMode == $sOutputMode));
        }

        //return $oCalendar->display();
        return array( $oCalendar->display(), $aDBTopMenu );
    }

    public function genSearchPagination($iCountProfiles, $iCurrentPage, $iResultsPerPage, $aFilterSortSettings = array(), $sPgnRoot = '')
    {
        $aGetParams = $_GET;
        unset($aGetParams['page']);
        unset($aGetParams['res_per_page']);
        unset($aGetParams['sort']);

        $sRequestString = $this->collectRequestString($aGetParams);
        $sRequestString = CH_WSB_URL_ROOT . strip_tags($sPgnRoot) . '?' . substr($sRequestString, 1);

        $sPaginTmpl = $sRequestString . '&res_per_page={per_page}&page={page}&sort={sorting}';

        // gen pagination block ;

        $oPaginate = new ChWsbPaginate(
            array(
                'page_url' => $sPaginTmpl,
                'count'    => $iCountProfiles,
                'per_page' => $iResultsPerPage,
                'sorting'  => $aFilterSortSettings['sort'], // New param
                'page'     => $iCurrentPage,
            )
        );

        $sPagination = $oPaginate->getPaginate();

        // fill array with sorting params
        $aSortingParam = array(
            'none'     => _t('_None'),
            'activity' => _t('_Latest activity'),
            'date_reg' => _t('_FieldCaption_DateReg_View'),
        );
        if (getParam('votes')) {
            $aSortingParam['rate'] = _t('_Rate');
        }

        // gen sorting block ( type of : drop down ) ;
        $sSortBlock   = $oPaginate->getSorting($aSortingParam);
        $sSortElement = '<div class="ordered_block">' . $sSortBlock . '</div><div class="clear_both"></div>';
        $sSortElement = $GLOBALS['oSysTemplate']->parseHtmlByName('designbox_top_controls.html', array(
            'top_controls' => $sSortElement
        ));

        return array($sPagination, $sSortElement);
    }

    public function collectRequestString($aGetParams, $sKeyPref = '', $sKeyPostf = '')
    {
        if (!is_array($aGetParams)) {
            return '';
        }

        $sRet = '';
        foreach ($aGetParams as $sKey => $sValue) {
            if ($sValue === '') {
                continue;
            }

            if (!is_array($sValue)) {
                $sRet .= '&' . urlencode($sKeyPref . $sKey . $sKeyPostf) . '=' . urlencode(process_pass_data($sValue));
            } else {
                $sRet .= $this->collectRequestString($sValue, "{$sKeyPref}{$sKey}{$sKeyPostf}[", "]"); //recursive call
            }
        }

        return $sRet;
    }

    public function GenActionsMenuBlock()
    {
        // init some user's values
        $p_arr = $this->_aProfile;

        $iMemberID = getLoggedId();

        $iViewedMemberID = (int)$p_arr['ID'];

        if ((!$iMemberID or !$iViewedMemberID) or ($iMemberID == $iViewedMemberID)) {
            return null;
        }

        // prepare all nedded keys
        $p_arr['url']           = CH_WSB_URL_ROOT;
        $p_arr['window_width']  = $this->oTemplConfig->popUpWindowWidth;
        $p_arr['window_height'] = $this->oTemplConfig->popUpWindowHeight;
        $p_arr['anonym_mode']   = $this->oTemplConfig->bAnonymousMode;
        $p_arr['member_id']     = $iMemberID;
        $p_arr['member_pass']   = getPassword($iMemberID);

        $sActions = $GLOBALS['oFunctions']->genObjectsActions($p_arr, 'Profile', 'cellpadding="0" cellspacing="0"');

        return  $sActions;
    }
}
