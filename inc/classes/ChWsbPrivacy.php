<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPrivacyQuery');

define('CH_WSB_PG_DEFAULT', '1');
define('CH_WSB_PG_NOBODY', '2');
define('CH_WSB_PG_ALL', '3');
define('CH_WSB_PG_MEMBERS', '4');
define('CH_WSB_PG_FRIENDS', '5');
define('CH_WSB_PG_FAVES', '6');
define('CH_WSB_PG_CONTACTS', '7');
define('CH_WSB_PG_HIDDEN', '8');

/**
 * Privacy settings for any content.
 *
 * Integration of the content with privacy engine allows site member
 * to organize the access to his content.
 *
 * Related classes:
 *  ChWsbPrivacyQuery - database queries.
 *  ChWsbPrivacySearch - organize members search using necessary criteria.
 *  ChWsbPrivacyView - super class for all representations.
 *  ChBasePrivacyView - base template representation.
 *  ChTemplPrivacyView - custom template representation.
 *
 * Example of usage:
 * 1. Register your privacy actions in `sys_privacy_actions` database table.
 * 2. Add one privacy field(with INT type) in the table with your items for each action.
 *    For example, for action 'comment', the field name should be 'allow_comment_to'.
 * 3. Add group choosers for necessary actions in the form, which is used to add new items.
 *
 *    $oPrivacy = new ChWsbPrivacy();
 *    $oPrivacy->getGroupChooser($iItemOwnerId, $sModuleUri, $sModuleAction);
 *
 * 4. Check privacy when any user tries to view an item.
 *
 *    $oPrivacy = new ChWsbPrivacy($sTable, $sFieldId, $sFieldOwnerId);
 *    if($oPrivacy->check($sAction, $iObjectId, $iViewerId)) {
 *     //show necessary content
 *    }
 *
 *    @see an example of integration in the default Cheetah's modules(feedback, events, sites, etc)
 *
 *
 * Memberships/ACL:
 * Doesn't depend on user's membership.
 *
 *
 * Alerts:
 * no alerts available
 *
 */
class ChWsbPrivacy
{
    var $_oDb;

    /**
     * constructor
     */
    function __construct($sTable = '', $sFieldId = '', $sFieldOwnerId = '')
    {
        $this->_oDb = new ChWsbPrivacyQuery($sTable, $sFieldId, $sFieldOwnerId);
    }

    /**
     * Get Select element with available groups.
     *
     * @param  integer $iOwnerId       object's owner ID.
     * @param  string  $sModuleUri     module's unique URI.
     * @param  string  $sActionName    action name.
     * @param  array   $aDynamicGroups an array of array('key' => group_id, 'value' => group_title).
     * @param  string  $sTitle         the title to be used for generated field.
     * @return an      array with Select element description.
     */
    function getGroupChooser($iOwnerId, $sModuleUri, $sActionName, $aDynamicGroups = array(), $sTitle = "")
    {
        if(empty($sActionName))
            return array();

        $sValue = $this->_oDb->getDefaultValue($iOwnerId, $sModuleUri, $sActionName);

        if(empty($sValue))
            $sValue = $this->_oDb->getDefaultValueModule($sModuleUri, $sActionName);

        $aValues = array();
        $aGroups = $this->_oDb->getGroupsBy(array('type' => 'owner', 'owner_id' => $iOwnerId, 'full' => true));
        foreach($aGroups as $aGroup) {
            if((int)$aGroup['owner_id'] == 0 && $this->_oDb->getParam('sys_ps_enabled_group_' . $aGroup['id']) != 'on')
               continue;

            $aValues[] = array('key' => $aGroup['id'], 'value' => ((int)$aGroup['owner_id'] == 0 ? _t('_ps_group_' . $aGroup['id'] . '_title') : $aGroup['title']));
        }
        $aValues = array_merge($aValues, $aDynamicGroups);

        $sName = $this->getFieldAction($sActionName);
        $sCaption = $this->_oDb->getFieldActionTitle($sModuleUri, $sActionName);
        return array(
            'type' => 'select',
            'name' => $sName,
            'caption' => (!empty($sTitle) ? $sTitle : _t(!empty($sCaption) ? $sCaption : '_' . $sName)),
            'value' => $sValue,
            'values' => $aValues,
            'checker' => array(
                'func' => 'avail',
                'error' => _t('_ps_ferr_incorrect_select')
            ),
            'db' => array(
                'pass' => 'Int'
            )
        );
    }

    /**
     * Check whether the viewer can make requested action.
     *
     * @param  string  $sAction   action name from 'sys_priacy_actions' table.
     * @param  integer $iObjectId object ID the action to be performed with.
     * @param  integer $iViewerId viewer ID.
     * @return boolean result of operation.
     */
    function check($sAction, $iObjectId, $iViewerId = 0)
    {
        if(empty($iViewerId))
            $iViewerId = getLoggedId();

        $aObject = $this->_oDb->getObjectInfo($this->getFieldAction($sAction), $iObjectId);
        if(empty($aObject) || !is_array($aObject))
            return false;

        if($aObject['group_id'] == CH_WSB_PG_HIDDEN)
            return false;

        if(isAdmin() || $iViewerId == $aObject['owner_id'])
            return true;

        if($this->_oDb->isGroupMember($aObject['group_id'], $aObject['owner_id'], $iViewerId))
            return true;

        return $this->isDynamicGroupMember($aObject['group_id'], $aObject['owner_id'], $iViewerId, $iObjectId);
    }

    /**
     * Get database field name for action.
     *
     * @param  string $sAction action name.
     * @return string with field name.
     */
    function getFieldAction($sAction)
    {
        return 'allow_' . strtolower(str_replace(' ', '-', $sAction)) . '_to';
    }

    /**
     * Check whethere viewer is a member of dynamic group.
     *
     * @param  mixed   $mixedGroupId   dynamic group ID.
     * @param  integer $iObjectOwnerId object owner ID.
     * @param  integer $iViewerId      viewer ID.
     * @return boolean result of operation.
     */
    function isDynamicGroupMember($mixedGroupId, $iObjectOwnerId, $iViewerId, $iObjectId)
    {
        return false;
    }

    /**
     * Static Method.
     * Check whether Privacy Group page/menu is available.
     */
    public static function isPrivacyPage()
    {
        return getParam('sys_ps_enable_create_group') == 'on' || getParam('sys_ps_enable_default_values') == 'on' || getParam('sys_ps_enabled_group_1') == 'on';
    }
}
