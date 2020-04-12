<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import ('ChWsbTwigTemplate');

/*
 * Events module View
 */
class ChEventsTemplate extends ChWsbTwigTemplate
{
    /**
     * Constructor
     */
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);
        $this->_iPageIndex = 300;
    }

    function unit ($aData, $sTemplateName, &$oVotingView, $isShort = false)
    {
        if (null == $this->_oMain)
            $this->_oMain = ChWsbModule::getInstance('ChEventsModule');

        if (!$this->_oMain->isAllowedView ($aData)) {
            $aVars = array ();
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }

        $sImage = '';
        if ($aData['PrimPhoto']) {
            $a = array ('ID' => $aData['ResponsibleID'], 'Avatar' => $aData['PrimPhoto']);
            $aImage = ChWsbService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        }

        $aVars = array (
            'id' => $aData['ID'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'),
            'event_url' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['EntryUri'],
            'event_title' => $aData['Title'],
            'event_start' => $this->_oMain->_formatDateInBrowse($aData),
            'spacer' => getTemplateIcon('spacer.gif'),
            'participants' => $aData['FansCount'],
            'country_city' => $this->_oMain->_formatLocation($aData),
            'snippet_text' => $this->_oMain->_formatSnippetText($aData),
            'ch_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => $aData['ResponsibleID'] ? getNickName($aData['ResponsibleID']) : _t('_ch_events_admin'),
                    'author_url' => $aData['ResponsibleID'] ? getProfileLink($aData['ResponsibleID']) : 'javascript:void(0);',
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['ID'], $aData['Rate']) : '&#160;',
                ),
            ),
        );

        $aVars = array_merge ($aVars, $aData);
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    // ======================= ppage compose block functions

    function blockInfo (&$aEvent)
    {
        if (null == $this->_oMain)
            $this->_oMain = ChWsbModule::getInstance('ChEventsModule');

        $aAuthor = getProfileInfo($aEvent['ResponsibleID']);

        $aVars = array (
            'author_unit' => $GLOBALS['oFunctions']->getMemberThumbnail($aAuthor['ID'], 'none', true),
            'date' => getLocaleDate($aEvent['Date'], CH_WSB_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aEvent['Date'], false),
            'cats' => $this->parseCategories($aEvent['Categories']),
            'tags' => $this->parseTags($aEvent['Tags']),
            'location' => $this->_oMain->_formatLocation($aEvent, true, true),
            'fields' => $this->blockFields($aEvent),
            'author_username' => $aAuthor ? $aAuthor['NickName'] : _t('_ch_events_admin'),
            'author_url' => $aAuthor ? getProfileLink($aAuthor['ID']) : 'javascript:void(0)',
        );
        return $this->parseHtmlByName('block_info', $aVars);
    }

    function blockDesc (&$aEvent)
    {
        $aVars = array (
            'description' => $aEvent['Description'],
        );
        return $this->parseHtmlByName('block_description', $aVars);
    }

    function blockFields (&$aEvent)
    {
        $sRet = '<table class="ch_events_fields">';
        ch_events_import ('FormAdd');
        $oForm = new ChEventsFormAdd ($GLOBALS['oChEventsModule'], $this->_iProfileId);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
            $sRet .= '<tr><td class="ch_events_field_name ch-def-font-grayed ch-def-padding-sec-right" valign="top">' . $a['caption'] . '</td><td class="ch_events_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display'])))
                $sRet .= call_user_func_array(array($this, $a['display']), array($aEvent[$k]));
            else
                $sRet .= $aEvent[$k];
            $sRet .= '</td></tr>';
        }
        $sRet .= '</table>';
        return $sRet;
    }

    // ======================= output display filters functions

    function filterDateUTC ($sTimestamp)
    {
        return gmdate(getLocaleFormat(CH_WSB_LOCALE_DATE), $sTimestamp);
    }
}
