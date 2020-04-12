<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbMemberInfoQuery');

/**
 * @page objects
 * @section member_info Member Info
 * @ref ChWsbMemberInfo
 */

/**
 * Member info objects.
 */
class ChWsbMemberInfo
{
    protected $_sObject;
    protected $_aObject;

    /**
     * Constructor
     * @param $aObject array of member info options
     */
    public function __construct($aObject)
    {
        $this->_sObject = $aObject['object'];
        $this->_aObject = $aObject;
    }

    /**
     * Get object instance by object name
     * @param $sObject object name
     * @return object instance or false on error
     */
    static public function getObjectInstance($sObject)
    {
        if (isset($GLOBALS['chWsbClasses']['ChWsbMemberInfo!'.$sObject]))
            return $GLOBALS['chWsbClasses']['ChWsbMemberInfo!'.$sObject];

        $aObject = ChWsbMemberInfoQuery::getMemberInfoObject($sObject);
        if (!$aObject || !is_array($aObject))
            return false;

        $sClass = 'ChWsbMemberInfo';
        if (!empty($aObject['override_class_name'])) {
            $sClass = $aObject['override_class_name'];
            if (!empty($aObject['override_class_file']))
                require_once(CH_DIRECTORY_PATH_ROOT . $aObject['override_class_file']);
            else
                ch_import($sClass);
        }

        $o = new $sClass($aObject);

        return ($GLOBALS['chWsbClasses']['ChWsbMemberInfo!'.$sObject] = $o);
    }

    /**
     * Get member info
     */
    public function get ($aData)
    {
        switch ($this->_sObject) {
	        case 'sys_username':
	            return $aData['NickName'];

	        case 'sys_full_name':
	            return htmlspecialchars_adv($aData['FullName'] ? $aData['FullName'] : $aData['NickName']);

            case 'sys_first_name':
                return $aData['FirstName'] ? $aData['FirstName'] : $aData['NickName'];

            case 'sys_first_name_last_name':
                return $aData['FirstName'] || $aData['LastName'] ? $aData['FirstName'] . ' ' . $aData['LastName'] : $aData['NickName'];

            case 'sys_last_name_firs_name':
                return $aData['FirstName'] || $aData['LastName'] ? $aData['LastName'] . ' ' . $aData['FirstName'] : $aData['NickName'];

	        case 'sys_status_message':
	            return $aData['UserStatusMessage'];

	        case 'sys_age_sex':
	            $s = ('0000-00-00' == $aData['DateOfBirth'] ? '' :  _t('_y/o', age($aData['DateOfBirth']))) . (empty($aData['Sex']) ? '' : ' ' . _t('_' . $aData['Sex']));
	            if ($aData['Couple'] > 0) {
	                $aData2 = getProfileInfo($aData['Couple']);
	                $s .= '<br />' . ('0000-00-00' == $aData2['DateOfBirth'] ? '' :  _t('_y/o', age($aData2['DateOfBirth']))) . (empty($aData2['Sex']) ? '' : ' ' . _t('_' . $aData2['Sex']));
	            }
	            return $s;

	        case 'sys_location':
	            return (empty($aData['City']) ? '' : htmlspecialchars_adv($aData['City']) . ', ') . _t($GLOBALS['aPreValues']['Country'][$aData['Country']]['LKey']);

			case 'sys_avatar_2x':
	            if (!$aData || !@include_once (CH_DIRECTORY_PATH_MODULES . 'cheetah/avatar/include.php'))
	                return false;
	            return $aData['Avatar'] ? CH_AVA_URL_USER_AVATARS . $aData['Avatar'] . 'b' . CH_AVA_EXT : '';

	        case 'sys_avatar':
	        case 'sys_avatar_icon_2x':
	            if (!$aData || !@include_once (CH_DIRECTORY_PATH_MODULES . 'cheetah/avatar/include.php'))
	                return false;
	            return $aData['Avatar'] ? CH_AVA_URL_USER_AVATARS . $aData['Avatar'] . CH_AVA_EXT : '';

	        case 'sys_avatar_icon':
	            if (!$aData || !@include_once (CH_DIRECTORY_PATH_MODULES . 'cheetah/avatar/include.php'))
	                return false;
	            return $aData['Avatar'] ? CH_AVA_URL_USER_AVATARS . $aData['Avatar'] . 'i' . CH_AVA_EXT : '';
        }
    }

    public function isAvatarSearchAllowed ()
    {
        return true;
    }

    public function isSetAvatarFromDefaultAlbumOnly ()
    {
        return false;
    }

    public function getMemberNameFields ()
    {
        switch ($this->_sObject) {
            default:
	        case 'sys_username':
	            return array('NickName');

	        case 'sys_full_name':
	            return array('FullName', 'NickName');

            case 'sys_first_name':
                return array('FirstName', 'NickName');

            case 'sys_last_name_firs_name':
            case 'sys_first_name_last_name':
                return array('FirstName', 'LastName', 'NickName');
        }
    }
}
