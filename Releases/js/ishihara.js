
/*Khai Van Ngo  */


//when user has pushed the submit button:

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

function checkValues() {        //checks if user has input the right values
    if ($('#picture1').val() == 12 && $('#picture2').val() == 5 && $('#picture3').val() == 8 && $('#picture4').val() == 26) {
        $('#submit-div').on('click', function() {
            $.Notify({
                content: "<br><br>        You appear to perceive colours normally",
                style: {background: '#32CD32'},
                width: '200px',
                height: '120px',
                timeout: '6000'
            });
        });
        //sets a timeout so that feedback may be given before redirection
        setTimeout(function() {
            window.location = 'index.php';
        }, 3000);

        return true;
    }
    else {

        $('#submit-div').on('click', function() {
            $.Notify({
                content: "<br><br>        You might have a problem perceiving colours properly",
                style: {background: '#A9A9A9'},
                width: '200px',
                height: '120px',
                timeout: '6000'
            });
        });

        setTimeout(function() {
            window.location = 'index.php';
        }, 3000);
        return false;
    }
}



