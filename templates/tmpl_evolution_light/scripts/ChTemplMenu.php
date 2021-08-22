<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChBaseMenu');

/**
 * @see ChBaseMenu;
 */
class ChTemplMenu extends ChBaseMenu
{
    var $aProfileOwnerSubmenu;

    /**
     * Class constructor;
     */
    function __construct()
    {
        parent::__construct();
    }

    /*
    * Generate navigation menu source
    */
    function getCode()
    {
        global $oSysTemplate;

        if(isset($GLOBALS['ch_profiler']))
            $GLOBALS['ch_profiler']->beginMenu('Main Menu');

        $this->getMenuInfo();

        //--- Main Menu ---//
        $t = $this->iElementsCntInLine;
        $sMainMenu = '';
        for($x = $t; $x > 0; $x--) {
          $this->iElementsCntInLine = $x;
          $sMainMenu .= $this->genTopItemsEvolution($x);
        }

        //--- Submenu Menu ---//
        $sSubMenu = '';
        if(!defined('CH_INDEX_PAGE') && !defined('CH_JOIN_PAGE'))
            $sSubMenu = $this->genSubMenus();

        $sResult = $oSysTemplate->parseHtmlByName('navigation_menu.html', array(
            'cur_menu' => $t,
            'last_menu' => 1,
            'main_menu' => $sMainMenu,
            'sub_menu' => $sSubMenu
        ));

        if(isset($GLOBALS['ch_profiler']))
            $GLOBALS['ch_profiler']->endMenu('Main Menu');

        return $sResult;
    }

    /*
    * Generate top menu elements
    */
    function genTopItemsEvolution($x, $aParams = array())
    {
      $t = (int)getParam('nav_menu_elements_on_line_' . (isLogged() ? 'usr' : 'gst'));
      if($x == $t) {
        $sDisplay = '';
      } else {
        $sDisplay = 'display: none;';
      }
    	$bWrap = isset($aParams['wrap']) ? (bool)$aParams['wrap'] : true;
    	$bGroupInMore = isset($aParams['group_in_more']) ? (bool)$aParams['group_in_more'] : $this->bGroupInMore;

        $iCounter = 0;
        foreach( $this->aTopMenu as $iItemID => $aItem ) {
            if( $aItem['Type'] != 'top' )
                continue;
            if( !$this->checkToShow( $aItem ) )
                continue;
            if ($aItem['Caption'] == "{profileNick}" && $this->aMenuInfo['profileNick']=='') continue;

            $bActive = ( $iItemID == $this->aMenuInfo['currentTop'] );

            if ($bActive && $bGroupInMore && $iCounter >= $this->iElementsCntInLine) {
                $this->iJumpedMenuID = $iItemID;
                break;
            }
            $iCounter++;
        }

        $sCode = '';
        $iCounter = 0;
        foreach( $this->aTopMenu as $iItemID => $aItem ) {
            if( $aItem['Type'] != 'top' )
                continue;

            if( !$this->checkToShow( $aItem ) )
                continue;

            //generate
            list( $aItem['Link'] ) = explode( '|', $aItem['Link'] );

            $aItem['Caption'] = $this->replaceMetas( $aItem['Caption'] );
            $aItem['Link'] = $this->replaceMetas( $aItem['Link'] );
            $aItem['Onclick'] = $this->replaceMetas( $aItem['Onclick'] );

            $bActive = ( $iItemID == $this->aMenuInfo['currentTop'] );
            $bActive = ($aItem['Link']=='index.php' && $this->aMenuInfo['currentTop']==0) ? true : $bActive;

            if ($this->bDebugMode)
            	print $iItemID . $aItem['Caption'] . '__' . $aItem['Link'] . '__' . $bActive . '<br />';

            $isBold = false;
            $sImage = ($aItem['Icon'] != '') ? $aItem['Icon'] : $aItem['Picture'];

            //Draw jumped element
            if ($this->iJumpedMenuID > 0 && $bGroupInMore && $iCounter == $this->iElementsCntInLine) {
                $aItemJmp = $this->aTopMenu[$this->iJumpedMenuID];
                list( $aItemJmp['Link'] ) = explode( '|', $aItemJmp['Link'] );
                $aItemJmp['Link']    = $this->replaceMetas( $aItemJmp['Link'] );
                $aItemJmp['Onclick'] = $this->replaceMetas( $aItemJmp['Onclick'] );

                $bJumpActive = ( $this->iJumpedMenuID == $this->aMenuInfo['currentTop'] );
                $bJumpActive = ($aItemJmp['Link']=='index.php' && $this->aMenuInfo['currentTop']==0) ? true : $bJumpActive;

                $sCode .= $this->genTopItem(_t($aItemJmp['Caption']), $aItemJmp['Link'], $aItemJmp['Target'], $aItemJmp['Onclick'], $bJumpActive, $this->iJumpedMenuID, $isBold);

                if ($this->bDebugMode)
                	print '<br />pre_pop: ' . $this->iJumpedMenuID . $aItemJmp['Caption'] . '__' . $aItemJmp['Link'] . '__' . $bJumpActive . '<br /><br />';
            }

            if ($bGroupInMore && $iCounter == $this->iElementsCntInLine) {
                $sCode .= $this->GenMoreElementBegin();

                $sCode .= '<td><div id="ch_more_menu" class="ch_more_menu">';

                if ($this->bDebugMode)
                	print '<br />more begin here ' . '<br /><br />';
            }

			if($this->iJumpedMenuID == 0 || $iItemID != $this->iJumpedMenuID) {
				if ($bGroupInMore && $this->iElementsCntInLine <= $iCounter)
					$sCode .= '<ul class="sub">' . $this->genTopItemMore(_t($aItem['Caption']), $aItem['Link'], $aItem['Target'], $aItem['Onclick'], $bActive, $iItemID) . '</ul>';
				else
					$sCode .= $this->genTopItem(_t($aItem['Caption']), $aItem['Link'], $aItem['Target'], $aItem['Onclick'], $bActive, $iItemID, $isBold, $sImage);
			}

            $iCounter++;
        }

        if($bGroupInMore && $this->iElementsCntInLine < $iCounter)
            //$sCode .= $this->GenMoreElementEnd();
            $sCode .= '</div></td>';

		if(!$bWrap)
			return $sCode;

        return $GLOBALS['oSysTemplate']->parseHtmlByName('navigation_menu_main.html', array(
          'id' => $x,
          //'display' => $sDisplay,
          'display' => 'display: none;',
        	'main_menu' => $sCode
        ));
    }


