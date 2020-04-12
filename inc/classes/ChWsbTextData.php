<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

define('CH_TD_VIEWER_TYPE_VISITOR', 0);
define('CH_TD_VIEWER_TYPE_MEMBER', 1);
define('CH_TD_VIEWER_TYPE_ADMIN', 2);

define('CH_TD_STATUS_ACTIVE', 0);
define('CH_TD_STATUS_INACTIVE', 1);
define('CH_TD_STATUS_PENDING', 2);

ch_import('ChWsbForm');
ch_import('ChWsbPrivacy');
ch_import('ChWsbCategories');
ch_import('ChTemplFormView');
ch_import('ChWsbCategories');

class ChWsbTextData
{
    var $_oModule;
    var $_aForm;
    var $_bComments;
    var $_iOwnerId;

    function __construct(&$oModule)
    {
        $this->_oModule = $oModule;

        $this->_iOwnerId = ChWsbTextData::getAuthorId();
        $oCategories = new ChWsbCategories();
        $oCategories->getTagObjectConfig();

        $this->_aForm = array(
            'form_attrs' => array(
                'id' => 'text_data',
                'name' => 'text_data',
                'action' => ch_html_attribute($_SERVER['PHP_SELF']),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            ),
            'params' => array (
                'db' => array(
                    'table' => '',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'caption',
                    'submit_name' => 'post'
                ),
            ),
            'inputs' => array (
                'author_id' => array(
                    'type' => 'hidden',
                    'name' => 'author_id',
                    'value' => $this->_iOwnerId,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ),
                'caption' => array(
                    'type' => 'text',
                    'name' => 'caption',
                    'caption' => _t("_td_caption"),
                    'value' => '',
                    'required' => 1,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,64),
                        'error' => _t('_td_err_incorrect_length'),
                    ),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),
                'snippet' => array(
                    'type' => 'textarea',
                    'html' => 0,
                    'name' => 'snippet',
                    'caption' => _t("_td_snippet"),
                    'value' => '',
                    'required' => 1,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,200),
                        'error' => _t('_td_err_incorrect_length'),
                    ),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),
                'content' => array(
                    'type' => 'textarea',
                    'html' => 2,
                    'name' => 'content',
                    'caption' => _t("_td_content"),
                    'value' => '',
                    'required' => 1,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,65536),
                        'error' => _t('_td_err_incorrect_length'),
                    ),
                    'db' => array (
                        'pass' => 'XssHtml',
                    ),
                ),
                'when' => array(
                    'type' => 'datetime',
                    'name' => 'when',
                    'caption' => _t("_td_date"),
                    'value' => date('Y-m-d H:i'),
                    'required' => 1,
                    'checker' => array (
                        'func' => 'DateTime',
                        'error' => _t('_td_err_empty_value'),
                    ),
                    'db' => array (
                        'pass' => 'DateTime',
                    ),
                ),
                'tags' => array(
                    'type' => 'text',
                    'name' => 'tags',
                    'caption' => _t("_td_tags"),
                    'value' => '',
                    'required' => 1,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,64),
                        'error' => _t('_td_err_incorrect_length'),
                    ),
                    'info' => _t('_sys_tags_note'),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),
                'categories' => $oCategories->getGroupChooser($this->_oModule->_oConfig->getCategoriesSystemName(), $this->_iOwnerId, true),
                'allow_comment_to' => array(),
                'allow_vote_to' => array(),
                'post' => array(
                    'type' => 'submit',
                    'name' => 'post',
                    'value' => _t("_td_post"),
                ),
            )
        );

        if(!empty($this->_iOwnerId)) {
            $oPrivacy = new ChWsbPrivacy();
            $sModuleUri = $this->_oModule->_oConfig->getUri();

            $this->_aForm['inputs']['allow_comment_to'] = $oPrivacy->getGroupChooser($this->_iOwnerId, $sModuleUri, 'comment');
            $this->_aForm['inputs']['allow_vote_to'] = $oPrivacy->getGroupChooser($this->_iOwnerId, $sModuleUri, 'vote');
        }
    }

    function getPostForm($aAddFields = array())
    {
        $oForm = new ChTemplFormView($this->_aForm);
        $oForm->initChecker();

        if($oForm->isSubmittedAndValid()) {
            $iDateNow = time();
            $iDatePublish = $oForm->getCleanValue('when');
            if($iDatePublish > $iDateNow)
                $iStatus = CH_TD_STATUS_PENDING;
            else if($iDatePublish <= $iDateNow && $this->_oModule->_oConfig->isAutoapprove())
                $iStatus = CH_TD_STATUS_ACTIVE;
            else
                $iStatus = CH_TD_STATUS_INACTIVE;

            $aDefFields = array(
                'uri' => $oForm->generateUri(),
                'date' => $iDateNow,
                'status' => $iStatus
            );
            $iId = $oForm->insert(array_merge($aDefFields, $aAddFields));

            //--- 'System' -> Post for Alerts Engine ---//
            ch_import('ChWsbAlerts');
            $oAlert = new ChWsbAlerts($this->_oModule->_oConfig->getAlertsSystemName(), 'post', $iId, $this->_iOwnerId);
            $oAlert->alert();
            //--- 'System' -> Post for Alerts Engine ---//

            //--- Reparse Global Tags ---//
            $oTags = new ChWsbTags();
            $oTags->reparseObjTags($this->_oModule->_oConfig->getTagsSystemName(), $iId);
            //--- Reparse Global Tags ---//

            //--- Reparse Global Categories ---//
            $oCategories = new ChWsbCategories();
            $oCategories->reparseObjTags($this->_oModule->_oConfig->getCategoriesSystemName(), $iId);
            //--- Reparse Global Categories ---//

            header('Location: ' . $oForm->aFormAttrs['action']);
        } else
            return $oForm->getCode();
    }

    function getEditForm($aValues, $aAddFields = array())
    {
        $oCategories = new ChWsbCategories();
        if (isset($this->_aForm['inputs']['categories'])) {
            //--- convert post form to edit one ---//
            $this->_aForm['inputs']['categories'] = $oCategories->getGroupChooser($this->_oModule->_oConfig->getCategoriesSystemName(), $this->_iOwnerId, true, $aValues['categories']);
        }
        if(!empty($aValues) && is_array($aValues)) {

            foreach($aValues as $sKey => $sValue)
                if(array_key_exists($sKey, $this->_aForm['inputs'])) {
                    if($this->_aForm['inputs'][$sKey]['type'] == 'checkbox')
                        $this->_aForm['inputs'][$sKey]['checked'] = (int)$sValue == 1 ? true : false;
                    else if($this->_aForm['inputs'][$sKey]['type'] == 'select_box' && $this->_aForm['inputs'][$sKey]['name'] == 'Categories') {
                        $aCategories = preg_split( '/['.$oCategories->sTagsDivider.']/', $sValue, 0, PREG_SPLIT_NO_EMPTY );
                        $this->_aForm['inputs'][$sKey]['value'] = $aCategories;
                    } else
                        $this->_aForm['inputs'][$sKey]['value'] = $sValue;
                }
            unset( $this->_aForm['inputs']['author_id']);
            $this->_aForm['inputs']['id'] = array(
                'type' => 'hidden',
                'name' => 'id',
                'value' => $aValues['id'],
                'db' => array (
                    'pass' => 'Int',
                )
            );
            $this->_aForm['inputs']['post']['value'] = _t("_td_edit");
        }
        $oForm = new ChTemplFormView($this->_aForm);
        $oForm->initChecker();

        if($oForm->isSubmittedAndValid()) {
            $iDateNow = time();
            $iDatePublish = $oForm->getCleanValue('when');
            if($iDatePublish > $iDateNow)
                $iStatus = CH_TD_STATUS_PENDING;
            else if($iDatePublish <= $iDateNow && $this->_oModule->_oConfig->isAutoapprove())
                $iStatus = CH_TD_STATUS_ACTIVE;
            else
                $iStatus = CH_TD_STATUS_INACTIVE;

            $aDefFields = array(
                'date' => $iDateNow,
                'status' => $iStatus
            );
            $oForm->update($aValues['id'], array_merge($aDefFields, $aAddFields));

            //--- 'System' -> Edit for Alerts Engine ---//
            ch_import('ChWsbAlerts');
            $oAlert = new ChWsbAlerts($this->_oModule->_oConfig->getAlertsSystemName(), 'edit', $aValues['id'], $this->_iOwnerId);
            $oAlert->alert();
            //--- 'System' -> Edit for Alerts Engine ---//

            //--- Reparse Global Tags ---//
            $oTags = new ChWsbTags();
            $oTags->reparseObjTags($this->_oModule->_oConfig->getTagsSystemName(), $aValues['id']);
            //--- Reparse Global Tags ---//

            //--- Reparse Global Categories ---//
            $oCategories->reparseObjTags($this->_oModule->_oConfig->getCategoriesSystemName(), $aValues['id']);
            //--- Reparse Global Categories ---//

            header('Location: ' . $oForm->aFormAttrs['action']);
        } else
            return $oForm->getCode();
    }

    function getViewerType()
    {
        $iViewerType = CH_TD_VIEWER_TYPE_VISITOR;
        if(isAdmin())
            $iViewerType = CH_TD_VIEWER_TYPE_ADMIN;
        else if(isMember())
            $iViewerType = CH_TD_VIEWER_TYPE_MEMBER;

        return $iViewerType;
    }

    public static function getAuthorId()
    {
        return getLoggedId();
    }

    function getAuthorPassword()
    {
        return getLoggedPassword();
    }
}
