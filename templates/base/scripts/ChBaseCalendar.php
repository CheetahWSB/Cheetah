<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import ('ChWsbCalendar');

/**
 * @see ChWsbCalendar
 */
class ChBaseCalendar extends ChWsbCalendar
{
    function __construct ($iYear, $iMonth)
    {
        parent::__construct($iYear, $iMonth);
    }

    function display($isMiniMode = false)
    {
        $aVars = array (
            'month_prev_url' => $this->getMonthUrl(false, $isMiniMode),
            'month_next_url' => $this->getMonthUrl(true, $isMiniMode),
            'month_current' => $this->getTitle(),
        );
        $sTopControls = $GLOBALS['oSysTemplate']->parseHtmlByName('calendar' . ($isMiniMode ? '_mini' : '') . '_top_controls.html', $aVars);

        $aVars = array_merge($aVars, array (
            'top_controls' => $sTopControls,
            'ch_repeat:week_names' => $this->_getWeekNames ($isMiniMode),
            'ch_repeat:calendar_row' => $this->_getCalendar (),
            'bottom_controls' => $sTopControls,
        ));
        $sHtml = $GLOBALS['oSysTemplate']->parseHtmlByName($isMiniMode ? 'calendar_mini.html' : 'calendar.html', $aVars);
        $sHtml = preg_replace ('#<ch_repeat:events>.*?</ch_repeat:events>#s', '', $sHtml);
        $GLOBALS['oSysTemplate']->addCss(array('calendar.css', 'calendar_phone.css'));
        return $sHtml;
    }
}
