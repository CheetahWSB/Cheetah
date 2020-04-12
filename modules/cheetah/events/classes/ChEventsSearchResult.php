<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbTwigSearchResult');

class ChEventsSearchResult extends ChWsbTwigSearchResult
{
    var $aCurrent = array(
        'name' => 'ch_events',
        'title' => '_ch_events_caption_browse',
        'table' => 'ch_events_main',
        'ownFields' => array('ID', 'Title', 'EntryUri', 'Country', 'City', 'Place', 'EventStart', 'EventEnd', 'ResponsibleID', 'PrimPhoto', 'FansCount', 'Rate', 'Description'),
        'searchFields' => array('Title', 'Description', 'City', 'Place', 'Tags', 'Categories'),
        'join' => array(
            'profile' => array(
                    'type' => 'left',
                    'table' => 'Profiles',
                    'mainField' => 'ResponsibleID',
                    'onField' => 'ID',
                    'joinFields' => array('NickName'),
            ),
        ),
        'restriction' => array(
            'activeStatus' => array('value' => 'approved', 'field'=>'Status', 'operator'=>'='),
            'owner' => array('value' => '', 'field' => 'ResponsibleID', 'operator' => '='),
            'tag' => array('value' => '', 'field' => 'Tags', 'operator' => 'against'),
            'category' => array('value' => '', 'field' => 'Category', 'operator' => '=', 'table' => 'sys_categories'),
            'category_type' => array('value' => '', 'field' => 'Type', 'operator' => '=', 'table' => 'sys_categories'),
            'country' => array('value' => '', 'field' => 'Country', 'operator' => '='),
            'public' => array('value' => '', 'field' => 'allow_view_event_to', 'operator' => '='),
        ),
        'paginate' => array('perPage' => 14, 'page' => 1, 'totalNum' => 0, 'totalPages' => 1),
        'sorting' => 'last',
        'rss' => array(
            'title' => '',
            'link' => '',
            'image' => '',
            'profile' => 0,
            'fields' => array (
                'Link' => '',
                'Title' => 'Title',
                'DateTimeUTS' => 'Date',
                'Desc' => 'Description',
                'Image' => 'PrimPhoto',
            ),
        ),
        'ident' => 'ID'
    );

    function __construct($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '')
    {
        $oMain = $this->getMain();

        switch ($sMode) {

            case 'pending':
                if (false !== ch_get('ch_events_filter'))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input(ch_get('ch_events_filter'), CH_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "administration";
                $this->aCurrent['title'] = _t('_ch_events_pending_approval');
                unset($this->aCurrent['rss']);
            break;

            case 'my_pending':
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_ch_events_pending_approval');
                unset($this->aCurrent['rss']);
            break;

            case 'search':
                if ($sValue)
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');

                if ($sValue2)
                    if (is_array($sValue2)) {
                        $this->aCurrent['restriction']['country'] = array('value' => $sValue2, 'field' => 'Country', 'operator' => 'in');
                    } else {
                        $this->aCurrent['restriction']['country']['value'] = $sValue2;
                    }

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $sValue2 = $GLOBALS['MySQL']->unescape($sValue2);
                $this->sBrowseUrl = "search/$sValue/" . (is_array($sValue2) ? implode(',',$sValue2) : $sValue2);
                $this->aCurrent['title'] = _t('_ch_events_caption_search_results') . ' ' . (is_array($sValue2) ? implode(', ',$sValue2) : $sValue2) . ' ' . $sValue;
                unset($this->aCurrent['rss']);
                break;

            case 'user':
                $iProfileId = $GLOBALS['oChEventsModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of events
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/user/$sValue";

                $iProfileId = getID($sValue);
                $this->aCurrent['title'] = _t('_ch_events_caption_browse_by_author', $iProfileId ? getNickName($iProfileId) : $sValue);

                if (ch_get('rss')) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['PrimPhoto']) {
                        $a = array ('ID' => $aData['ResponsibleID'], 'Avatar' => $aData['PrimPhoto']);
                        $aImage = ChWsbService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    }
                }
                break;

            case 'joined':
                $iProfileId = $GLOBALS['oChEventsModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab

                if (!$iProfileId) {

                    $this->isError = true;

                } else {

                    $this->aCurrent['join']['fans'] = array(
                        'type' => 'inner',
                        'table' => 'ch_events_participants',
                        'mainField' => 'ID',
                        'onField' => 'id_entry',
                        'joinFields' => array('id_profile'),
                    );
                    $this->aCurrent['restriction']['fans'] = array(
                        'value' => $iProfileId,
                        'field' => 'id_profile',
                        'operator' => '=',
                        'table' => 'ch_events_participants',
                    );
                    $this->aCurrent['restriction']['confirmed_fans'] = array(
                        'value' => 1,
                        'field' => 'confirmed',
                        'operator' => '=',
                        'table' => 'ch_events_participants',
                    );
                }

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/joined/$sValue";

                $iProfileId = getID($sValue);
                $this->aCurrent['title'] = _t('_ch_events_caption_browse_by_author_joined_events', $iProfileId ? getNickName($iProfileId) : $sValue);

                if (ch_get('rss')) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['Avatar']) {
                        $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
                        $aImage = ChWsbService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    }
                }
                break;

            case 'admin':
                if (ch_get('ch_events_filter'))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input(ch_get('ch_events_filter'), CH_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->sBrowseUrl = "browse/admin";
                $this->aCurrent['title'] = _t('_ch_events_admin_events');
                break;

            case 'category':
                $this->aCurrent['join']['category'] = array(
                    'type' => 'inner',
                    'table' => 'sys_categories',
                    'mainField' => 'ID',
                    'onField' => 'ID',
                    'joinFields' => '',
                );
                $this->aCurrent['restriction']['category_type']['value'] = $this->aCurrent['name'];
                $this->aCurrent['restriction']['category']['value'] = $sValue;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/category/" . title2uri($sValue);
                $this->aCurrent['title'] = _t('_ch_events_caption_browse_by_category', $sValue);
                break;

            case 'tag':
                $this->aCurrent['restriction'][$sMode]['value'] = $sValue;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/$sMode/" . title2uri($sValue);
                $this->aCurrent['title'] = _t('_ch_events_caption_browse_by_'.$sMode, $sValue);
                break;

            case 'country':
                $this->aCurrent['restriction'][$sMode]['value'] = $sValue;
                $this->sBrowseUrl = "browse/$sMode/$sValue";
                $this->aCurrent['title'] = _t('_ch_events_caption_browse_by_'.$sMode, $sValue);
                break;

            case 'upcoming':
                $this->aCurrent['restriction']['upcoming'] = array('value' => time(), 'field' => 'EventEnd', 'operator' => '>');
                $this->aCurrent['sorting'] = 'upcoming';
                $this->sBrowseUrl = 'browse/upcoming';
                $this->aCurrent['title'] = _t('_ch_events_caption_browse_upcoming');
                break;

            case 'past':
                $this->aCurrent['restriction']['past'] = array('value' => time(), 'field' => 'EventEnd', 'operator' => '<');
                $this->aCurrent['sorting'] = 'past';
                $this->sBrowseUrl = 'browse/past';
                $this->aCurrent['title'] = _t('_ch_events_caption_browse_past');
                break;

            case 'recent':
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_ch_events_caption_browse_recently_added');
                break;

            case 'top':
                $this->sBrowseUrl = 'browse/top';
                $this->aCurrent['sorting'] = 'top';
                $this->aCurrent['title'] = _t('_ch_events_caption_browse_top_rated');
                break;

            case 'popular':
                $this->sBrowseUrl = 'browse/popular';
                $this->aCurrent['sorting'] = 'popular';
                $this->aCurrent['title'] = _t('_ch_events_caption_browse_popular');
                break;

            case 'featured':
                $this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'Featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured';
                $this->aCurrent['title'] = _t('_ch_events_caption_browse_featured');
                break;

            case 'calendar':
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 00:00:00')", 'field' => 'EventEnd', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 23:59:59')", 'field' => 'EventStart', 'operator' => '<=', 'no_quote_value' => true);
                $this->sBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";

                $this->aCurrent['title'] = _t('_ch_events_caption_browse_by_day', getLocaleDate(strtotime("{$sValue}-{$sValue2}-{$sValue3}"), CH_WSB_LOCALE_DATE_SHORT));
                break;

            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_ch_events');
                unset($this->aCurrent['rss']);
                break;

            default:
                $this->isError = true;
        }