    /*
    * Generate sub menu elements
    */
    function genSubMenus()
    {
        foreach( $this->aTopMenu as $iTItemID => $aTItem ) {
            if( $aTItem['Type'] != 'top' && $aTItem['Type'] !='system')
                continue;

            if( !$this->checkToShow( $aTItem ) )
                continue;

            if( $this->aMenuInfo['currentTop'] == $iTItemID && $this->checkShowCurSub() )
                $sDisplay = 'block';
            else {
                $sDisplay = 'none';
                if ($aTItem['Caption']=='_Home' && $this->aMenuInfo['currentTop']==0)
                    $sDisplay = 'block';
            }

            $sCaption = _t( $aTItem['Caption'] );
            $sCaption = $this->replaceMetas($sCaption);

            //generate
            if ($sDisplay == 'block') {
                $sPicture = $aTItem['Picture'];

                $iFirstID = $this->genSubFirstItem( $iTItemID );
                $this->genSubHeader( $iTItemID, $iFirstID, $sCaption, $sDisplay, $sPicture );
            }
        }

        return $GLOBALS['oSysTemplate']->parseHtmlByName('navigation_menu_sub.html', array(
        	'sub_menu' => $this->sCode
        ));
    }


    function genTopSubitems($iItemID)
    {
        return '';
    }

    function genSubItems($iTItemID = 0)
    {
        $sSubItems = parent::genSubItems($iTItemID);
        if (empty($sSubItems)) {
            return '';
        }

        $iSelected = (int)$this->aMenuInfo['currentCustom'] > 0 ? (int)$this->aMenuInfo['currentCustom'] : $this->getSubItemFirst($this->aMenuInfo['currentTop']);
        $aSelected = $this->aTopMenu[$iSelected];

        return $GLOBALS['oSysTemplate']->parseHtmlByName('navigation_menu_sub_header_submenu.html', array(
            'link'    => $this->replaceMetas($aSelected['Link']),
            'onclick' => 'javascript:return oChEvolutionLightTopMenu.showSubmenuSubmenu(this);',
            'caption' => _t($aSelected['Caption']),
            'submenu' => $sSubItems
        ));
    }

    function getSubItemFirst($iTItemID = 0)
    {

        $iResult = 0;
        foreach ($this->aTopMenu as $iItemID => $aItem) {
            if ($aItem['Type'] != 'custom') {
                continue;
            }
            if ($aItem['Parent'] != $iTItemID) {
                continue;
            }
            if (!$this->checkToShow($aItem)) {
                continue;
            }

            $iResult = $iItemID;
            break;
        }

        return $iResult;

    }

