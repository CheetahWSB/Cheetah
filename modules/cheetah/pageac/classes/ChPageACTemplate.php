<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModuleTemplate');
ch_import('ChTemplFormView');

class ChPageACTemplate extends ChWsbModuleTemplate
{
    /**
     * Constructor
     */
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);
    }

    function getTabs()
    {
        $sBaseUrl = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'action_get_page_';

        $this->addAdminCss(array('tabs.css', 'admin.css', 'forms_adv.css'));
        $this->addAdminJs(array('jquery.ui.core.min.js', 'jquery.ui.widget.min.js', 'jquery.ui.tabs.min.js', 'main.js'));

        $aTabs = array(
            'ch_repeat:page_tabs' => array(
                array(
                    'page_url' => $sBaseUrl.'rules',
                    'page_name' => _t('_ch_pageac_rules_page')
                ),
                array(
                    'page_url' => $sBaseUrl.'top_menu',
                    'page_name' => _t('_ch_pageac_topmenu_page')
                ),
                array(
                    'page_url' => $sBaseUrl.'member_menu',
                    'page_name' => _t('_ch_pageac_membermenu_page')
                ),
                array(
                    'page_url' => $sBaseUrl.'page_blocks',
                    'page_name' => _t('_ch_pageac_page_blocks_page')
                )
            ),
            'base_url' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri()
        );
        return $this->parseHtmlByName('tabs.html', $aTabs);
    }

    function displayRulesList($aRules)
    {
        $aResult['ch_if:rules_not_exist'] = array(
            'condition' => count($aRules) == 0,
            'content' => array(
                'no_rules' => MsgBox(_t('_ch_pageac_no_rules_admin'))
            )
        );

        $aResult['ch_if:rules_exist'] = array(
            'condition' => count($aRules) > 0,
            'content' => array()
        );

        $aRulesList = array();
        if (count($aRules) > 0)
        foreach ($aRules as $aRule) {
            $aForbiddenGroups = array();
            foreach ($this->_oConfig->_aMemberships as $iMemLevelID => $sMemLevelName) {
                $aForbiddenGroups[] = array(
                    'checked' => $aRule['MemLevels'][$iMemLevelID] ? 'checked="checked"' : '',
                    'rule_id' => $aRule['ID'],
                    'memlevel_id' => $iMemLevelID,
                    'memlevel_name' => $sMemLevelName
                );
            }

            $aRulesList[] = array(
                'rule_id' => $aRule['ID'],
                'rule_text' => htmlentities($aRule['Rule']),
                'ch_repeat:forbidden_groups' => $aForbiddenGroups
            );
        }

        $aResult['ch_if:rules_exist']['content']['ch_repeat:rules'] = $aRulesList;

        return  $this->parseHtmlByName('rules_list.html', $aResult);
    }

    function displayNewRuleForm()
    {
        $sBaseUrl = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri();

        $this->_aNewRuleForm = array(
            'form_attrs' => array(
                'id' => 'new_rule_form',
                'name' => 'new_rule_form',
                'action' => $sBaseUrl . 'action_new_rule/',
                'method' => 'post',
                'onsubmit' => 'oChPageACMain.addNewRule(this); return false;'
            ),
            'inputs' => array (
                'rule_text' => array(
                    'type' => 'text',
                    'name' => 'rule',
                    'caption' => _t('_ch_pageac_page_url'),
                    'info' => _t('_ch_pageac_page_url_descr'),
                ),
                'rule_advanced' => array(
                    'type' => 'checkbox',
                    'name' => 'advanced',
                    'caption' => _t('_ch_pageac_advanced'),
                    'info' => _t('_ch_pageac_advanced_descr'),
                ),
                'rule_access' => array(
                    'type' => 'checkbox_set',
                    'name' => 'memlevels',
                    'caption' => _t('_ch_pageac_forbidden_groups'),
                    'value' => array_keys($this->_oConfig->_aMemberships),
                    'values' => $this->_oConfig->_aMemberships
                ),
                'rule_submit' => array(
                    'type' => 'submit',
                    'name' => 'add_rule',
                    'value' => _t('_ch_pageac_add_rule')
                )
            )
        );

        $oForm = new ChTemplFormView($this->_aNewRuleForm);
        return $oForm->getCode();
    }

    function displayTopMenuCompose($aTopMenuArray)
    {
        $aTopItems = array();
        foreach ($aTopMenuArray['TopItems'] as $iItemID => $sItemName) {
            $aCustomItems = array();
            foreach ($aTopMenuArray['CustomItems'][$iItemID] as $iCustomItemID => $sCustomItemName) {
                $aCustomItems[] = array(
                    'custom_item_id' => $iCustomItemID,
                    'custom_item_caption' => $sCustomItemName
                );
            }
            $aTopItems[] = array(
                'item_id' => $iItemID,
                'item_caption' => $sItemName,
                'ch_repeat:custom_items' => $aCustomItems
            );
        }

        $aSystemItems = array();
        foreach ($aTopMenuArray['SystemItems'] as $iItemID => $sItemName) {
            $aCustomItems = array();
            foreach ($aTopMenuArray['CustomItems'][$iItemID] as $iCustomItemID => $sCustomItemName) {
                $aCustomItems[] = array(
                    'custom_item_id' => $iCustomItemID,
                    'custom_item_caption' => $sCustomItemName
                );
            }
            $aSystemItems[] = array(
                'item_id' => $iItemID,
                'item_caption' => $sItemName,
                'ch_repeat:custom_items' => $aCustomItems
            );
        }

        $aResult = array(
            'parser_url' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri().'action_top_menu/',
            'ch_repeat:top_items' => $aTopItems,
            'ch_repeat:system_items' => $aSystemItems,
        );

        return  $this->parseHtmlByName('top_menu_table.html', $aResult);
    }
    function displayMemberMenuCompose($aItemsArray)
    {
        $aItems = array();
        foreach ($aItemsArray as $iItemID => $sItemName) {
            $aItems[] = array(
                'item_id' => $iItemID,
                'item_caption' => $sItemName
            );
        }

        $aResult = array(
            'parser_url' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri().'action_member_menu/',
            'ch_repeat:top_items' => $aItems,
        );

        return  $this->parseHtmlByName('member_menu_table.html', $aResult);
    }
    function getMenuItemEditForm($sMenuType, $iMenuItem, $aMenuItemVisibility)
    {
        $sBaseUrl = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri();

        $aMemLevelValues = array();
        $aMemLevelCheckedValues = array();
        $aMemLevelValues[-1] = _t('_ch_pageac_visible_for_all');
        if (empty($aMenuItemVisibility)) $aMemLevelCheckedValues[] = -1;
        foreach ($this->_oConfig->_aMemberships as $iID => $sName) {
            if ($iID == 1) continue;
            $aMemLevelValues[$iID] = $sName;
            if ($aMenuItemVisibility[$iID] || empty($aMenuItemVisibility)) $aMemLevelCheckedValues[] = $iID;
        }

        $aMenuItemEditForm = array(
            'form_attrs' => array(
                'id' => 'item_edit',
                'name' => 'item_edit',
                'action' => $sBaseUrl . 'action_'.$sMenuType.'_menu/save/'.$iMenuItem,
                'method' => 'post',
                'onsubmit' => 'oChPageACMain.saveItem(this); return false;'
            ),
            'inputs' => array (
                'mlv_visible_to' => array(
                    'type' => 'checkbox_set',
                    'caption' => _t('_ch_pageac_visible_for'),
                    'name' => 'mlv_visible_to',
                    'value' => $aMemLevelCheckedValues,
                    'values' => $aMemLevelValues
                ),
                'submit' => array(
                    'type' => 'submit',
                    'name' => 'add_rule',
                    'value' => _t('_Save Changes')
                )
            )
        );

        $oForm = new ChTemplFormView($aMenuItemEditForm);
        return $oForm->getCode();
    }
    function getPageBlockEditForm($iMenuItem, $aMenuItemVisibility)
    {
        $sBaseUrl = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri();

        $aMemLevelValues = array();
        $aMemLevelCheckedValues = array();
        $aMemLevelValues[-1] = _t('_ch_pageac_visible_for_all');
        if (empty($aMenuItemVisibility)) $aMemLevelCheckedValues[] = -1;
        foreach ($this->_oConfig->_aMemberships as $iID => $sName) {
            if ($iID == 1) continue;
            $aMemLevelValues[$iID] = $sName;
            if ($aMenuItemVisibility[$iID] || empty($aMenuItemVisibility)) $aMemLevelCheckedValues[] = $iID;
        }

        $aMenuItemEditForm = array(
            'form_attrs' => array(
                'id' => 'item_edit',
                'name' => 'item_edit',
                'action' => $sBaseUrl . 'action_page_block/save/'.$iMenuItem,
                'method' => 'post',
                'onsubmit' => 'oChPageACMain.saveItem(this); return false;'
            ),
            'inputs' => array (
                'mlv_visible_to' => array(
                    'type' => 'checkbox_set',
                    'caption' => _t('_ch_pageac_visible_for'),
                    'name' => 'mlv_visible_to',
                    'value' => $aMemLevelCheckedValues,
                    'values' => $aMemLevelValues
                ),
                'submit' => array(
                    'type' => 'submit',
                    'name' => 'add_rule',
                    'value' => _t('_Save Changes')
                )
            )
        );

        $oForm = new ChTemplFormView($aMenuItemEditForm);
        return $oForm->getCode();
    }

    function _getAvailablePages($aPages)
    {
        $aPagesTempl = array();
        foreach ($aPages as $aPage) {
            $sTitle = htmlspecialchars( $aPage['Title'] ? $aPage['Title'] : $aPage['Name'] );
            $aPagesTempl[] = array(
                'page_name' => htmlspecialchars_adv($aPage['Name']),
                'page_caption' => $sTitle,
            );
        }

        $aResult = array(
            'update_url' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri().'action_get_page_page_blocks/',
            'ch_repeat:pages' => $aPagesTempl
        );
        return  $this->parseHtmlByName('page_builder_main.html', $aResult);
    }
    function _getPageBlocks($aColumns)
    {
        $sParseUrl = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri().'action_page_block/edit/';

        $aResult = array(
            'ch_repeat:pages' => $aPagesTempl
        );

        foreach ($aColumns as $iColumn => $aBlocks) {
            $aBlocksTmpl = array();
            foreach ($aBlocks as $aBlock) {
                $aBlocksTmpl[] = array(
                    'block_caption' => _t( $aBlock['Caption'] ),
                    'edit_block_url' => $sParseUrl.$aBlock['ID']
                );
            }
            $aResult['ch_repeat:columns'][] = array(
                'column' => $iColumn,
                'ch_repeat:blocks' => $aBlocksTmpl
            );
        }

        return  $this->parseHtmlByName('page_builder_blocks.html', $aResult);
    }
}
