
//Initializes listeners and lists
$(document).ready(function() {

    setUpModeMenu('.observer-mode');

    //Sets up search bars, populates lists and sets up event handlers
    setUpSearchFields();
    getExperimentTypes();
    getInstitutions();
    getOrganizations();
    getScientists('');
    getExperiments();
    setUpEventHandlers();
    checkInvite();
});

/**
 * Gets and adds all experimenttypes to list
 */
function getExperimentTypes() {
    var data = loadExperimentTypes();
    $('#select-method').append('<a class="list method all active" href="#">' +
        '<div class="list-content">' +
        '<span id="method" class="list-title">' + '&lt Display all &gt' + '</span>' +
        '</div>' +
        '</a>');

    for (var i = 0; i < data.length; i++) {
        $('#select-method').append('<a class="list method"  method-id = "' + data[i]['id'] + '" href="#">' +
            '<div class="list-content">' +
            '<span class="list-title">' + data[i]['name'] + '</span>' +
            '</div>' +
            '</a>');
    }
    $('#method-list').listview();
}

/**
 * Populates institute list based on institute search
 * Adds random institutes if field is empty
 * @param {type} $institute
 * @returns {undefined}
 */
function getInstitutions($institute) {

    $('#select-institution').append('<a class="list institute all active" href="#">' +
        '<div class="list-content">' +
        '<span id="institute" class="list-title">' + '&lt Display all &gt' + '</span>' +
        '</div>' +
        '</a>');
    $.ajax({
        url: 'ajax/observer/getInstitutes.php',
        data: {
            'institute': $institute,
            'type': 0
        },
        type: 'post',
        async: false,
        //contentType: "application/x-www-form-urlencoded;charset=ISO-8859-15",
        dataType: 'json',
        success: function(data) {
            $('#select-institution a:not(:first-child)').remove();
            if (data !== 0) {
                for (var i = 0; i < data.length; i++) {
                    $('#select-institution').append('<a class="list institute" href="#">' +
                        '<div class="list-content">' +
                        '<span id="institute' + i + '" class="list-title">' + data[i]['name'] + '</span>' +
                        '</div>' +
                        '</a>');
                }
            }
            $('#institute-list').listview();
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Populates organization-list based on organization search.
 * Adds random organizations if field is empty
 * @param {type} $organization The organization to be searched for
 * @returns {undefined}
 */
function getOrganizations($organization) {

    $('#select-organization').append('<a class="list organization all active" href="#">' +
        '<div class="list-content">' +
        '<span id="organization" class="list-title">' + '&lt Display all &gt' + '</span>' +
        '</div>' +
        '</a>');
    $.ajax({
        url: 'ajax/observer/getInstitutes.php',
        data: {
            'institute': $organization,
            'type': 1
        },
        type: 'post',
        async: false,
        dataType: 'json',
        success: function(data) {
            $('#select-organization a:not(:first-child)').remove();
            if (data !== 0) {

                for (var i = 0; i < data.length; i++) {
                    $('#select-organization').append('<a class="list organization" href="#">' +
                        '<div class="list-content">' +
                        '<span id="organization' + i + '" class="list-title">' + data[i]['name'] + '</span>' +
                        '</div>' +
                        '</a>');
                }
            }
            $('#organization-list').listview();
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Populates the scientist-list based on scientist search.
 * Adds random scientists if field is empty
 * @param {type} $scientist Scientist to be searched for
 * @returns {undefined}
 */
function getScientists($scientist) {
    $('#select-scientist').append('<a class="list scientist all active" href="#">' +
        '<div class="list-content">' +
        '<span id="scientist" class="list-title">' + '&lt Display all &gt' + '</span>' +
        '</div>' +
        '</a>');
    $.ajax({
        url: 'ajax/observer/getScientists.php',
        data: {
            'scientist': $scientist
        },
        type: 'post',
        async: false,
        dataType: 'json',
        success: function(data) {
            $('#select-scientist a:not(:first-child)').remove();
            if (data !== 0) {

                for (var i = 0; i < data.length; i++) {
                    $('#select-scientist').append('<a class="list scientist" href="#">' +
                        '<div class="list-content">' +
                        '<span id="scientist' + i + '" class="list-title">' + data[i]['lastName'] + ', ' +
                        data[i]['firstName'] + '</span>' +
                        '</div>' +
                        '</a>');
                }
            }
            $('#scientist-list').listview();
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Populates experiment list based on institute/organization/scientist and experiment search field.
 * Gets random experiments if no experiment is searched for
 * @param {type} $experiment Experiment to be searched for
 * @returns {undefined}
 */
function getExperiments($experiment) {

    var institute = $('#select-institution a.active:not(.all)').text();
    var organization = $('#select-organization a.active:not(.all)').text();
    var scientist = $('#select-scientist a.active:not(.all)').text();
    $.ajax({
        url: 'ajax/observer/getExperiments.php',
        data: {
            'experiment': $experiment,
            'organization': organization,
            'institute': institute,
            'scientist': scientist
        },
        type: 'post',
        async: false,
        dataType: 'json',
        success: function(data) {
            $('#select-experiment a').remove();
            emptyExperimentData();
            if (data !== 0) {
                for (var i = 0; i < data.length; i++) {
                    $('#select-experiment').append('<a class="list experiment" eid="' + data[i]['id'] +
                        '" method="' + data[i]['experimentType'] + '" href="#">' +
                        '<div class="list-content">' +
                        '<span id="experiment' + i + '" class="list-title">' + data[i]['title'] + '</span>' +
                        '</div>' +
                        '</a>');
                }
            }
        },
        complete: function() {
            displayByMethod($('#select-method').children('.active'));
            $('#select-experiment').listview().init();
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Gets experimentinfo and selected experiment using experimenthash via invite GET
 */
function checkInvite() {
    if ($('#invite').val() != undefined) {

        var invite;

        $.ajax({
            url: 'ajax/observer/checkInviteHash.php',
            data: {
                'invite': $('#invite').val()
            },
            type: 'post',
            async: false,
            dataType: 'json',
            success: function(data) {
                

                if (data !== 0) {

                    invite = data;
                    getExperimentData(data, 0, $('#invite').val());
                }

            },
            error: function(request, status, error) {
                alert(request.responseText);
                check = false;
            }
        });
    }
}

/**
 * Sets keyup functions for seach fields
 * @returns {undefined}
 */
function setUpSearchFields() {
    $('#institution-search').keyup(function() {
        getInstitutions($('#institution-search').val());
    });
    $('#organization-search').keyup(function() {
        getOrganizations($('#organization-search').val());
    });
    $('#scientist-search').keyup(function() {
        getScientists($('#scientist-search').val());
    });
    $('#experiment-search').keyup(function() {
        getExperiments($('#experiment-search').val());
    });
}

/**
 * Sets up event handlers for list-items, fields, notices and buttons
 * @returns {undefined}
 */
function setUpEventHandlers() {

    $(document.body).on('click', '#select-method a', function() {
        displayByMethod($(this));
    });

    //Click function for institute list-items
    $(document.body).on('click', '.institute', function() {
        $('.institute').removeClass('active');
        $(this).addClass('active');
        getExperiments();
    });

    //Click function for organization list-items
    $(document.body).on('click', '.organization', function() {
        $('.organization').removeClass('active');
        $(this).addClass('active');
        getExperiments();
    });

    //Click function for scientist list-items
    $(document.body).on('click', '.scientist', function() {
        $('.scientist').removeClass('active');
        $(this).addClass('active');
        getExperiments();
    });

    //Click function for experiment list-items
    $(document.body).on('click', '.experiment', function() {
        $('.experiment').removeClass('active');
        $(this).addClass('active');
        getExperimentData($('#select-experiment a.active:not(.all)').attr('eid')); //Gets experiment data
        checkColourBlind(); // from active list item
    });

    //Click function on colourblind button
    $(document.body).on('click', '.colourblind', function() {
        startColourblindTest();
    });

    //Click function on start experiment button
    $(document.body).on('click', '.start-experiment', function() {
        if (checkCustomFieldValues() == true) {
            startExperiment();
        }
    });

    //Click function on notices (displays when fields are not filled)
    $(document.body).on('click', '.notice', function() {
        $(this).fadeOut(function() {
            $(this).remove();
        })
    });

    //Click function on custom fields, removes notices
    $(document.body).on('click', '.custom-field', function() {
        $(this).siblings('.notice').fadeOut(function() {
            $(this).remove();
        });
    });

}

/**
 * Displays list of experiments by method
 * @param  {element} $el element selected, method
 */
function displayByMethod($el) {
    var id = $el.attr('method-id');

    if ($el.attr('method-id') == undefined) {
        $('[eid]').show();
    } else {
        $('[eid]:not([method=' + id + '])').hide();
        $('[eid][method=' + id + ']').show();
    }
}

/**
 * Gets and displays experiment data and "start experiment" or "ishihara" button
 * @param {type} $eId
 * @param {type} $type only returns data when 1
 * @returns {undefined}
 */
function getExperimentData($eId, $type, $invite) {
    var result = 0;
    $.ajax({
        url: 'ajax/observer/getExperimentData.php',
        data: {
            'experiment': $eId,
            'invite': $invite
        },
        type: 'post',
        async: false,
        dataType: 'json',
        success: function(data) {
            if (data[0] !== 0) {
                if ($type == 1) {
                    result = data[0];
                } else {
                    emptyExperimentData();
                    $('#experiment-title').append(data[0]['title']);
                    $('#experiment-info').append(data[0]['shortDescription']);
                    $('#experiment-text').append(data[0]['longDescription']);

                    if (data[1] !== 0) {
                        setUpCustomFields(data[1]);
                    }

                    if (data[0]['allowColourBlind'] == 0 && checkColourBlind() == false) {
                        $('#experiment-buttons').append('<button id="bottom-button" class="button success colourblind" style="margin 20px 0; float:right" ' +
                            'eid="' + data[0]['id'] + '">Start Colourblind Test</button>')
                    } else {
                        $('#experiment-buttons').append('<button id="bottom-buttom"' +
                            'class="button success start-experiment" style="margin 20px 0; float:right"' +
                            'eid="' + data[0]['id'] + '">Start Experiment</button>');
                    }
                }
            }

        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
    return result;
}

/**
 * Empties experiment data
 * @returns {undefined}
 */
function emptyExperimentData() {
    $('#experiment-title').empty();
    $('#experiment-info').empty();
    $('#experiment-text').empty();
    $('#experiment-buttons').empty();
    $('#input-fields').empty();
}

/**
 * Returns true if user is not colourblind
 * @returns {checkColourBlind.colourBlind}
 */
function checkColourBlind() {
    var colourBlind = getUserSession('colourBlindFlag');
    return (colourBlind == 1) ? true : false;
}

/**
 * Sets up custom fields from experiment
 * @param {type} $fields Array with fields
 * @returns {undefined}
 */
function setUpCustomFields($fields) {
    var f;
    for (var i = 0; i < $fields.length; i++) {
        f = $fields[i];
        $('#input-fields').append('<label>' + f['info'] + '</label>' +
            '<div class="input-control text" data-role="input-control" style="margin:5px 0">' +
            '<input class="custom-field" style="width:95%" type="text" fid="' + f['infoTypeId'] + '"placeholder="' + f['info'] + '" >' +
            '<span style="float:right; margin-top: 5px; font-size: 150%; font-weight: bold"> * </span>' +
            '</div>');
    }
}

/**
 * Adds a notice if a field is not filled.
 * @returns {undefined} Returns true if all fields are filled, false if not
 */
function checkCustomFieldValues() {
    var check = true;
    $('.notice').remove();
    $('.custom-field').each(function() {
        if ($(this).val() == "") {
            $(this).after('<div class="bg-red notice marker-on-top span1" style="margin:10px 0">' +
                'Field Is Empty' +
                '</div>');

            check = false;
        }
    });

    return check;
}

/**
 * Redirects to colourblindtest
 * @return {[type]} [description]
 */
function startColourblindTest() {
    var url = 'ishihara.php';
    var form = $('<form action="' + url + '" method="post" target="_blank">' +
        '<input type="hidden" />' +
        '</form>');
    $('body').append(form);
    $(form).submit();
}

/**
 * Starts experiment with invite url
 * @return {[type]} [description]
 */
function startExperiment() {
    saveFieldValues();
    var url;
    var data = getExperimentData($('.start-experiment').attr('eid'), 1);
    var invite = "";
    if ($('#invite').val() != undefined) {
        invite = '?invite=' + $('#invite').val();
    }
    var type = data['experimentType'];

    switch (type) {
        case '1':
            url = 'rankorderexperiment.php';
            break;

        case '2':
            url = 'pairComparisonExperiment.php';
            break;

        case '3':
            url = 'categoryexperiment.php';
            break;
    }

    url+=invite;    //adds invite hash if set

    var form = $('<form action="' + url + '" method="post" target="_blank">' +
        '<input type="hidden" name="experimentId" value="' + $('.start-experiment').attr('eid') + '" />' +
        '</form>');
    $('body').append(form);
    $(form).submit();
}

/**
 * Saves values from custom fields in database
 * @returns {Boolean}
 */
function saveFieldValues() {
    var eId = $('#bottom-buttom').attr('eid');
    var fieldValues = new Array();
    var check;

    //Add each field and its value to the array
    $('.custom-field').each(function() {
        fieldValues.push([$(this).attr('fid'), $(this).val()]);
    });


    $.ajax({
        url: 'ajax/observer/saveFieldValues.php',
        data: {
            'fieldValues': fieldValues,
            'experiment': eId
        },
        type: 'post',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data !== 0) {

                check = true;
            }

        },
        error: function(request, status, error) {
            alert(request.responseText);
            check = false;
        }
    });
    return check;
}