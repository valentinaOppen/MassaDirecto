$(document).ready(function() {
    
    $('#alert').hide();
    $("#formulario").on("submit", function(e) {
        
        $('#alert').hide();
        $('#responseMessage').text('');
        
        e.preventDefault();
        
        var theBtn = $('#btnEnviar');

        var data = $("#formulario").serialize();

        // needs for recaptacha ready
        grecaptcha.ready(function() {
            // do request for recaptcha token
            // response is promise with passed token
            grecaptcha.execute('6LehkpsUAAAAAK8vxzil8kjSXp5YZfQzZb__p7ze', {action: 'create_comment'}).then(function(token) {
                $.ajax({
                    url: 'http://www.massadirecto.com/api/mail.php',
                    type: 'POST',
                    data: data + "&token=" + token,
                }).done(function(data) {
                    //some code going here if success 
                    if(data.message) {
                        $('#responseMessage').text(data.message);
                        $('#alert').show(); 
                    }
                }).fail(function() {
                    if(data.message) {
                        $('#responseMessage').text(data.message);
                        $('#alert').show();
                    }
                });
            });
        });
    });
});