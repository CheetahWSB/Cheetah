<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

/**
 * This class is needed to work with XML files.
 */
class ChXml
{
    function createParser()
    {
        return xml_parser_create("UTF-8");
    }

    /**
         * Get the value of specified attribute for specified tag.
         */
    function getAttribute($sXmlContent, $sXmlTag, $sXmlAttribute)
    {
        $rParser = $this->createParser();
        xml_parse_into_struct($rParser, $sXmlContent, $aValues, $aIndexes);
        xml_parser_free($rParser);

        $aFieldIndex = $aIndexes[strtoupper($sXmlTag)][0];
        return $aValues[$aFieldIndex]['attributes'][strtoupper($sXmlAttribute)];
    }

    /**
         * Get an array of attributes for specified tag or an array of tags with the same name.
         */
    function getAttributes($sXmlContent, $sXmlTagName, $sXmlTagIndex = -1)
    {
        $rParser = $this->createParser();
        xml_parse_into_struct($rParser, $sXmlContent, $aValues, $aIndexes);
        xml_parser_free($rParser);

        /**
         * gets two-dimensional array of attributes.
         * tags-attlibutes
         */
        if($sXmlTagIndex == -1) {
            $aResult = array();
            $aTagIndexes = $aIndexes[strtoupper($sXmlTagName)];
            if(count($aTagIndexes) <= 0) return NULL;
            foreach($aTagIndexes as $iTagIndex)
                $aResult[] = $aValues[$iTagIndex]['attributes'];
            return $aResult;
        } else {
            $iTagIndex = $aIndexes[strtoupper($sXmlTagName)][$sXmlTagIndex];
            return $aValues[$iTagIndex]['attributes'];
        }
    }

    /**
         * Get an array of tags or one tag if its index is specified.
         */
    function getTags($sXmlContent, $sXmlTagName, $iXmlTagIndex = -1)
    {
        $rParser = $this->createParser();
        xml_parse_into_struct($rParser, $sXmlContent, $aValues, $aIndexes);
        xml_parser_free($rParser);

        //--- Get an array of tags ---//
        if($iXmlTagIndex == -1) {
            $aResult = array();
            $aTagIndexes = $aIndexes[strtoupper($sXmlTagName)];
            if(count($aTagIndexes) <= 0) return NULL;
            foreach($aTagIndexes as $iTagIndex)
                $aResult[] = $aValues[$iTagIndex];
            return $aResult;
        } else {
            $iTagIndex = $aIndexes[strtoupper($sXmlTagName)][$iXmlTagIndex];
            return $aValues[$iTagIndex];
        }
    }

    /**
         * Gets the values of the given tag.
         */
    function getValues($sXmlContent, $sXmlTagName)
    {
        $rParser = $this->createParser();
        xml_parse_into_struct($rParser, $sXmlContent, $aValues, $aIndexes);
        xml_parser_free($rParser);

        $aTagIndexes = $aIndexes[strtoupper($sXmlTagName)];
        $aTagIndexes = isset($aTagIndexes) ? $aTagIndexes : array();
        $aReturnValues = array();
        foreach($aTagIndexes as $iTagIndex) {
            $aReturnValues[$aValues[$iTagIndex]['attributes']['KEY']] =
                isset($aValues[$iTagIndex]['value']) ? $aValues[$iTagIndex]['value'] : NULL;
        }
        return $aReturnValues;
    }

    /**
         * Sets the values of tag where attribute "key" equals to specified.
         */
    function setValues($sXmlContent, $sXmlTagName, $aKeyValues)
    {
        $rParser = $this->createParser();
        xml_parse_into_struct($rParser, $sXmlContent, $aValues, $aIndexes);
        xml_parser_free($rParser);

        $aTagIndexes = $aIndexes[strtoupper($sXmlTagName)];
        if(count($aTagIndexes) == 0) return $this->getContent();
        foreach($aTagIndexes as $iTagIndex)
            foreach($aKeyValues as $sKey => $sValue)
                if($aValues[$iTagIndex]['attributes']['KEY'] == $sKey) {
                    $aValues[$iTagIndex]['value'] = $sValue;
                    break;
                }
        return $this->getContent($aValues);
    }

