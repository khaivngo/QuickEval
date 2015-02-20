
/**
 * Listens for user clicking submit, if clicked input values are checked,
 * if they matches the ajax will be run updating whether user have problem
 * with perceiving colours.
 */
$(document).ready(function() {
    $('#submit').click(function() {
        if (checkValues()) {
            $.ajax
                    ({
                        url: 'ajax/observer/updateColourBlindness.php',
                        async: false,
                        success: function(data) {

                        },
                        error: function(request, status, error) {
                            alert(request.responseText);
                        }
                    });
        }
    });
    setUpModeMenu('.observer-mode');

});
/**
 * Check if users input is the correct values
 * @returns {Boolean}
 */
function checkValues() {        //Input values matches images
    if ($('#picture1').val() == 12 && $('#picture2').val() == 5 && $('#picture3').val() == 8 && $('#picture4').val() == 26) {
        $('#submit-div').on('click', function() {
            $.Notify({             //notifies user
                content: "You appear to perceive colors normally",
                style: {background: '#32CD32'},
                width: '180px',
                height: '50px',
                timeout: '6000'
            });
        });
        setTimeout(function() {  //sends user back to main page after a short delay
            window.location = 'index.php';
        }, 3000);

        return true;
    }
    else {

        $('#submit-div').on('click', function() {
            $.Notify({          //notifies user
                content: "You might have problems with perceiving colors properly",
                style: {background: '#A9A9A9'},
                width: '180px',
                height: '50px',
                timeout: '6000'
            });
        });

        setTimeout(function() {     //sends user back to main page after a short delay
            window.location = 'index.php';
        }, 3000);
        return false;
    }
}



