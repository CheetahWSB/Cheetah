<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$sLangCategory = 'SMTP Mailer';

$aLangContent = array(
    '_sys_module_smtpmailer' => 'SMTP Mailer',
    '_ch_smtp_administration' => 'SMTP Mailer',
    '_ch_smtp' => 'SMTP Mailer',
    '_ch_smtp_tester' => 'SMTP Mailer Tester',
    '_ch_smtp_recipient' => 'Recipient',
    '_ch_smtp_subject' => 'Subject',
    '_ch_smtp_body' => 'Body',
    '_ch_smtp_is_html' => 'Is HTML?',
    '_ch_smtp_send_ok' => 'Mail has been successfully sent',
    '_ch_smtp_send_fail' => 'Mail send failed',
    '_ch_smtp_help' => 'Help',
    '_ch_smtp_help_text' => '
        <b>SMTP authentication </b> - most SMTP servers requires SMTP authentication, most probably you need to enable it.
        <br />
        <b>SMTP server name</b> - is it better to send mail from your site SMTP server, so it is probably the name of your site or localhost.
        <br />
        <b>SMTP username/SMTP password</b> - registered email account username and password, it is better to create different account for this purpose, like no-reply@mysite.com.
        <br />
        <b>From name of the message</b> - recipients will see this name as email sender, so it is probably your site name.
        <br />
        <b>Attach every outgoing email all files from kmailer/attach folder</b> - you can upload any files to <b>modules/cheetah/smtpmailer/data/attach</b> folder on your site and enable this option setting to attach uploaded files to every email.
        <br />
        <b>Is your SMTP server requires secure connection</b> - you can disable it if your hostname is localhost, secure connection is not needed if you send mails from localhost.
        <br />
        <b>Enable SMTP mailer</b> - it is possible to disable SMTP mailer at all, for example if you have temporary problems with your SMTP server.
        <br />
        <b>Override default sender email address</b> - set this the same as <b>SMTP username</b> email address, to avoid problems with mail marked as spam.
        <br />
        <br />
        Alternatively, you can get above options from your desktop mail client, if you are going to use the same email address for sending mail from your site.

        <hr />

        If emails still go to spam folder, check the following:
        <ul>
            <li>Try to set "Override default sender email address" field the same as "SMTP username" email address</li>
            <li>Make sure that "Override default sender email address" is valid and exists</li>
            <li>Try to change default email templates, it maybe that standard ones can be in spam filters, since they are used on a lot of sites by default</li>
            <li>Ask your members to add your site email to their contacts - when email is in user\'s contact list it always goes to inbox</li>
            <li><a href="https://wiki.cheetahwsb.com/index.php?title=Tweaking_Spam_Filters" target="_blank">This is great tutorial</a> on how to whitelist your server in many popular webmail services, including yahoo</li>
        </ul>
',

);
