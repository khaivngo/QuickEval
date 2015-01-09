//-----------------STYLE SETTINGS/BACKGROUND COLOUR------------------------------------------------------------------------------------------

/**
 * Sends data to updateBackgroundColour.php for updating data in database
 */
function updateBackgroundColour() {
    $.ajax({
        type: 'POST',
        url: 'ajax/observer/updateBackgroundColour.php',
        data: {'backgroundColour': $('#background-colour').val()},
        success: function(data) {
            $.Notify({//notifies user about successfull email change
                content: "Background style updated",
                style: {background: 'lime'},
            });
        },
        error: function(request, status, error) {
            alert(request.responseText);
            console.log(request.responseText);
        }
    });
}