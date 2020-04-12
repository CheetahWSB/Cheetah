<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

//  mail sending class

class ChMail extends Mistake
{
    var $_sSenderName = 'Orca Forum';

    /**
     * send mail with password
     * @param $p	email template variables to replace
     */
    function sendActivationMail (&$p)
    {
        global $gConf;

        $subj = "[L[Mail Subj Registration]]";

        $mailContent = <<<EOF
[L[Mail Body Registration]]
EOF;

        $p['site_url'] = $gConf['url']['base'];
        foreach ($p as $k => $v) {
            $mailContent = str_replace ('{'.$k.'}', $v, $mailContent);
        }

        $headers = "From: =?UTF-8?B?" . base64_encode($gConf['def_title']) . "?= <" . $gConf['email']['sender'] . ">\r\nContent-type: text/html; charset=UTF-8\r\n";
        $subj = '=?UTF-8?B?' . base64_encode($subj) . '?=';
        return mail ($p['email'], $subj, $mailContent, $headers, '-f'.$gConf['email']['sender']);
    }

}
