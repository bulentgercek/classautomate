$(function() {

    // sample 1 - login form
    $('#login_form #username').validator({
        format: 'alphanumeric',
        invalidEmpty: true,
        correct: function() {
            $('#login_form #vres_username').text('Thanks');
        },
        error: function() {
            $('#login_form #vres_username').text('Plese fill username field');
        }
    });
    $('#login_form #password').validator({
        format: 'alphanumeric',
        invalidEmpty: true,
        correct: function() {
            $('#vres_password').text('Thanks');
        },
        error: function() {
            $('#vres_password').text('Plese fill password field');
        }
    });


    // sample 2 - join form
    $('#join_form #username').validator({
        format: 'alphanumeric',
        invalidEmpty: true,
        correct: function() {
            $('#join_form #vres_username').text('Thanks');
        },
        error: function() {
            $('#join_form #vres_username').text('Plese fill your username');
        }
    });
    $('#join_form #password').validator({
        format: 'alphanumeric',
        invalidEmpty: true,
        correct: function() {
            $('#join_form #vres_password').text('Thanks');
        },
        error: function() {
            $('#join_form #vres_password').text('Plese fill password');
        }
    });
    $('#join_form #email').validator({
        format: 'email',
        invalidEmpty: true,
        correct: function() {
            $('#join_form #vres_email').text('Thanks');
        },
        error: function() {
            $('#join_form #vres_email').text('Please fill correct email');
        }
    });
    $('#join_form #date').validator({
        format: 'date',
        invalidEmpty: true,
        correct: function() {
            $('#vres_date').text('Thanks');
        },
        error: function() {
            $('#vres_date').text('Please fill correct date (in format: mm/dd/yyyy, mm-dd-yyyy, mm.dd.yyyy, mm dd yyyy)');
        }
    });

    // sample 3 - adding news form
    $('#news_form #title').validator({
        format: 'alphanumeric',
        invalidEmpty: true,
        correct: function() {
            $('#news_form #vres_title').text('Thanks');
        },
        error: function() {
            $('#news_form #vres_title').text('Plese fill title');
        }
    });
    $('#news_form #text').validator({
        minLength: 5,
        maxLength: 255,
        invalidEmpty: true,
        correct: function() {
            $('#news_form #vres_text').text('Thanks');
        },
        error: function() {
            $('#news_form #vres_text').text('Length of news text should be between 5 and 255 symbols');
        }
    });

});
