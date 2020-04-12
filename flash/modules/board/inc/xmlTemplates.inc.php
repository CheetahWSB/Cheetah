<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$aXmlTemplates = array (
    "user" => array (
        2 => '<user id="#1#" status="#2#" />',
        3 => '<user id="#1#" status="#2#" type="#3#" />',
        6 => '<user id="#1#" sex="#3#" age="#4#" photo="#5#" profile="#6#"><nick><![CDATA[#2#]]></nick></user>',
        8 => '<user id="#1#" status="#2#" sex="#4#" age="#5#" photo="#6#" profile="#7#"><nick><![CDATA[#3#]]></nick><desc><![CDATA[#8#]]></desc></user>'
    ),

    "result" => array (
        1 => '<result value="#1#" />',
        2 => '<result value="#1#" status="#2#" />'
    ),

    "savedBoard" => array (
        2 => '<board url="#1#"><title><![CDATA[#2#]]></title></board>'
    ),

    "board" => array (
        2 => '<board id="#1#" status="#2#" />',
        3 => '<board id="#1#" in="#2#" out="#3#" />',
        5 => '<board id="#1#" status="#2#" owner="#3#" password="#4#"><title><![CDATA[#5#]]></title></board>',
        6 => '<board id="#1#" status="#2#" owner="#3#" password="#4#" in="#5#"><title><![CDATA[#6#]]></title></board>'
    )
);
