<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbMailBox');

/**
 * Admin dashboard content.
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
class ChWsbAdminDashboard
{
    private $aBlocks;

    /**
     * constructor
     */
    function __construct()
    {
        if (isset($_POST['hide_admin_help']) && $_POST['hide_admin_help']) {
            setParam('sys_show_admin_help', '');
            echo '1';
            exit;
        }

        $this->aBlocks = array(
            'help' => 'on' == getParam('sys_show_admin_help') ? true : false,
        	'links' => true,
            'charts' => true,
            'stats' => true,
        );
    }

    function getCode()
    {
        $sContent = '';

        foreach($this->aBlocks as $sName => $bActive) {
            if(!$bActive)
                continue;

            $sMethod = 'getCode' . str_replace(' ', '', ucwords(str_replace('_', ' ', $sName)));
            if(!method_exists($this, $sMethod))
                continue;

            $sContent .= $this->$sMethod();
        }

        return $sContent;
    }
    function getCodeLinks()
    {
    	$sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('dashboard_links.html', array());
        return DesignBoxAdmin(_t('_adm_box_cpt_links'), $sContent, '', '', 11);
    }
    function getCodeHelp()
    {
        $sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('dashboard_help.html', array(
            'content' => _t('_adm_txt_dashboard_help', CH_WSB_URL_ADMIN, CH_WSB_URL_ROOT),
        ));

        return DesignBoxAdmin(_t('_adm_box_cpt_help'), $sContent, '', '', 11);
    }
    function getCodeCharts()
    {
        $aObjects = $GLOBALS['MySQL']->getAll("SELECT * FROM `sys_objects_charts` WHERE `active` = 1 ORDER BY `order` ASC");
        foreach ($aObjects as $k => $a)
            $aObjects[$k]['title'] = _t($a['title']);
        $sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('dashboard_charts.html', array(
            'proto' => ch_proto(),
            'admin_url' => CH_WSB_URL_ADMIN,
            'from' => date('Y-m-d', time() - 30*24*60*60),
            'to' => date('Y-m-d', time()),
            'ch_repeat:objects' => $aObjects,
        ));

        // add datepicker
        ch_import('ChTemplFormView');
        $oForm = new ChTemplFormView(array());
        $oForm->addCssJs(true);

        return DesignBoxAdmin(_t('_adm_box_cpt_charts'), $sContent, '', '', 11);
    }
    function getCodeStats()
    {
        $aStats = getSiteStatArray();

        $aTmplItemsCom = $aTmplItemsImp = array();
        foreach($aStats as $aStat) {
            $mixedItem = $this->_getStatsItem($aStat);
            if($mixedItem !== false)
                $aTmplItemsCom[] = $mixedItem;

            $mixedItem = $this->_getStatsItem($aStat, 'adm_');
            if($mixedItem !== false)
                $aTmplItemsImp[] = $mixedItem;
        }

        $aCommonChartData = array();
        foreach ($aTmplItemsCom as $r)
            $aCommonChartData[] = array(
            	'value' => $r['number'],
	            'color' => '#' . dechex(rand(0x000000, 0xFFFFFF)),
	            'highlight' => '',
	            'label' => ch_js_string($r['caption'], CH_ESCAPE_STR_APOS),
            );
		$sCommonChartData = json_encode($aCommonChartData);

        $GLOBALS ['oAdmTemplate']->addJsSystem(array(
			'chart.min.js',
		));

        $sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('dashboard_stats.html', array(
            'ch_repeat:items_common' => $aTmplItemsCom,
            'ch_repeat:items_important' => $aTmplItemsImp,
            'common_chart_data' => $sCommonChartData,
        ));
        return DesignBoxAdmin(_t('_adm_box_cpt_content'), $sContent, '', '', 11);
    }
    function _getStatsItem($aStat, $sPrefix = '')
    {
        if(empty($aStat[$sPrefix . 'query']))
            return false;

        $iNumber = (int)$GLOBALS['MySQL']->getOne($aStat[$sPrefix . 'query']);
        if(!empty($sPrefix) && $iNumber == 0)
            return false;

        $sCaption = _t('_' . $aStat['capt'] . ($sPrefix != '' ? '_' . trim($sPrefix, '_') . '_stats' : ''));
        $bLink = !empty($aStat[$sPrefix . 'link']);
        if($bLink) {
            $aStat[$sPrefix . 'link'] = str_replace(array('{site_url}', '{admin_url}'), array(CH_WSB_URL_ROOT, CH_WSB_URL_ADMIN), $aStat[$sPrefix . 'link']);
            if(substr($aStat[$sPrefix . 'link'], 0, 4) != 'http')
                $aStat[$sPrefix . 'link'] = CH_WSB_URL_ROOT . $aStat[$sPrefix . 'link'];
        }

        return array(
            'number' => $iNumber,
            'caption' => $sCaption,
            'ch_if:show_link' => array(
                'condition' => $bLink,
                'content' => array(
                    'link' => $aStat[$sPrefix . 'link'],
                    'number' => $iNumber,
                    'caption' => $sCaption
                )
            ),
            'ch_if:show_text' => array(
                'condition' => !$bLink,
                'content' => array(
                    'number' => $iNumber,
                    'caption' => $sCaption
                )
            )
        );
    }
}
