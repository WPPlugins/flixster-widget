/**
 *	Flixster Reviews is released under the GNU General Public License (GPL)
 *	http://www.gnu.org/licenses/gpl.txt
 *
 *  TODO: Load images when clicked in shadow mode
 */

//var is_ie = false;
var is_moz		= (!document.all && document.getElementById);
var is_opera	= (navigator.userAgent.indexOf("Opera") > -1);
var is_ie 		= ((document.all && document.getElementById) && (!is_opera));

/**
 * Returns an HTML element's opacity
 */
function fr_get_opacity(sID // Element ID
						) {
	oElt = window.document.getElementById(sID);
	if (is_ie) {
		return parseFloat(parseInt(oElt.filters.alpha.opacity) / 100);
	} else {
		return parseFloat(oElt.style.opacity);
	}
}

/**
 * Sets an HTML element's opacity
 */
function fr_set_opacity(sID, 	// Element ID
						iValue	// Opacity value
						) {
	oElt = window.document.getElementById(sID);						
	if (is_ie) {
		oElt.style.filter = 'alpha(opacity = ' + parseInt(iValue * 100) + ')';
	} else {
		oElt.style.opacity = iValue;
	}
}

/**
 * Shadows an HTML element
 */
function fr_shadow(	sID,			// Element ID 
					bDisappear, 	// Disappearing or appearing?
					bFromTimeout	// Called from setTimeout()?
					) {
	fOpacity = fr_get_opacity(sID);
	if (bDisappear) {
		if ((fOpacity != 1) && (!bFromTimeout)) {
			// Effect is already in progress.
			return;
		}
		if (fOpacity > 0) {
			fr_set_opacity(sID, fOpacity - 0.2);
			setTimeout('fr_shadow(\'' + sID + '\', '+ bDisappear + ', true)', 100);
		} else {
			window.document.getElementById(sID).style.display = 'none';
		}
	} else {
		if (fOpacity == 0) {
			window.document.getElementById(sID).style.display = 'block';
			if (bFromTimeout) {
				// Effect is already in progress.
				return;
			}
		}
		if (fOpacity < 1) {
			fr_set_opacity(sID, fOpacity + 0.2);
			setTimeout('fr_shadow(\'' + sID + '\', '+ bDisappear + ', true)', 100);
		}
	}
}
