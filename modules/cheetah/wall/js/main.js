/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$.fn.bxwallanim = function(action, effect, speed, h) {
   return this.each(function() {
   		var sFunc = '';
   		var sEval;

   		if (speed == 0)
   			effect = 'default';

  		switch (action) {
  			case 'show':
  				switch (effect) {
  					case 'slide': sFunc = 'slideDown'; break;
  					case 'fade': sFunc = 'fadeIn'; break;
  					default: sFunc = 'show';
  				}
  				break;
  			case 'hide':
  				switch (effect) {
  					case 'slide': sFunc = 'slideUp'; break;
  					case 'fade': sFunc = 'fadeOut'; break;
  					default: sFunc = 'hide';
  				}
  				break;
  			default:
  			case 'toggle':
  				switch (effect) {
  					case 'slide': sFunc = 'slideToggle'; break;
  					case 'fade': sFunc = ($(this).filter(':visible').length) ? 'fadeOut' : 'fadeIn'; break;
  					default: sFunc = 'toggle';
  				}
  		}

  		if ((0 == speed || undefined == speed) && undefined == h) {
  			sEval = '$(this).' + sFunc + '();';
  		}
  		else if ((0 == speed || undefined == speed) && undefined != h) {
  			sEval = '$(this).' + sFunc + '(); $(this).each(h);';
  		}
  		else {
  			sEval = '$(this).' + sFunc + "('" + speed + "', h);";
  		}
  		eval(sEval);

  		return this;
   });
};