    /**
         * Adds given values to XML content.
         */
    function addValues($sXmlContent, $sXmlTagName, $aKeyValues)
    {
        $rParser = $this->createParser();
        xml_parse_into_struct($rParser, $sXmlContent, $aValues, $aIndexes);
        xml_parser_free($rParser);

        $aTagIndexes = $aIndexes[strtoupper($sXmlTagName)];
        $iLastTagIndex = $aTagIndexes[count($aTagIndexes) - 1];
        $iAddsCount = count($aKeyValues);
        $iLevel = $aValues[$iLastTagIndex]["level"];

        for($i=count($aValues)-1; $i>$iLastTagIndex; $i--)
            $aValues[$i+$iAddsCount] = $aValues[$i];

        $i = $iLastTagIndex;
        foreach($aKeyValues as $sKey => $sValue) {
            $i++;
            $aValues[$i] = Array("tag" => $sXmlTagName, "type" => "complete", "level" => $iLevel, "attributes" => Array("KEY" => $sKey), "value" => $sValue);
        }
        return $this->getContent($aValues);
    }

    /**
         * get content in XML format from given values array
         */
    function getContent($aValues = array())
    {
        $sContent = "";
        foreach($aValues as $aValue) {
            $sTagName = strtolower($aValue['tag']);
            switch($aValue['type']) {
                case "open":
                    $sContent .= "<" . $sTagName . ">";
                    break;

                case "complete":
                    $sContent .= "<" . $sTagName;
                    if(isset($aValue['attributes']))
                        foreach($aValue['attributes'] as $sAttrKey => $sAttrValue)
                            $sContent .= " " . strtolower($sAttrKey) . "=\"" . $sAttrValue . "\"";
                    $sContent .= isset($aValue['value']) ? "><![CDATA[" . $aValue['value'] . "]]></" . $sTagName . ">" : " />";
                    break;

                case "close":
                    $sContent .= "</" . $sTagName . ">";
                    break;
            }
        }
        return $sContent;
    }
}

$oXml = new ChXml();

function xmlGetAttribute($sXmlContent, $sXmlTag, $sXmlAttribute)
{
    global $oXml;
    return $oXml->getAttribute($sXmlContent, $sXmlTag, $sXmlAttribute);
}

function xmlGetAttributes($sXmlContent, $sXmlTagName, $sXmlTagIndex = -1)
{
    global $oXml;
    return $oXml->getAttributes($sXmlContent, $sXmlTagName, $sXmlTagIndex);
}

function xmlGetTags($sXmlContent, $sXmlTagName, $iXmlTagIndex = -1)
{
    global $oXml;
    return $oXml->getTags($sXmlContent, $sXmlTagName, $iXmlTagIndex = -1);
}
function xmlGetValue($sXmlContent, $sXmlTagName, $sName)
{
    global $oXml;
    $aValues = $oXml->getValues($sXmlContent, $sXmlTagName);
    return isset($aValues[$sName]) ? $aValues[$sName] : "";
}
function xmlGetValues($sXmlContent, $sXmlTagName)
{
    global $oXml;
    return $oXml->getValues($sXmlContent, $sXmlTagName);
}
function xmlSetValue($sXmlContent, $sXmlTagName, $sName, $sValue)
{
    global $oXml;
    $aKeyValues = Array($sName => $sValue);
    return $oXml->setValues($sXmlContent, $sXmlTagName, $aKeyValues);
}
function xmlSetValues($sXmlContent, $sXmlTagName, $aKeyValues)
{
    global $oXml;
    return $oXml->setValues($sXmlContent, $sXmlTagName, $aKeyValues);
}
function xmlAddValues($sXmlContent, $sXmlTagName, $aKeyValues)
{
    global $oXml;
    return $oXml->addValues($sXmlContent, $sXmlTagName, $aKeyValues);
}