        $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('ch_events_perpage_browse');

        if (isset($this->aCurrent['rss']))
            $this->aCurrent['rss']['link'] = CH_WSB_URL_ROOT . $oMain->_oConfig->getBaseUri() . $this->sBrowseUrl;

        if (ch_get('rss')) {
            $this->aCurrent['ownFields'][] = 'Description';
            $this->aCurrent['ownFields'][] = 'Date';
            $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('ch_events_max_rss_num');
        }

        ch_events_import('Voting', $this->getModuleArray());
        $oVotingView = new ChEventsVoting ('ch_events', 0);
        $this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

        parent::__construct();
    }

    function getAlterOrder()
    {
        if ($this->aCurrent['sorting'] == 'last') {
            $aSql = array();
            $aSql['order'] = " ORDER BY `ch_events_main`.`Date` DESC";
            return $aSql;
        } elseif ($this->aCurrent['sorting'] == 'upcoming') {
            $aSql = array();
            $aSql['order'] = " ORDER BY `EventStart` ASC";
            return $aSql;
        } elseif ($this->aCurrent['sorting'] == 'past') {
            $aSql = array();
            $aSql['order'] = " ORDER BY `EventEnd` DESC";
            return $aSql;
        } elseif ($this->aCurrent['sorting'] == 'top') {
            $aSql = array();
            $aSql['order'] = " ORDER BY `ch_events_main`.`Rate` DESC, `ch_events_main`.`RateCount` DESC";
            return $aSql;
        } elseif ($this->aCurrent['sorting'] == 'popular') {
            $aSql = array();
            $aSql['order'] = " ORDER BY `ch_events_main`.`FansCount` DESC, `ch_events_main`.`Views` DESC";
            return $aSql;
        }
        return array();
    }

    function displayResultBlock ()
    {
        global $oFunctions;
        $s = parent::displayResultBlock ();
        if ($s) {
            $oMain = $this->getMain();
            $GLOBALS['oSysTemplate']->addDynamicLocation($oMain->_oConfig->getHomePath(), $oMain->_oConfig->getHomeUrl());
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
        }
        return '';
    }

    function getModuleArray()
    {
        return db_arr ("SELECT * FROM `sys_modules` WHERE `title` = 'Events' AND `class_prefix` = 'ChEvents' LIMIT 1");
    }

    function getMain()
    {
        return ChWsbModule::getInstance('ChEventsModule');
    }

    function getRssUnitLink (&$a)
    {
        $oMain = $this->getMain();
        return CH_WSB_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'view/' . $a['EntryUri'];
    }

    function _getPseud ()
    {
        return array(
            'ID' => 'ID',
            'Title' => 'Title',
            'EntryUri' => 'EntryUri',
            'EventStart' => 'EventStart',
            'Place' => 'Place',
            'Country' => 'Country',
            'City' => 'City',
            'ResponsibleID' => 'ResponsibleID',
            'NickName' => 'NickName',
            'PrimPhoto' => 'PrimPhoto',
        );
    }
}
