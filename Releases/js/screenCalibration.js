$(document).ready(function() {
    $('#submit').click(function() {
        $.ajax
                ({
                    async: false,
                    success: function(data) {
                        window.location = 'index.php';
                    },
                    error: function(request, status, error) {
                        alert(request.responseText);
                    }
                });
    });
    
    setUpModeMenu('.observer-mode');

});