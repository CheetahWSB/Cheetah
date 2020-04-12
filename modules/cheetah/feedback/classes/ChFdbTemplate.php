<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbTextTemplate');

class ChFdbTemplate extends ChWsbTextTemplate
{
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);

        $this->sCssPrefix = 'feedback';
    }
    function displayAdminBlock($aParams)
    {
        $oSearchResult = $aParams['search_result_object'];
        unset($aParams['search_result_object']);

        $sModuleUri = $this->_oConfig->getUri();
        $aButtons = array(
            $sModuleUri . '-approve' => _t('_' . $sModuleUri . '_lcaption_approve'),
            $sModuleUri . '-reject' => _t('_' . $sModuleUri . '_lcaption_reject'),
            $sModuleUri . '-delete' => _t('_' . $sModuleUri . '_lcaption_delete')
        );

        $aResult = array(
            'include_css' => $this->addCss(array('view.css', 'cmts.css'), true),
            'include_js_content' => $this->getViewJs(),
            'filter' => $oSearchResult->showAdminFilterPanel($this->_oDb->unescape($aParams['filter_value']), $sModuleUri . '-filter-txt', $sModuleUri . '-filter-chb', $sModuleUri . '-filter'),
            'content' => $this->displayList($aParams),
            'control' => $oSearchResult->showAdminActionsPanel($this->sCssPrefix . '-view-admin', $aButtons, $sModuleUri . '-ids')
        );

        return $this->addJs(array('main.js'), true) . $this->parseHtmlByName('admin.html', $aResult);
    }
    function displayItem($aParams, &$aEntry)
    {
        global $oFunctions;

        $sSampleType = $aParams['sample_type'];
        $iViewerType = $aParams['viewer_type'];
        $iViewerId = isset($aParams['viewer_id']) ? (int)$aParams['viewer_id'] : 0;
        $bAdminPanel = $iViewerType == CH_TD_VIEWER_TYPE_ADMIN && ((isset($aParams['admin_panel']) && $aParams['admin_panel']) || $sSampleType == 'admin');
        $bAuthorExists = !empty($aEntry['author_id']) && !empty($aEntry['author_username']);

        $sModuleUri = $this->_oConfig->getUri();
        $sLKLinkEdit = _t('_' . $sModuleUri . '_lcaption_edit');

        $aTmplVars = array(
            'id' => $this->_oConfig->getSystemPrefix() . $aEntry['id'],
        	'ch_if:author_icon' => array(
        		'condition' => $bAuthorExists,
                'content' => array(
                    'author_icon' => get_member_icon($aEntry['author_id'], 'left'),
                )
            ),

            'ch_if:author_icon_empty' => array(
        		'condition' => !$bAuthorExists,
                'content' => array(
            		'author_icon' => $oFunctions->getSexPic('', 'small')
                )
            ),
            'ch_if:author_username_link' => array(
                'condition' => $bAuthorExists,
                'content' => array(
        			'author_url' => getProfileLink($aEntry['author_id']),
            		'author_username' => getNickName($aEntry['author_id'])
                )
            ),
            'ch_if:author_username_text' => array(
                'condition' => !$bAuthorExists,
            	'content' => array(
            		'author_username' => _t('_Anonymous')
                )
            ),
            'caption' => str_replace("$", "&#36;", $aEntry['caption']),
            'class' => !in_array($sSampleType, array('view')) ? ' ' . $this->sCssPrefix . '-text-snippet' : '',
            'date' => defineTimeInterval($aEntry['date']),
            'content' => str_replace("$", "&#36;", $aEntry['content']),
            'link' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aEntry['uri'],
            'ch_if:checkbox' => array(
                'condition' => $bAdminPanel,
                'content' => array(
                    'id' => $aEntry['id']
                ),
            ),
            'ch_if:status' => array(
                'condition' => ($iViewerType == CH_TD_VIEWER_TYPE_MEMBER && $iViewerId == $aEntry['author_id']) || $iViewerType == CH_TD_VIEWER_TYPE_ADMIN,
                'content' => array(
                    'status' => _t('_' . $sModuleUri . '_status_' . $aEntry['status'])
                ),
            ),
            'ch_if:edit_link' => array (
                'condition' => ($iViewerType == CH_TD_VIEWER_TYPE_MEMBER  && $iViewerId == $aEntry['author_id']) || $iViewerType == CH_TD_VIEWER_TYPE_ADMIN,
                'content' => array(
                    'edit_link_url' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'post/' . $aEntry['uri'],
                    'edit_link_caption' => $sLKLinkEdit,
                )
            )
        );

        return $this->parseHtmlByName('item.html', $aTmplVars);
    }

    protected function _updatePaginate($aParams)
    {
        switch($aParams['sample_type']) {
            case 'owner':
                $this->oPaginate->setCount($this->_oDb->getCount($aParams));
                $this->oPaginate->setOnChangePage($this->_oConfig->getJsObject() . '.changePage({start}, {per_page}, \'' . $aParams['sample_type'] . '\', \'' . urlencode(serialize($aParams['sample_params'])) . '\')');
                break;

            default:
                parent::_updatePaginate($aParams);
        }
    }
}
