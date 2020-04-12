<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPrivacy');

class ChStorePrivacyFile extends ChWsbPrivacy
{
    /**
     * Constructor
     */
    function __construct(&$oModule)
    {
        parent::__construct($oModule->_oDb->getPrefix() . 'product_files', 'id', 'author_id');
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
        if (preg_match('/^m(\d+)$/', $mixedGroupId, $m)) {
            $iMembershipId = $m[1];
            require_once(CH_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
            $aMembershipInfo = getMemberMembershipInfo($iViewerId);

            return $iMembershipId == $aMembershipInfo['ID'] && (!$aMembershipInfo['DateStarts'] || $aMembershipInfo['DateStarts'] < time()) && (!$aMembershipInfo['DateExpires'] || $aMembershipInfo['DateExpires'] > time()) ? true : false;
        }
        return false;
    }

}
