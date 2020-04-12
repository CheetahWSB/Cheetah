<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    ch_import('ChWsbMenuSimple');

    /**
     * @see ChWsbMenuBottom;
     */
    class ChBaseMenuSimple extends ChWsbMenuSimple
    {
        /**
         * Class constructor;
         */
        function __construct()
        {
            parent::__construct();
        }

        /*
         * Generate navigation menu source
         */
        function getCode()
        {
            if(empty($this->aItems))
                $this->load();

            if(isset($GLOBALS['ch_profiler']))
                $GLOBALS['ch_profiler']->beginMenu(ucfirst($this->sName) . ' Menu');

            $sResult = $this->getItems();

            if(isset($GLOBALS['ch_profiler']))
                $GLOBALS['ch_profiler']->endMenu(ucfirst($this->sName) . ' Menu');

            return $sResult;
        }

        function getItems()
        {
            $aTmplVars = array();
            foreach($this->aItems as $aItem) {
                if(!$this->checkToShow($aItem))
                    continue;

                list( $aItem['Link'] ) = explode( '|', $aItem['Link'] );

                $aItem['Caption'] = _t($this->replaceMetas($aItem['Caption']));
                $aItem['Link'] = $this->replaceMetas($aItem['Link']);
                $aItem['Script'] = $this->replaceMetas($aItem['Script']);

                $aTmplVars[] = array(
                    'caption' => $aItem['Caption'],
                	'caption_attr' => ch_html_attribute($aItem['Caption']),
                	'icon' => $aItem['Icon'],
                    'link' => $aItem['Script'] ? 'javascript:void(0)' : $this->oPermalinks->permalink($aItem['Link']),
                    'script' => $aItem['Script'] ? 'onclick="' . $aItem['Script'] . '"' : null,
                    'target' => $aItem['Target'] ? 'target="_blank"' : null
                );
            }

            return $GLOBALS['oSysTemplate']->parseHtmlByName('extra_' . $this->sName . '_menu.html', array('ch_repeat:items' => $aTmplVars));
        }

        function getItemsArray($iLimit=1)
        {
            if(empty($this->aItems))
                $this->load();

            if(isset($GLOBALS['ch_profiler']))
                $GLOBALS['ch_profiler']->beginMenu(ucfirst($this->sName) . ' Menu');

            $iCount = 0;
            $aTmplVars = array();
            foreach ($this->aItems as $aItem) {
                if (!$this->checkToShow($aItem))
                    continue;

                $iCount++;
                if ($iCount > $iLimit)
                    break;

                list($aItem['Link']) = explode('|', $aItem['Link']);

                $aItem['Caption'] = _t($this->replaceMetas($aItem['Caption']));
                $aItem['Link'] = $this->replaceMetas($aItem['Link']);
                $aItem['Script'] = $this->replaceMetas($aItem['Script']);

                $aTmplVars[] = array(
                    'name' => $aItem['Name'],
                    'caption' => $aItem['Caption'],
                    'caption_attr' => ch_html_attribute($aItem['Caption']),
                    'icon' => $aItem['Icon'],
                    'link' => $aItem['Script'] ? 'javascript:void(0)' : $this->oPermalinks->permalink($aItem['Link']),
                    'script' => $aItem['Script'] ? 'onclick="' . $aItem['Script'] . '"' : null,
                    'target' => $aItem['Target'] ? 'target="_blank"' : null
                );
            }

            if(isset($GLOBALS['ch_profiler']))
                $GLOBALS['ch_profiler']->endMenu(ucfirst($this->sName) . ' Menu');

            return $aTmplVars;
        }
    }
