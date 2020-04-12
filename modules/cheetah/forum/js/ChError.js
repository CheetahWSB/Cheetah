/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

/**
 * Error handler object
 */


/**
 * constructor
 *		o - HTML Error object
 */
function ChError (o)
{
	alert(o.message + "\n" + o.description);
}

/**
 * constructor
 *		s1 - error message
 *		s2 - error description
 */
function ChError (s1, s2)
{
	alert(s1 + "\n" + s2);
}


