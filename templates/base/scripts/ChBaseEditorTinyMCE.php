<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbEditor');

/**
 * TinyMCE editor representation.
 * @see ChWsbEditor
 */
class ChBaseEditorTinyMCE extends ChWsbEditor
{
    /**
     * Common initialization params
     */
    protected static $CONF_COMMON = "
                    jQuery('{ch_var_selector}').tinymce({
                        {ch_var_custom_init}
                        {ch_var_custom_conf}
                        document_base_url: '{ch_url_root}',
                        language: '{ch_var_lang}',
                        skin: '{ch_var_skin}',
                        content_css: '{ch_var_content_css}',
                        entity_encoding: 'raw',
                        browser_spellcheck: true,
                        contextmenu: false,
                        branding: false,
                        images_upload_url: '{ch_url_root}media/images/tinymce_uploads/postAcceptor.php',
                    });
    ";

    /**
     * Standard view initialization params
     */
    protected static $WIDTH_STANDARD = '100%';
    protected static $MODULES_STANDARD = array (
                            "advlist: '{ch_url_tinymce}plugins/advlist/plugin.min.js'",
                            "autolink: '{ch_url_tinymce}plugins/autolink/plugin.min.js'",
                            "autosave: '{ch_url_tinymce}plugins/autosave/plugin.min.js'",
                            "code: '{ch_url_tinymce}plugins/code/plugin.min.js'",
                            "hr: '{ch_url_tinymce}plugins/hr/plugin.min.js'",
                            "image: '{ch_url_tinymce}plugins/image/plugin.min.js'",
                            "link: '{ch_url_tinymce}plugins/link/plugin.min.js'",
                            "lists: '{ch_url_tinymce}plugins/lists/plugin.min.js'",
                            "media: '{ch_url_tinymce}plugins/media/plugin.min.js'",
                            "fullscreen: '{ch_url_tinymce}plugins/fullscreen/plugin.min.js'",
    );
    protected static $CONF_STANDARD = "
                        external_plugins: {
                            {modules}
                        },
                        width: '100%',
                        height: '270',
                        theme_url: '{ch_url_tinymce}themes/silver/theme.min.js',
                        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                        statusbar: true,
                        resize: true,
    ";

    /**
     * Minimal view initialization params
     */
    protected static $WIDTH_MINI = '100%';
    protected static $MODULES_MINI = array (
                            "autolink: '{ch_url_tinymce}plugins/autolink/plugin.min.js'",
                            "image: '{ch_url_tinymce}plugins/image/plugin.min.js'",
                            "link: '{ch_url_tinymce}plugins/link/plugin.min.js'",
                            "lists: '{ch_url_tinymce}plugins/lists/plugin.min.js'",
    );
    protected static $CONF_MINI = "
                        menubar: false,
                        external_plugins: {
                            {modules}
                        },
                        width: '100%',
                        height: '150',
                        theme_url: '{ch_url_tinymce}themes/silver/theme.min.js',
                        toolbar: 'bold italic underline removeformat | bullist numlist | alignleft aligncenter alignright | blockquote | link unlink image',
                        statusbar: false,
    ";

    /**
     * Full view initialization params
     */
    protected static $WIDTH_FULL = '100%';
    protected static $MODULES_FULL = array (
                            "advlist: '{ch_url_tinymce}plugins/advlist/plugin.min.js'",
                            "anchor: '{ch_url_tinymce}plugins/anchor/plugin.min.js'",
                            "autolink: '{ch_url_tinymce}plugins/autolink/plugin.min.js'",
                            "autosave: '{ch_url_tinymce}plugins/autosave/plugin.min.js'",
                            "charmap: '{ch_url_tinymce}plugins/charmap/plugin.min.js'",
                            "code: '{ch_url_tinymce}plugins/code/plugin.min.js'",
                            "emoticons: '{ch_url_tinymce}plugins/emoticons/plugin.min.js'",
                            "hr: '{ch_url_tinymce}plugins/hr/plugin.min.js'",
                            "image: '{ch_url_tinymce}plugins/image/plugin.min.js'",
                            "link: '{ch_url_tinymce}plugins/link/plugin.min.js'",
                            "lists: '{ch_url_tinymce}plugins/lists/plugin.min.js'",
                            "media: '{ch_url_tinymce}plugins/media/plugin.min.js'",
                            "nonbreaking: '{ch_url_tinymce}plugins/nonbreaking/plugin.min.js'",
                            "pagebreak: '{ch_url_tinymce}plugins/pagebreak/plugin.min.js'",
                            "preview: '{ch_url_tinymce}plugins/preview/plugin.min.js'",
                            "print: '{ch_url_tinymce}plugins/print/plugin.min.js'",
                            "save: '{ch_url_tinymce}plugins/save/plugin.min.js'",
                            "searchreplace: '{ch_url_tinymce}plugins/searchreplace/plugin.min.js'",
                            "table: '{ch_url_tinymce}plugins/table/plugin.min.js'",
                            "textcolor: '{ch_url_tinymce}plugins/textcolor/plugin.min.js'",
                            "visualblocks: '{ch_url_tinymce}plugins/visualblocks/plugin.min.js'",
                            "fullscreen: '{ch_url_tinymce}plugins/fullscreen/plugin.min.js'",

    );
    protected static $CONF_FULL = "
                        external_plugins: {
                            {modules}
                        },
                        width: '100%',
                        height: '320',
                        theme_url: '{ch_url_tinymce}themes/silver/theme.min.js',
                        toolbar: [
                            'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                            'print preview media | forecolor emoticons'
                        ],
                        statusbar: true,
                        resize: true,
                        image_advtab: true,
    ";

