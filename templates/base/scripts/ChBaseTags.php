<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbTags.php');

class ChBaseTags extends ChWsbTags
{
    var $_sTagTmplName;
    var $_sTagTmplContent;

    function __construct ()
    {
        parent::__construct();

        $this->_sTagTmplName = 'view_tags.html';
        $this->_sTagTmplContent = '';
    }

    function getTagsView ($aTotalTags, $sHrefTempl)
    {
        global $oTemplConfig;
        global $oSysTemplate;

        if (empty($aTotalTags))
            return MsgBox(_t('_Empty'));

        $iMinFontSize = $oTemplConfig -> iTagsMinFontSize;
        $iMaxFontSize = $oTemplConfig -> iTagsMaxFontSize;
        $iFontDiff = $iMaxFontSize - $iMinFontSize;

        $iMinRating = min( $aTotalTags );
        $iMaxRating = max( $aTotalTags );

        $iRatingDiff = $iMaxRating - $iMinRating;
        $iRatingDiff = ($iRatingDiff==0)? 1:$iRatingDiff;

        $sCode = '<div class="tags_wrapper ch-def-bc-margin">';
        $aUnit = array();
        foreach( $aTotalTags as $sTag => $iCount ) {
            $aUnit['tagSize'] = $iMinFontSize + round( $iFontDiff * ( ( $iCount - $iMinRating ) / $iRatingDiff ) );
            $aUnit['tagHref'] = str_replace( '{tag}', rawurlencode(title2uri($sTag)), $sHrefTempl);
            $aUnit['countCapt'] = _t( '_Count' );
            $aUnit['countNum'] = $iCount;
            $aUnit['tag'] = htmlspecialchars_adv( $sTag );
            if ($this->_sTagTmplContent)
                $sCode .= $oSysTemplate->parseHtmlByContent($this->_sTagTmplContent, $aUnit);
            else
                $sCode .= $oSysTemplate->parseHtmlByName($this->_sTagTmplName, $aUnit);
        }
        $sCode .= '</div>';
        $sCode .= '<div class="clear_both"></div>';
        return $sCode;
    }

    function getTagsTopMenu ($aParam, $sAction = '')
    {
        $aTopMenu = array();
        $aParamTmp = $aParam;

        foreach ($this->aTagObjects as $sKey => $aTagUnit) {
            $sName = _t($aTagUnit['LangKey']);
            $sHref = ch_html_attribute($_SERVER['PHP_SELF']) . "?tags_mode=$sKey" . ($sAction ? '&action=' . $sAction : '');

            if (isset($aParam['filter']) && $aParam['filter']) {
                $aParamTmp['type'] = $sKey;
                $sName .= '(' . $this->getTagsCount($aParamTmp) . ')';
                $sHref .= '&filter=' . $aParam['filter'];
            }

            if (isset($aParam['date']) && $aParam['date'])
                $sHref .= '&year=' . $aParam['date']['year'] .
                    '&month=' . $aParam['date']['month'] .
                    '&day=' . $aParam['date']['day'];

            $aTopMenu[$sName] = array('href' => $sHref, 'key' => $sKey, 'dynamic' => true, 'active' => ( $sKey == $aParam['type']));
        }

        return $aTopMenu;
    }

    function getTagsTopMenuHtml ($aParam, $iBoxId, $sAction = '')
    {
        $aItems = array();

        $aTopMenu = $this->getTagsTopMenu($aParam, $sAction);
        foreach ($aTopMenu as $sName => $aItem) {
            $aItems[$sName] = array(
                'dynamic' => true,
                'active' => $aItem['active'],
                'href' => $aItem['href']
            );
        }

        return ChWsbPageView::getBlockCaptionItemCode($iBoxId, $aItems);
    }

    function getTagsInternalMenuHtml ($aParam, $iBoxId, $sAction = '')
    {
        global $oSysTemplate;

        $aTmplVars = array(
            'block_id' => $iBoxId,
            'ch_repeat:options' => array()
        );

        $aMenu = $this->getTagsTopMenu($aParam, $sAction);
        foreach ($aMenu as $sName => $aItem)
            $aTmplVars['ch_repeat:options'][] = array(
                'key' => $aItem['href'],
                'ch_if:show_selected' => array(
                    'condition' => $aItem['key'] == $aParam['type'],
                    'content' => array()
                ),
                'value' => $sName
            );
        $sTopControls = $oSysTemplate->parseHtmlByName('tags_top_controls.html', $aTmplVars);

        return $oSysTemplate->parseHtmlByName('designbox_top_controls.html', array('top_controls' => $sTopControls));
    }

    function display($aParam, $iBoxId, $sAction = '', $sUrl = '')
    {
        $sPaginate = '';

        if (!isset($aParam['type']) || !$aParam['type'])
            return MsgBox(_t( '_Empty' ));

        if (isset($aParam['pagination']) && $aParam['pagination']) {
            ch_import('ChWsbPaginate');
            $sPageUrl = $sUrl ? $sUrl : ch_html_attribute($_SERVER['PHP_SELF']);
            $sPageUrl .= '?tags_mode=' . $aParam['type'] . '&page={page}&per_page={per_page}';

            if (isset($aParam['filter']) && $aParam['filter'])
                $sPageUrl .= '&filter=' . $aParam['filter'];
            if ($sAction)
                $sPageUrl .= '&action=' . $sAction;
            if (isset($aParam['date']) && $aParam['date']) {
                $sPageUrl .= '&year=' . $aParam['date']['year'] .
                    '&month=' . $aParam['date']['month'] .
                    '&day=' . $aParam['date']['day'];
            }

            $aPaginate = array(
                'page_url' => $sPageUrl,
                'info' => true,
                'page_links' => true,
                'on_change_page' => "!loadDynamicBlock($iBoxId, this.href)"
            );

            $aParam['limit'] = $aPaginate['per_page'] = $aParam['pagination'];
            $aPaginate['count'] = $this->getTagsCount($aParam);
            $aPaginate['page'] = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
            $aParam['start'] = $aParam['limit'] * ($aPaginate['page'] - 1);
            if ($aParam['start'] <=0)
                $aParam['start'] = 0;

            $oPaginate = new ChWsbPaginate($aPaginate);
            $sPaginate = '<div class="clear_both"></div>'.$oPaginate->getPaginate();
        }

        $sHrefTmpl = $this->getHrefWithType($aParam['type']);
        $aTotalTags = $this->getTagList($aParam);

        if ($aTotalTags)
            return $this->getTagsView($aTotalTags, $sHrefTmpl) . $sPaginate;
        else
            return MsgBox(_t( '_Empty' ));
    }

    function setTemplateName($sTmplName)
    {
        $this->_sTagTmplName = $sTmplName;
    }

    function setTemplateContent($sTmplContent)
    {
        $this->_sTagTmplContent = $sTmplContent;
    }
}