	/*
    * Generate header for sub items of sub menu elements
    */
    function genSubHeader( $iTItemID, $iFirstID, $sCaption, $sDisplay, $sPicture = '' )
    {

        $this->sCustomActions .= $GLOBALS['oSysTemplate']->parseHtmlByName('action_link_submenu_share.html', array(
    		'popup' => $GLOBALS['oFunctions']->transBox(
    			$GLOBALS['oSysTemplate']->parseHtmlByName('share_popup.html', array())
    		)
    	));

        parent::genSubHeader($iTItemID, $iFirstID, $sCaption, $sDisplay, $sPicture);

    }

    function genSubHeaderCaption($aItem, $sCaption, $sTemplateFile = 'navigation_menu_sub_header_caption.html')
    {
        return '';
    }

    /*
    * Generate top menu elements
    */
    function genTopItem($sText, $sLink, $sTarget, $sOnclick, $bActive, $iItemID, $isBold = false, $sPicture = '')
    {
    	$sLink = (strpos($sLink, 'http://') === false && strpos($sLink, 'https://') === false && strpos($sLink, 'javascript') === false && !strlen($sOnclick)) ? $this->sSiteUrl . $sLink : $sLink;

        return $GLOBALS['oSysTemplate']->parseHtmlByName('navigation_menu_mm_item.html', array(
        	'link' => $sLink,
          'more_marker' => '',
        	'ch_if:show_active' => array(
        		'condition' => $bActive,
        		'content' => array()
        	),
        	'ch_if:show_onclick' => array(
        		'condition' => !$bActive && $sOnclick,
        		'content' => array(
        			'onclick' => $sOnclick
        		)
        	),
        	'ch_if:show_target' => array(
        		'condition' => !$bActive && $sTarget,
        		'content' => array(
        			'target' => $sTarget
        		)
        	),
        	'ch_if:show_style' => array(
        		'condition' => $isBold,
        		'content' => array(
        			'style' => 'font-weight:bold;'
        		)
        	),
        	'ch_if:show_picture' => array(
        		'condition' => $sText == '' && $isBold && $sPicture != '',
        		'content' => array(
        			'src' => getTemplateIcon($sPicture)
        		)
        	),
        	'text' => $sText,
        	'sub_menus' => $this->genTopSubitems($iItemID)
        ));
    }

    function GenMoreElementBegin()
    {
        return $GLOBALS['oSysTemplate']->parseHtmlByName('navigation_menu_mm_item.html', array(
            'link'               => '#',
            'more_marker' => ' db-more-marker',
            'ch_if:show_active'  => array(
                'condition' => false,
                'content'   => array()
            ),
            'ch_if:show_onclick' => array(
                'condition' => true,
                'content'   => array(
                    'onclick' => "toggleMore(); return false;"
                )
            ),
            'ch_if:show_target'  => array(
                'condition' => false,
                'content'   => array()
            ),
            'ch_if:show_style'   => array(
                'condition' => false,
                'content'   => array()
            ),
            'ch_if:show_picture' => array(
                'condition' => false,
                'content'   => array()
            ),
            'text'               => '<i id="morebars" class="morebars img_submenu sys-icon bars"></i><i id="moreclose" class="moreclose img_submenu sys-icon close" style="display: none;"></i>',
            'sub_menus'          => ''
        ));
    }

    function genTopItemMore($sText, $sLink, $sTarget, $sOnclick, $bActive, $iItemID)
    {
    	if(strpos($sLink, 'http://') === false && strpos($sLink, 'https://') === false && !strlen($sOnclick))
    		$sLink = $this->sSiteUrl . $sLink;

        $sSubMenus = $this->getAllSubMenus($iItemID);

        return $GLOBALS['oSysTemplate']->parseHtmlByName('navigation_menu_mm_more_subitem.html', array(
        	'wrapper_class' => $bActive ? 'active' : '',
        	'item_class' => $bActive ? ' active' : '',
        	'link' => $sLink,
        	'ch_if:show_onclick' => array(
        		'condition' => strlen($sOnclick) > 0,
        		'content' => array(
        			'onclick' => $sOnclick
        		)
        	),
        	'ch_if:show_target' => array(
        		'condition' => strlen($sTarget) > 0,
        		'content' => array(
        			'target' => $sTarget
        		)
        	),
        	'text' => $sText,
        	'ch_if:show_submenus' => array(
        		'condition' => !empty($sSubMenus),
        		'content' => array(
        			'sub_menus' => $sSubMenus
        		)
        	)
        ));
    }

    function GenMoreElementEnd()
    {
        return "";
    }
}

// Creating template navigation menu class instance
$oTopMenu = new ChTemplMenu();
