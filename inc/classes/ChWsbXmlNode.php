<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

class ChWsbXmlNode
{
    var $name     = '';
    var $value    = '';
    var $children = array();

    function __construct( $name1 = '', $value1 = '' )
    {
        $this->name  = $name1;
        $this->value = $value1;
    }
    function addChild( $node )
    {
        if ( is_a($node, 'ChWsbXmlNode') )
            $this->children[] = $node;
    }
    function getXMLText()
    {
        $result = "<{$this->name}>";
        if ( empty($this->children) )
            $result .= $this->value;
        else
            foreach ( $this->children as $child )
                $result .= $child->getXMLText();
        $result .= "</{$this->name}>";
        return $result;
    }

    function GetXMLHtml()
    {
        $sRes = '<?xml version="1.0" encoding="UTF-8"?>' . $this->getXMLText();
        return $sRes;
    }
}
