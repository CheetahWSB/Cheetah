<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$aXmlTemplates = array (
    "ads" => array (
        1 => '<ads enabled="#1#" />',
        5 => '<ads enabled="#1#" banner="#2#" url="#3#" target="#4#" alpha="#5#" />'
    ),

    "setting" => array (
        3 => '<setting name="#1#" value="#2#" title="#3#" />'
    ),

    "file" => array (
        1 => '<file name="#1#" />',
        2 => '<file name="#1#" date="#2#" />',
        3 => '<file name="#1#"><![CDATA[#2#]]></file>'
    ),

    "current" => array (
        2 => '<current name="#1#" url="#2#" />'
    ),

    "widget" => array (
        8 => '<widget name="#1#" version="#2#" title="#3#" author="#4#" authorUrl="#5#" imageUrl="#6#" status="#7#" adminUrl="#8#" />'
    ),

    "result" => array (
        1 => '<result value="#1#" />',
        2 => '<result value="#1#" status="#2#" />',
        3 => '<result status="#1#" code="#2#" license="#3#" />',
        4 => '<result status="#1#" updated="#2#" updateLast="#3#"><![CDATA[#4#]]></result>'
    ),

    "application" => array (
        4 => '<app id="#1#" name="#2#" widgetId="#3#" banner="#4#" />'
    ),

    "caption" => array (
        1 => '<caption><![CDATA[#1#]]></caption>'
    ),

    "text" => array (
        1 => '<text><![CDATA[#1#]]></text>'
    ),

    "item" => array (
        2 => '<item key="#1#"><![CDATA[#2#]]></item>',
        3 => '<item type="file" key="#2#" value="" range="#3#">
                <caption><![CDATA[Permissions for #1#/#2#]]></caption>
                <comment><![CDATA[This file(directory) should have #3# permissions]]></comment>
              </item>'
    )
);
