<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

class ChWsbExceptionHandler
{
    protected $dontReport = [

    ];

    /**
     * @param Throwable $e
     */
    public function handle($e)
    {
        if (in_array(get_class($e), $this->dontReport)) {
            return;
        }

        if (!defined('CH_WSB_LOG_ERROR') || CH_WSB_LOG_ERROR) {
            $this->log($e);
        }

        $bFullError = (!defined('CH_WSB_FULL_ERROR')) ? false : CH_WSB_FULL_ERROR;
        $this->render($e, $bFullError);

        $bEmailError = (!defined('CH_WSB_EMAIL_ERROR')) ? true : CH_WSB_EMAIL_ERROR;
        if ($bEmailError) {
            $this->email($e);
        }
    }

    protected function log($e)
    {
        $s = "\n--- " . date('c') . "\n";
        $s .= "Type: " . get_class($e) . "\n";
        $s .= "Message: " . $e->getMessage() . "\n";
        $s .= "File: " . $e->getFile() . "\n";
        $s .= "Line: " . $e->getLine() . "\n";
        $s .= "Trace: \n";
        //$s .= nl2br($e->getTraceAsString());
        $s .= $this->getExceptionTraceAsString($e);

        file_put_contents($GLOBALS['dir']['tmp'] . 'error.log', $s, FILE_APPEND);
    }

    /**
     * @param Throwable $e
     * @param boolean   $bFullMsg display full error message with back trace
     */
    protected function render($e, $bFullMsg = false)
    {
        if ((php_sapi_name() === 'cli')) {
            // don't render errors when invoking from cli
            // they should get an email for errors
            return;
        }

        ob_start(); ?>
        <html>
        <body>
        <?php if (!$bFullMsg): ?>
            <div style="border:2px solid red;padding:4px;width:600px;margin:0px auto;">
                <div style="text-align:center;background-color:transparent;color:#000;font-weight:bold;">
                    <?= (function_exists('_t') ? _t('_Exception_user_msg') : 'A error occurred while loading the page. The system administrator has been notified.') ?>
                </div>
            </div>
        <?php else: ?>
            <div style="border:2px solid red;padding:10px;width:90%;margin:0px auto;">
                <h2 style="margin-top: 0px;"><?= (function_exists('_t') ? _t('_Exception_uncaught_msg') : 'An uncaught exception was thrown') ?></h2>
                <h3>Details</h3>
                <table style="table-layout: fixed;">
                    <tr>
                        <td style="font-weight: bold;">Type</td>
                        <td><?= get_class($e) ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Message</td>
                        <td><?= $e->getMessage() ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">File</td>
                        <td><?= $e->getFile() ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Line</td>
                        <td><?= $e->getLine() ?></td>
                    </tr>
                </table>
                <h3>Trace</h3>
                <code>
                    <!--<?= nl2br($e->getTraceAsString()) ?>-->
                    <?= nl2br($this->getExceptionTraceAsString($e)) ?>
                </code>
            </div>
        <?php endif; ?>
        </body>
        </html>
        <?php

        $sOutput = ob_get_clean();
        ch_show_service_unavailable_error_and_exit($sOutput);
    }

    /**
     * @param Throwable $e
     */
    protected function email($e)
    {
        $sMailBody = _t('_Exception_uncaught_in_msg') . " " . CH_WSB_URL_ROOT . "<br /><br /> \n";
        $sMailBody .= "Type: " . get_class($e) . "<br /><br /> ";
        $sMailBody .= "Message: " . $e->getMessage() . "<br /><br /> ";
        $sMailBody .= "File: " . $e->getFile() . "<br /><br /> ";
        $sMailBody .= "Line: " . $e->getLine() . "<br /><br /> ";
        //$sMailBody .= "Debug backtrace:\n <pre>" . htmlspecialchars_adv(nl2br($e->getTraceAsString())) . "</pre> ";
        $sMailBody .= "Debug backtrace:\n <pre>" . htmlspecialchars_adv(nl2br($this->getExceptionTraceAsString($e))) . "</pre> ";
        $sMailBody .= "<hr />Called script: " . $_SERVER['PHP_SELF'] . "<br /> ";
        $sMailBody .= "<hr />Request parameters: <pre>" . print_r($_REQUEST, true) . " </pre>";
        $sMailBody .= "--\nAuto-report system\n";

        if (!defined('CH_WSB_REPORT_EMAIl')) {
            global $site;
            $bugReportEmail = $site['bugReportMail'];
        } else {
            $bugReportEmail = CH_WSB_REPORT_EMAIL;
        }

        sendMail(
            $bugReportEmail,
            _t('_Exception_uncaught_in_msg') . " " . CH_WSB_URL_ROOT,
            $sMailBody,
            0,
            [],
            'html',
            false,
            true
        );
    }

    protected function getExceptionTraceAsString($exception)
    {
        $rtn = "";
        $count = 0;
        foreach ($exception->getTrace() as $frame) {
            $args = "";
            if (isset($frame['args'])) {
                $args = array();
                foreach ($frame['args'] as $arg) {
                    if (is_string($arg)) {
                        $args[] = "'" . $arg . "'";
                    } elseif (is_array($arg)) {
                        $args[] = "Array";
                    } elseif (is_null($arg)) {
                        $args[] = 'NULL';
                    } elseif (is_bool($arg)) {
                        $args[] = ($arg) ? "true" : "false";
                    } elseif (is_object($arg)) {
                        $args[] = get_class($arg);
                    } elseif (is_resource($arg)) {
                        $args[] = get_resource_type($arg);
                    } else {
                        $args[] = $arg;
                    }
                }
                $args = join(", ", $args);
            }
            $rtn .= sprintf(
                "#%s %s(%s): %s(%s)\n",
                $count,
                $frame['file'],
                $frame['line'],
                $frame['function'],
                $args
            );
            $count++;
        }
        return $rtn;
    }
}