    protected $_sConfCustom = '';

    /**
     * Available editor languages
     */
    protected static $CONF_LANGS = array('ar' => 1, 'ar_SA' => 1, 'az' => 1, 'be' => 1, 'bg_BG' => 1, 'bn_BD' => 1, 'bs' => 1, 'ca' => 1, 'cs' => 1, 'cs_CZ' => 1, 'cy' => 1, 'da' => 1, 'de' => 1, 'de_AT' => 1, 'dv' => 1, 'el' => 1, 'en_CA' => 1, 'en_GB' => 1, 'eo' => 1, 'es' => 1, 'es_MX' => 1, 'et' => 1, 'eu' => 1, 'fa' => 1, 'fa_IR' => 1, 'fi' => 1, 'fo' => 1, 'fr_CH' => 1, 'fr_FR' => 1, 'ga' => 1, 'gd' => 1, 'gl' => 1, 'he_IL' => 1, 'hi_IN' => 1, 'hr' => 1, 'hu_HU' => 1, 'hy' => 1, 'id' => 1, 'is_IS' => 1, 'it' => 1, 'ja' => 1, 'ka_GE' => 1, 'kab' => 1, 'kk' => 1, 'km_KH' => 1, 'ko' => 1, 'ko_KR' => 1, 'ku' => 1, 'ku_IQ' => 1, 'lb' => 1, 'lt' => 1, 'lv' => 1, 'ml' => 1, 'ml_IN' => 1, 'mn_MN' => 1, 'nb_NO' => 1, 'nl' => 1, 'pl' => 1, 'pt_BR' => 1, 'pt_PT' => 1, 'ro' => 1, 'ru' => 1, 'si_LK' => 1, 'sk' => 1, 'sl_SI' => 1, 'sr' => 1, 'sv_SE' => 1, 'ta' => 1, 'ta_IN' => 1, 'tg' => 1, 'th_TH' => 1, 'tr' => 1, 'tr_TR' => 1, 'tt' => 1, 'ug' => 1, 'uk' => 1, 'uk_UA' => 1, 'vi' => 1, 'vi_VN' => 1, 'zh_CN' => 1, 'zh_TW' => 1);

    protected $_oTemplate;
    protected $_bJsCssAdded = false;

    public function __construct ($aObject, $oTemplate)
    {
        parent::__construct ($aObject);

        if ($oTemplate)
            $this->_oTemplate = $oTemplate;
        else
            $this->_oTemplate = $GLOBALS['oSysTemplate'];
    }

    /**
     * Get minimal width which is neede for editor for the provided view mode
     */
    public function getWidth ($iViewMode)
    {
        switch ($iViewMode) {
            case CH_EDITOR_MINI:
                return self::$WIDTH_MINI;
            case CH_EDITOR_FULL:
                return self::$WIDTH_FULL;
            break;
            case CH_EDITOR_STANDARD:
            default:
                return self::$WIDTH_STANDARD;
        }
    }

    /**
     * Set custom TinyMCE configuration option
     */
    public function setCustomConf ($s)
    {
        $this->_sConfCustom = $s;
    }

