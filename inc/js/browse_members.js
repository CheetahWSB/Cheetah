/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

	function BrowsePage()
	{

		/**
		** @description : will redirtect with received URL;
		** @param		:  rObject (resource) link on object;
		** @param		:  sLocation (string) Url for link;
		** @return		: null;
		*/

		this.LocationChange = function( rObject, sLocation )
		{

			if ( rObject.checked == true || rObject.selected == true )
				window.location.href = sLocation + "&" +  rObject.name + "=" +  rObject.value;
			else
				window.location.href = sLocation;

		}

		/**
		** @description : function will show or hide received object;
		** @param 		: sObjectID (string) object's id;
		** @return		: null;
		*/

		this.ShowHideToggle = function( rObject )
		{

			var sChildID	= $(rObject).attr("bxchild");
			var sBlockState = $("#" + sChildID).css("display");

			if ( sBlockState == 'block' ){
				$("#" + sChildID).slideUp(300);
				$(rObject).css({ backgroundPosition : "0 -17px"});
			}
			else {
				$(rObject).css({ backgroundPosition : "0 0"});
				$("#" + sChildID).slideDown(300);
			}

		}

	}

	var oBrowsePage = new BrowsePage();
