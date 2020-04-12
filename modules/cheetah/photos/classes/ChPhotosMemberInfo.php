<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbMemberInfo');

/**
 * Member info objects.
 */
class ChPhotosMemberInfo extends ChWsbMemberInfo
{
    /**
     * Constructor
     * @param $aObject array of member info options
     */
    public function __construct($aObject)
    {
        parent::__construct($aObject);
    }

    /**
     * Get member avatar from profile photos
     */
    public function get ($aData)
    {
        switch ($this->_sObject) {
        	case 'ch_photos_thumb_2x':
	            return ChWsbService::call('photos', 'profile_photo', array($aData['ID'], 'browse'), 'Search');

	        case 'ch_photos_thumb':
	        case 'ch_photos_icon_2x':
	            return ChWsbService::call('photos', 'profile_photo', array($aData['ID'], 'thumb'), 'Search');

	        case 'ch_photos_icon':
	            return ChWsbService::call('photos', 'profile_photo', array($aData['ID'], 'icon'), 'Search');
        }

        return parent::get($aData);
    }

    public function isAvatarSearchAllowed ()
    {
        return false;
    }

    public function isSetAvatarFromDefaultAlbumOnly ()
    {
        return true;
    }
}
