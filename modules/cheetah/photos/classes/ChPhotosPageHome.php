<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbFilesPageHome');

class ChPhotosPageHome extends ChWsbFilesPageHome
{
    function __construct (&$oShared)
    {
        parent::__construct($oShared);
    }

    function getBlockCode_Cover ()
    {
    	$bUseFeatured = $this->oConfig->getGlParam('cover_featured') == 'on';

    	$iRows = (int)$this->oConfig->getGlParam('cover_rows');
    	$iColumns = (int)$this->oConfig->getGlParam('cover_columns');
    	$iExcess = 20;

    	$iCountRequired = $iRows * $iColumns + $iExcess;
    	$this->oSearch->clearFilters(array('activeStatus', 'allow_view', 'album_status', 'albumType', 'ownerStatus'), array('albumsObjects', 'albums'));
    	if($bUseFeatured)
	    	$this->oSearch->aCurrent['restriction']['featured'] = array(
	            'field' => 'Featured',
	            'value' => '1',
	            'operator' => '=',
	            'param' => 'featured'
	        );
    	$this->oSearch->aCurrent['paginate']['perPage'] = $iCountRequired;
        $aFiles = $this->oSearch->getSearchData();
        if(empty($aFiles))
        	return '';

        $iCount = count($aFiles);
        if($iCount < $iCountRequired)
        	while($iCount < $iCountRequired) {
        		$aFiles = array_merge($aFiles, $aFiles);
        		$iCount = count($aFiles);
        	}

		$sViewUrl = CH_WSB_URL_ROOT . $this->oModule->_oConfig->getBaseUri() . 'view/';

        $aTmplVarsImages = array();
        foreach($aFiles as $aFile)
        	$aTmplVarsImages[] = array(
        		'src' => $this->oSearch->getImgUrl($aFile['Hash'], 'browse'),
        		'link' => $sViewUrl . $aFile['uri'],
        		'title' => ch_html_attribute($aFile['title'])
        	);

		$this->oTemplate->addCss(array('cover.css'));
        $this->oTemplate->addJs(array('modernizr.js', 'jquery.gridrotator.js'));
        return $this->oTemplate->parseHtmlByName('cover.html', array(
        	'loading' => $GLOBALS['oFunctions']->loadingBoxInline(),
        	'ch_repeat:images' => $aTmplVarsImages,
        	'rows' => $iRows,
        	'columns' => $iColumns
        ));
    }

    function getBlockCode_LatestFile ()
    {
		return $this->oSearch->getFeaturedPhotoBlock();
    }
}
