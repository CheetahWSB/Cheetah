<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbEditorQuery');

/**
 * Standard WYSIWYG editor view.
 * @see ChWsbEditor::attachEditor
 */
define('CH_EDITOR_STANDARD', 1);

/**
 * Full WYSIWYG editor view. If not supported by editor, standard view is used.
 * @see ChWsbEditor::attachEditor
 */
define('CH_EDITOR_FULL', 2);

/**
 * Mini WYSIWYG editor view. If not supported by editor, standard view is used.
 * @see ChWsbEditor::attachEditor
 */
define('CH_EDITOR_MINI', 3);

/**
 * @page objects
 * @section editor Editor
 * @ref ChWsbEditor
 */

/**
 * WYSIWYG editors.
 *
 * Site owner can choose which visual editor can be user on the site.
 *
 * Default visual editor is stored in 'sys_editor_default' setting option.
 *
 * @section editor_create Creating the Editor object:
 *
 *
 * Add record to 'sys_objects_editor' table:
 *
 * - object: name of the editor object, in the format: vendor prefix, underscore, module prefix, underscore, internal identifier or nothing; for example: ch_blogs - custom editor in blogs module.
 * - title: title of the editor, displayed in the studio.
 * - skin: editor skin, if editor suports custom/multiple skins.
 * - override_class_name: user defined class name which is derived from one of base editor classes.
 * - override_class_file: the location of the user defined class, leave it empty if class is located in system folders.
 *
 *
 * @section example Example of usage
 *
 * Apply visual editor to textarea:
 *
 * @code
 *  echo '<textarea id="my_textarea" rows="20" cols="80">some text here</textarea>'; // print text area element
 *
 *  ch_import('ChWsbEditor'); // import editor class
 *  $oEditor = ChWsbEditor::getObjectInstance(); // get default editor object instance
 *  if ($oEditor) // check if editor is available for using
 *      echo $oEditor->attachEditor ('#my_textarea', CH_EDITOR_STANDARD); // output HTML which will automatically apply editor to textarea element by its id
 * @endcode
 *
 */
class ChWsbEditor
{
    protected $_sObject;
    protected $_aObject;

    /**
     * Constructor
     * @param $aObject array of editor options
     */
    public function __construct($aObject)
    {
        $this->_sObject = $aObject['object'];
        $this->_aObject = $aObject;
    }

    /**
     * Get editor object instance by object name
     * @param $sObject object name
     * @return object instance or false on error
     */
    static public function getObjectInstance($sObject = false)
    {
        if (!$sObject)
            $sObject = getParam('sys_editor_default');

        if (isset($GLOBALS['chWsbClasses']['ChWsbEditor!'.$sObject]))
            return $GLOBALS['chWsbClasses']['ChWsbEditor!'.$sObject];

        $aObject = ChWsbEditorQuery::getEditorObject($sObject);
        if (!$aObject || !is_array($aObject))
            return false;

        if (empty($aObject['override_class_name']))
            return false;

        $sClass = $aObject['override_class_name'];
        if (!empty($aObject['override_class_file']))
            require_once(CH_DIRECTORY_PATH_ROOT . $aObject['override_class_file']);
        else
            ch_import($sClass);

        $o = new $sClass($aObject);

        return ($GLOBALS['chWsbClasses']['ChWsbEditor!'.$sObject] = $o);
    }

    /**
     * Get object name
     */
    public function getObjectName ()
    {
        return $this->_sObject;
    }

    /**
     * Get minimal width which is neede for editor for the provided view mode
     */
    public function getWidth ($iViewMode)
    {
        // override this function in particular editor class
    }

    /**
     * Attach editor to HTML element, in most cases - textarea.
     * @param $sSelector - jQuery selector to attach editor to.
     * @param $iViewMode - editor view mode: CH_EDITOR_STANDARD, CH_EDITOR_MINI, CH_EDITOR_FULL
     * @param $bDynamicMode - is AJAX mode or not, the HTML with editor area is loaded synamically.
     */
    public function attachEditor ($sSelector, $iViewMode, $bDynamicMode = false)
    {
        // override this function in particular editor class
    }

    /**
     * Add css/js files which are needed for editor display and functionality.
     */
    protected function _addJsCss ($bDynamicMode = false)
    {
        // override this function in particular editor class
    }

    /**
     * Replace provided markers string.
     * @param $s - string to replace markers in
     * @param $a - markers array
     * @return string with replaces markers
     */
    protected function _replaceMarkers ($s, $a)
    {
        if (empty($s) || empty($a) || !is_array($a))
            return $s;

        foreach ($a as $sKey => $sValue)
            $s = str_replace('{' . $sKey . '}', $sValue, $s);

        return $s;
    }

}