    /**
     * Attach editor to HTML element, in most cases - textarea.
     * @param $sSelector - jQuery selector to attach editor to.
     * @param $iViewMode - editor view mode: CH_EDITOR_STANDARD, CH_EDITOR_MINI, CH_EDITOR_FULL
     * @param $bDynamicMode - is AJAX mode or not, the HTML with editor area is loaded dynamically.
     */
    public function attachEditor ($sSelector, $iViewMode = CH_EDITOR_STANDARD, $bDynamicMode = false)
    {
        // set visual mode
        switch ($iViewMode) {
            case CH_EDITOR_MINI:
                $sToolsItems = self::$CONF_MINI;
                $aModules = self::$MODULES_MINI;
                break;
            case CH_EDITOR_FULL:
                $sToolsItems = self::$CONF_FULL;
                $aModules = self::$MODULES_FULL;
            break;
            case CH_EDITOR_STANDARD:
            default:
                $sToolsItems = self::$CONF_STANDARD;
                $aModules = self::$MODULES_STANDARD;
        }

        // detect language
        $aLang = ch_lang_info();
        if (isset(self::$CONF_LANGS[$aLang['LanguageCountry']]))
            $sLang = $aLang['LanguageCountry'];
        elseif (isset(self::$CONF_LANGS[$aLang['Name']]))
            $sLang = $aLang['Name'];
        else
            $sLang = 'en_GB';

        if($this->_aObject['content_css'] == '') {
            $sContentCss = 'default';
        } else {
            $sContentCss = ch_js_string($this->_aObject['content_css'], CH_ESCAPE_STR_APOS);
        }

        $aMarkers = array(
            'ch_var_custom_init' => &$sToolsItems,
            'ch_var_custom_conf' => $this->_sConfCustom,
            'ch_var_plugins_path' => ch_js_string(CH_WSB_URL_PLUGINS, CH_ESCAPE_STR_APOS),
            'ch_var_skin' => ch_js_string($this->_aObject['skin'], CH_ESCAPE_STR_APOS),
            'ch_var_content_css' => $sContentCss,
            'ch_var_lang' => ch_js_string($sLang, CH_ESCAPE_STR_APOS),
            'ch_var_selector' => ch_js_string($sSelector, CH_ESCAPE_STR_APOS),
            'ch_url_root' => ch_js_string(CH_WSB_URL_ROOT, CH_ESCAPE_STR_APOS),
            'ch_url_tinymce' => ch_js_string(CH_WSB_URL_PLUGINS . 'tinymce/', CH_ESCAPE_STR_APOS),
        );

        $o = new ChWsbAlerts('system', 'attach_editor', 0, 0, array('markers' => &$aMarkers, 'modules' => &$aModules, 'view_mode' => $iViewMode, 'dynamic_mode' => $bDynamicMode, 'selector' => $sSelector, 'editor' => $this));
        $o->alert();

        $sToolsItems = str_replace('{modules}', join(",\n", $aModules), $sToolsItems);

        // initialize editor
        $sInitEditor = $this->_replaceMarkers(self::$CONF_COMMON, $aMarkers);

        if ($bDynamicMode) {

            $sScript = "<script>
                if ('undefined' == typeof(jQuery(document).tinymce)) {
                    window.tinyMCEPreInit = {base : '" . ch_js_string(CH_WSB_URL_PLUGINS . 'tinymce', CH_ESCAPE_STR_APOS) . "', suffix : '.min', query : ''};
                    $.getScript('" . ch_js_string(CH_WSB_URL_PLUGINS . 'tinymce/tinymce.min.js', CH_ESCAPE_STR_APOS) . "', function(data, textStatus, jqxhr) {
                        $.getScript('" . ch_js_string(CH_WSB_URL_PLUGINS . 'tinymce/jquery.tinymce.min.js', CH_ESCAPE_STR_APOS) . "', function(data, textStatus, jqxhr) {
                            $sInitEditor
                        });
                    });
                } else {
                    setTimeout(function () {
                        $sInitEditor
                    }, 10); // wait while html is rendered in case of dynamic adding html with tinymce
                }
            </script>";

        } else {

            $sScript = "
            <script>
                $(document).ready(function () {
                    $sInitEditor
                });
            </script>";

        }

        return $this->_addJsCss($bDynamicMode) . $sScript;
    }

    /**
     * Add css/js files which are needed for editor display and functionality.
     */
    protected function _addJsCss($bDynamicMode = false, $sInitEditor = '')
    {
        if ($bDynamicMode)
            return '';
        if ($this->_bJsCssAdded)
            return '';

        $this->_oTemplate->addInjection ('injection_head_begin', 'text', "<script>window.tinyMCEPreInit = {base : '" . ch_js_string(CH_WSB_URL_PLUGINS . 'tinymce', CH_ESCAPE_STR_APOS) . "', suffix : '.min', query : ''};</script>\n");

        $aJs = array('tinymce/tinymce.min.js', 'tinymce/jquery.tinymce.min.js', 'prism.js');
        $this->_oTemplate->addJs($aJs);

        $aCss = array('prism.css');
        $this->_oTemplate->addCss($aCss);

        if (isset($GLOBALS['oAdmTemplate'])) {
            $GLOBALS['oAdmTemplate']->addJs($aJs);
            $GLOBALS['oAdmTemplate']->addCss($aCss);
        }

        $this->_bJsCssAdded = true;
        return '';
    }

}
