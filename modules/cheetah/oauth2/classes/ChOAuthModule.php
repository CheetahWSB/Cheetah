<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModule');
ch_import('ChWsbPaginate');
ch_import('ChWsbAlerts');

require_once (CH_DIRECTORY_PATH_PLUGINS . 'OAuth2/Autoloader.php');
OAuth2\Autoloader::register();

class ChOAuthModule extends ChWsbModule
{
    protected $_oStorage;
    protected $_oServer;
    protected $_oAPI;

    function __construct(&$aModule)
    {
        parent::__construct($aModule);

        $aConfig = array (
            'client_table' => 'ch_oauth_clients',
            'access_token_table' => 'ch_oauth_access_tokens',
            'refresh_token_table' => 'ch_oauth_refresh_tokens',
            'code_table' => 'ch_oauth_authorization_codes',
            'user_table' => 'Profiles',
            'jwt_table'  => '',
            'jti_table'  => '',
            'scope_table'  => 'ch_oauth_scopes',
            'public_key_table'  => '',
        );

        $this->_oStorage = new OAuth2\Storage\Pdo(ChWsbDb::getInstance()->getLink(), $aConfig);

        $this->_oServer = new OAuth2\Server($this->_oStorage, array (
            'require_exact_redirect_uri' => false,
        ));

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $this->_oServer->addGrantType(new OAuth2\GrantType\ClientCredentials($this->_oStorage));

        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->_oServer->addGrantType(new OAuth2\GrantType\AuthorizationCode($this->_oStorage));

    }

    function actionToken ()
    {
        // Handle a request for an OAuth2.0 Access Token and send the response to the client
        $this->_oServer->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
    }

    function actionApi ($sAction)
    {
        // Handle a request to a resource and authenticate the access token
        if (!$this->_oServer->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->_oServer->getResponse()->send();
            return;
        }

        $aToken = $this->_oServer->getAccessTokenData(OAuth2\Request::createFromGlobals());

        if (!$this->_oAPI) {
            ch_import('API', $this->_aModule);
            $this->_oAPI = new ChOAuthAPI($this);
        }

        if (!$sAction || !method_exists($this->_oAPI, $sAction) || 0 === strcasecmp('errorOutput', $sAction) || 0 === strcasecmp('output', $sAction)) {
            $this->_oAPI->errorOutput(404, 'not_found', 'No such API endpoint available');
            return;
        }

        $sScope = $this->_oAPI->aAction2Scope[$sAction];
        if (false === strpos($sScope, $aToken['scope'])) {
            $this->_oAPI->errorOutput(403, 'insufficient_scope', 'The request requires higher privileges than provided by the access token');
            return;
        }

        $this->_oAPI->$sAction($aToken);

        //echo json_encode(array('success' => true, 'message' => 'TODO: process "' . $sAction . '" action for user "' . $aToken['user_id'] . '"'));
    }

    function actionAuth ()
    {
        $oRequest = OAuth2\Request::createFromGlobals();
        $oResponse = new OAuth2\Response();

        // validate the authorize request
        if (!$this->_oServer->validateAuthorizeRequest($oRequest, $oResponse)) {
            $o = json_decode($oResponse->getResponseBody());
            $this->_oTemplate->pageError($o->error_description);
        }

        if (!isLogged()) {
            $_REQUEST['relocate'] = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'auth/?client_id=' . ch_get('client_id') . '&response_type=' . ch_get('response_type') . '&state=' . ch_get('state') . '&redirect_uri=' . ch_get('redirect_uri');
            login_form('', 0, false, 'disable_external_auth no_join_text');
            return;
        }

        if (!($iProfileId = $this->_oDb->getSavedProfile(getLoggedId())) && empty($_POST)) {
            $this->_oTemplate->pageAuth($this->_oDb->getClientTitle(ch_get('client_id')));
            return;
        }

        $bConfirm = $iProfileId ? true : (bool)ch_get('confirm');
        $iProfileId = getLoggedId();

        $this->_oServer->handleAuthorizeRequest($oRequest, $oResponse, $bConfirm, $iProfileId);

        $oResponse->send();
    }

    function actionAdministration ()
    {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();


        ch_import('FormAdd', $this->_aModule);
        $oForm = new ChOAuthFormAdd($this);
        $oForm->initChecker();

        $sContent = '';
        if ($oForm->isSubmittedAndValid ()) {
            $oForm->insert ();
            $sContent = MsgBox(_t('_Success'));
        }
        $sContent .= $oForm->getCode ();

        $aVars = array (
            'content' => $sContent,
        );
        echo $this->_oTemplate->adminBlock ($this->_oTemplate->parseHtmlByName('default_padding', $aVars), _t('_ch_oauth_add'));


        if (is_array($_POST['clients']) && $_POST['clients'])
            $this->_oDb->deleteClients($_POST['clients']);
        ch_import('ChTemplSearchResult');
        $sControls = ChTemplSearchResult::showAdminActionsPanel('ch-oauth-form-add', array(
            'ch-oauth-delete' => _t('_Delete'),
        ), 'clients');

        $aClients = $this->_oDb->getClients();
        $aVars = array (
            'ch_repeat:clients' => $aClients,
            'controls' => $sControls,
        );
        echo $this->_oTemplate->adminBlock ($this->_oTemplate->parseHtmlByName('clients', $aVars), _t('_ch_oauth_clients'));


        $aVars = array (
            'content' => _t('_ch_oauth_help_text', CH_WSB_URL_ROOT)
        );
        echo $this->_oTemplate->adminBlock ($this->_oTemplate->parseHtmlByName('default_padding', $aVars), _t('_ch_oauth_help'));


        $this->_oTemplate->addCssAdmin ('forms_adv.css');
        $this->_oTemplate->pageCodeAdmin (_t('_ch_oauth_administration'));
    }

    function isAdmin ()
    {
        return $GLOBALS['logged']['admin'] ? true : false;
    }
}
