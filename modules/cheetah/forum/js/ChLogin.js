/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

/**
 * login/join functionality
 */


/**
 * constructor
 */
function Login (base, forum)
{
	this._base = base;
	this._forum = forum;
}


/**
 * show login form
 *	@param id	forum id
 */
Login.prototype.showLoginForm = function ()
{
	this._forum.loading ('[L[LOADING LOGIN FORM]]');

	var $this = this;

	var h = function (r)
	{
		$this._forum.showHTML (r, 400, 200);

		$this._forum.stopLoading ();
	}

	new ChXslTransform(this._base + "?action=login_form", urlXsl + "login_form.xsl", h);

	return false;
}


/**
 * show join form
 */
Login.prototype.showJoinForm = function ()
{
	this._forum.loading ('[L[LOADING JOIN FORM]]');

	var $this = this;

	var h = function (r)
	{
		$this._forum.showHTML (r, 400, 200);

		$this._forum.stopLoading ();
	}

	new ChXslTransform(this._base + "?action=join_form", urlXsl + "join_form.xsl", h);

	return false;
}


/**
 * submit join form
 *	@param username	new username
 *	@param email	new email
 */
Login.prototype.joinFormSubmit = function (username, email)
{
	var $this = this;

	var h = function (r)
	{
		var o = new ChXmlRequest('','','');
		var ret = o.getRetNodeValue (r, 'js');
		if (!ret || !ret.length)
		{
			alert ("[L[Thank you! You Joined! Your login and password have been sent to your email.]]");
			$this._forum.hideHTML();
			return false;
		}

		alert ('[L[Join failed]]');

		eval (ret);

		return false;
	}

	new ChXmlRequest (this._base + "?action=join_submit&username="+username+"&email="+email, h, true);

	return false;
}

/**
 * submit login form
 *	@param username	login username
 *	@param pwd		login password
 */
Login.prototype.loginFormSubmit = function (username, pwd)
{
	var $this = this;

	var h = function (r)
	{
		var o = new ChXmlRequest('','','');
		var ret = o.getRetNodeValue (r, 'js');
		if (!ret || !ret.length)
		{
			document.location = $this._base + "?refresh=1";
			return false;
		}

		alert ('[L[Login failed]]');

		eval (ret);

		return false;
	}

	new ChXmlRequest (this._base + "?action=login_submit&username="+username+"&pwd="+pwd, h, true);

	return false;
}


/**
 * logout
 */
Login.prototype.logout = function ()
{
	$this = this;

	var h = function (r)
	{
		document.location = $this._base + "?refresh=1";
		return false;
	}

	new ChXmlRequest (this._base + "?action=logout", h, true);

	return false;

	document.cookie = 'orca_pwd=; orca_user=; expires=Fri, 02-Jan-1970 00:00:00 GMT';
	document.location = this._base + "?refresh=1";
	return false;
}
