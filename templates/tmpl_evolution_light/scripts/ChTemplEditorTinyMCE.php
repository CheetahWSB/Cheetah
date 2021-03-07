<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChBaseEditorTinyMCE');

/**
 * @see ChWsbEditor
 */
class ChTemplEditorTinyMCE extends ChBaseEditorTinyMCE
{
    public function __construct ($aObject, $oTemplate = false)
    {
        // This template will use the default tinymce 5 skin. So these 2 values do not need to be passed.
        //$aObject['skin'] = 'evolution_light';
        //$aObject['content_css'] = 'evolution_light';
        parent::__construct ($aObject, $oTemplate);
    }
}
