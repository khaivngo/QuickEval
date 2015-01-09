$(document).ready(function() {
    
    setUpModeMenu('.observer-mode');

    //Sets up search bars, populates lists and sets up event handlers
    setUpSearchFields();
    getInstitutions();
    getOrganizations();
    getScientists('');
    getExperiments();
    setUpEventHandlers();
});

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
    $.ajax
            ({
                url: 'ajax/observer/getInstitutes.php',
                data: {'institute': $institute,
                    'type': 0},
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
    $.ajax
            ({
                url: 'ajax/observer/getInstitutes.php',
                data: {'institute': $organization,
                    'type': 1},
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
    $.ajax
            ({
                url: 'ajax/observer/getScientists.php',
                data: {'scientist': $scientist},
                type: 'post',
                async: false,
                dataType: 'json',
                success: function(data) {
                    $('#select-scientist a:not(:first-child)').remove();
                    console.log(data);
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
    $.ajax
            ({
                url: 'ajax/observer/getExperiments.php',
                data: {'experiment': $experiment,
                    'organization': organization,
                    'institute': institute,
                    'scientist': scientist},
                type: 'post',
                async: false,
                dataType: 'json',
                success: function(data) {
                    $('#select-experiment a').remove();
                    emptyExperimentData();
                    if (data !== 0) {
                        for (var i = 0; i < data.length; i++) {
                            $('#select-experiment').append('<a class="list experiment" eid="' + data[i]['id'] +
                                    '" href="#">' +
                                    '<div class="list-content">' +
                                    '<span id="experiment' + i + '" class="list-title">' + data[i]['title'] + '</span>' +
                                    '</div>' +
                                    '</a>');
                        }
                    }
                },
                complete: function() {
                    $('#select-experiment').listview().init();
                },
                error: function(request, status, error) {
                    alert(request.responseText);
                }
            });
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
        getExperimentData($('#select-experiment a.active:not(.all)').attr('eid'));  //Gets experiment data
        checkColourBlind();                                                         // from active list item
    });

    //Click function on colourblind button
    $(document.body).on('click', '.colourblind', function() {
        window.location.href = 'ishihara.php';
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
 * Gets and displays experiment data and "start experiment" or "ishihara" button
 * @param {type} $eId
 * @returns {undefined}
 */
function getExperimentData($eId) {

    $.ajax
            ({
                url: 'ajax/observer/getExperimentData.php',
                data: {'experiment': $eId},
                type: 'post',
                async: false,
                dataType: 'json',
                success: function(data) {
                    if (data[0] !== 0) {
                        emptyExperimentData();
                        $('#experiment-title').append(data[0]['title']);
                        $('#experiment-info').append(data[0]['info']);
                        $('#experiment-text').append(data[0]['text']);

                        if (data[1] !== 0) {
                            setUpCustomFields(data[1]);
                        }

                        if (data['allowColourBlind'] == 0 && checkColourBlind() == false) {
                            $('#bottom-right-panel').append('<button id="bottom-button" class="button success large colourblind"' +
                                    'eid="' + data[0]['id'] + '">Start Colourblind Test</button>')
                        } else {
                            $('#bottom-right-panel').append('<button id="bottom-buttom"' +
                                    'class="button success large start-experiment" style="margin 10px 0"' +
                                    'eid="' + data[0]['id'] + '">Start Experiment</button>');
                        }
                    }

                },
                error: function(request, status, error) {
                    alert(request.responseText);
                }
            });
}

/**
 * Empties experiment data
 * @returns {undefined}
 */
function emptyExperimentData() {
    $('#experiment-title').empty();
    $('#experiment-info').empty();
    $('#experiment-text').empty();
    $('#bottom-right-panel').empty();
}

/**
 * Returns true if user is not colourblind
 * @returns {checkColourBlind.colourBlind}
 */
function checkColourBlind() {
    var colourBlind = getUserSession('colourBlindFlag');
    return (colourBlind === 0) ? true : false;
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
        $('#bottom-right-panel').append('<div class="input-control text" data-role="input-control" style="margin:15px 0">' +
                '<input class="custom-field" type="text" fid="' + f['id'] + '"placeholder="' + f['info'] + '" >' +
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

function startExperiment() {
    saveFieldValues();
    saveUserData();
}

/**
 * Saves values from custom fields in database
 * @returns {Boolean}
 */
function saveFieldValues() {
    var eId = $('.custom-field').parent('div').siblings('.button').attr('eid');
    var fieldValues = new Array();
    var check;
    //Add each field and its value to the array
    $('.custom-field').each(function() {
        fieldValues.push([$(this).attr('fid'), $(this).val()]);
    });

    $.ajax
            ({
                url: 'ajax/observer/saveFieldValues.php',
                data: {'fieldValues': fieldValues,
                    'experiment': eId},
                type: 'post',
                async: false,
                dataType: 'json',
                success: function(data) {
                    if (data !== 0) {
                        alert('Infofields updated');
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

function saveUserData() {
    var width = $(window).width();
    var height = $(window).height();
    var browserLanguage = navigator.language;
    var browserName = navigator.appName;
    var browserVersion = navigator.appVersion;
    var platform = navigator.platform;
    var browserCodeName = navigator.appCodeName;

    alert(browserCodeName + " " + platform + " " + browserVersion);
}