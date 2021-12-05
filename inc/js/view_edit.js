/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

// submits edit form located on view profile page
function submitViewEditForm(eForm, iPageBlockID, sSuccessUrl) {
    if( !eForm )
        return false;

    hideEditFormErrors( eForm );

    $(eForm).ajaxSubmit({
        success: function(sResponce) {
            try {
                var aErrors = eval(sResponce);
            } catch(e) {
                return false;
            }

            var bHaveErrors = doShowEditErrors( aErrors, eForm );

            if (!bHaveErrors) {
                loadDynamicBlock(iPageBlockID, sSuccessUrl);
                closeDynamicPopupBlock();
            }
        }
    } );

    return false;
}

function hideEditFormErrors( eForm ) {
    $( '.error', eForm ).removeClass( 'error' );
}

function doShowEditErrors( aErrors, eForm ) {
    if( !aErrors || !eForm )
        return false;

    var bHaveErrors = false;

    for( var iInd = 0; iInd < aErrors.length; iInd ++ ) {
        var aErrorsInd = aErrors[iInd];
        for( var sField in aErrorsInd ) {
            var sError = aErrorsInd[ sField ];
            bHaveErrors = true;

            doShowError( eForm, sField, iInd, sError );
        }
    }

    return bHaveErrors;
}

function doShowError( eForm, sField, iInd, sError ) {
    var $Field = $( "[name='" + sField + "']", eForm ); // single (system) field
    if( !$Field.length ) // couple field
        $Field = $( "[name='" + sField + '[' + iInd + ']' + "']", eForm );
    if( !$Field.length ) // couple multi-select
        $Field = $( "[name='" + sField + '[' + iInd + '][]' + "']", eForm );
    if( !$Field.length ) // couple range (two fields)
        $Field = $( "[name='" + sField + '[' + iInd + '][0]' + "'],[name='" + sField + '[' + iInd + '][1]' + "']", eForm );

    $Field.parents('.ch-form-element:first').addClass('ch-form-element-error').find('.ch-form-error > [float_info]').attr('float_info', sError);
    $Field.parents('.ch-form-element:first').find('.ch-form-error-div i').after(sError);    
}
