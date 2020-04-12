<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbProfileFields.php');

class ChEventsFormSearch extends ChTemplFormView
{
    function __construct ()
    {
        $oProfileFields = new ChWsbProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        $aCountries = array_merge (array('' => _t('_ch_events_all_countries')), $aCountries);

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_search_events',
                'action'   => '',
                'method'   => 'get',
            ),

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
                'csrf' => array(
                    'disable' => true,
                ),
            ),

            'inputs' => array(
                'Keyword' => array(
                    'type' => 'text',
                    'name' => 'Keyword',
                    'caption' => _t('_ch_events_caption_keyword'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_ch_events_err_keyword'),
                    ),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),
                'Country' => array(
                    'type' => 'select_box',
                    'name' => 'Country',
                    'caption' => _t('_ch_events_caption_country'),
                    'values' => $aCountries,
                    'required' => true,
                    'checker' => array (
                        'func' => 'preg',
                        'params' => array('/^[a-zA-Z]{0,2}$/'),
                        'error' => _t ('_ch_events_err_country'),
                    ),
                    'db' => array (
                        'pass' => 'Preg',
                        'params' => array('/([a-zA-Z]{0,2})/'),
                    ),
                ),
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                    'colspan' => true,
                ),
            ),
        );

        parent::__construct ($aCustomForm);
    }
}
