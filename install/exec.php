<?php

/**
 * This work, "Cheetah - https://cheetah.deanbassett.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    if (!empty($_POST['phpinfo']))
        phpinfo();
    elseif (!empty($_POST['gdinfo']))
        echo '<pre>' . print_r(gd_info(), true) . '</pre>';

?>
<center>

    <form method=post>
        <input type=submit name=phpinfo value="PHP Info">
    </form>
    <form method=post>
        <input type=submit name=gdinfo value="GD Info">
    </form>

</center>
