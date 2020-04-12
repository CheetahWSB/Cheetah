<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChAdsPageHome extends ChWsbPageView
{
    var $oModule;
    function __construct($oModule)
    {
        parent::__construct('ads_home');
        $this->oModule = $oModule;
    }

    function getBlockCode_last()
    {
        return $this->oModule->GenAllAds('last', true);
    }

    function getBlockCode_featured()
    {
        ch_import('SearchUnit', $this->oModule->_aModule);
        $oTmpAdsSearch = new ChAdsSearchUnit();
        $oTmpAdsSearch->sSelectedUnit = 'ad_of_day';
        $oTmpAdsSearch->aCurrent['paginate']['forcePage'] = 1;
        $oTmpAdsSearch->aCurrent['paginate']['perPage'] = 1;
        $oTmpAdsSearch->aCurrent['restriction']['featuredStatus']['value'] = 1;
        $sTopAdOfAllDayValue = $oTmpAdsSearch->displayResultBlock();
        return $oTmpAdsSearch->aCurrent['paginate']['totalNum'] > 0 ? $sTopAdOfAllDayValue : '';
    }

    function getBlockCode_categories()
    {
        return $this->oModule->genCategoriesBlock();
    }
}
