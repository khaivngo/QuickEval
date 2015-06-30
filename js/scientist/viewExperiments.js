$(document).ready(function () {
    $("#view-experiments").click(function () {
        setUpExperimentView();
        $('#export-parameters').hide();
    });
});

/**
 * Displays experiment on screen based on experimentid
 * @param  {int} $experimentId ID of experiment
 */
function viewExperiment($experimentId) {

    //Gets experimentdata
    var data = getExperimentById($experimentId);

    //Removes lists to display experiment
    $('.listview-outlook').siblings(':eq(0)').remove();
    $('.listview-outlook').remove();

    //Displays experimentdata
    $('#selected-experiment').show();
    $('#experiment-name').html('<a class="icon-arrow-left-3" style="margin-right: 10px"/>' + data['title']);
    $('#experiment-name').attr('experimentid', $experimentId);
    $('#experiment-type').html('Type: ' + data['experimentTypeName']);
    $('#experiment-description').html(data['longDescription']);


    //Gets URL
    var path = location.href.split('/');
    path.pop();
    path = path.join("/") + "/";

    var type = data['experimentType'];
    var url = 'index.php';

    //get overal data
    getExperimentStatisticsOneExperiment($experimentId);

    //Generated invite url based on hash
    var fullPath = path + url + '?invite=' + data['inviteHash']

    $('#experiment-url').html('Invite URL: <a href="' + fullPath + '" target = "_blank">' + fullPath + '</a>');

    printResults($experimentId);


    //Displays set hidden/public button
    if (data['isPublic'] == '1 = Public') {
        $('#set-hidden').show();
        $('#set-public').hide();
    } else {
        $('#set-public').show();
        $('#set-hidden').hide();
    }
}

/**
 * Sets up click listeners for list
 */
function setUpListListener() {
    $(document.body).off('click', '.list');
    $(document.body).on('click', '.list', function (e) {
        e.preventDefault();
        $('.list.active').removeClass('active');
        $(this).addClass('active');

        viewExperiment($(this).attr('experimentid'));
    });

    $(document.body).off('click', '#export-results');
    $(document.body).on('click', '#export-results', function () {
        var experimentId = $('#experiment-name').attr('experimentid');
        $('#export-parameters').slideUp();
        ($('#export-parameters').is(":visible")) ? window.open(getExportResultsURL(experimentId), '_blank') : $('#export-parameters').slideDown();
    });

    $(document.body).off('click', '.icon-arrow-left-3');
    $(document.body).on('click', '.icon-arrow-left-3', function (e) {
        e.preventDefault();
        $("#view-experiments").trigger('click');
    });

    $(document.body).off('click', '.group-title');
    $(document.body).on('click', '.group-title', function () {
        $(this).siblings().slideToggle();
        $(this).parent().toggleClass('collapsed');
    });

    $(document.body).off('click', '.group-title');
    $(document.body).on('click', '.group-title', function () {
        $(this).siblings().slideToggle();
        $(this).parent().toggleClass('collapsed');
    });

    //Opens and closes edit area labels on the x-axis
    $(document.body).off('click', '#change-label');
    $(document.body).on('click', '#change-label', function () {
        $("#graph-labels").slideToggle("slow", function () {
        });
    });
    //listens for when the user is ready to submit new label names
    $(document.body).on('click', '#submit-labels', function () {
        editLabels();
        prepareEditLabels();
        $("#graph-labels").slideUp();
    });


}

/**
 * Sets up experiment list displaying all experiments split by hidden and public
 */
function setUpExperimentList() {
    var element;

    getExperiments().forEach(function (t) {
        element = $('<a href="#" class="list" experimentid="' + t['id'] + '"><div class="list-content">' + t['title'] + '</div></a>');
        t['isPublic'] == "1 = Public" ? $('.public-experiments').append(element) : $('.hidden-experiments').append(element);
    });
}

/**
 * Sets up button listeners
 */
function setUpListeners() {

    $(document.body).off('click', '#delete-experiment');
    $(document.body).on('click', '#delete-experiment', function () {
        if ($('#confirm-delete-div').length == 0) {
            deleteExperimentConfirm();
        } else {
            $('#confirm-delete-div').remove();
        }
    });


    $(document.body).on('click', '#clear-results', function () {
        if ($('#clear-results-div').length == 0) {
            clearResultsConfirm();
        } else {
            $('#confirm-clear-results').remove();
        }
    });

    $(document.body).off('click', '#cancel-clear-results');
    $(document.body).on('click', '#cancel-clear-results', function () {
        $('#clear-results-div').remove().slideUp("fast");
    });

    $(document.body).off('click', '#confirm-clear-results');
    $(document.body).on('click', '#confirm-clear-results', function () {
        var experimentId = $('#experiment-name').attr('experimentid');
        clearResultsForExperiment(experimentId);
        console.log("clear results");
        $('#clear-results-div').remove().slideUp("fast");
    });


    $(document.body).off('click', '#export-experiment');
    $(document.body).on('click', '#export-experiment', function () {
        var experimentId = $('#experiment-name').attr('experimentid');
        generateSQLForExperiment(experimentId);
    });
    $(document.body).off('click', '#confirm-delete');
    $(document.body).on('click', '#confirm-delete', function () {
        var experimentId = $('#experiment-name').attr('experimentid');
        deleteExperiment(experimentId);
        setUpExperimentView();
    });

    $(document.body).off('click', '#set-hidden');
    $(document.body).on('click', '#set-hidden', function () {
        var experimentId = $('#experiment-name').attr('experimentid');
        setExperimentHidden(experimentId, 1);
    });

    $(document.body).off('click', '#set-public');
    $(document.body).on('click', '#set-public', function () {
        var experimentId = $('#experiment-name').attr('experimentid');
        setExperimentHidden(experimentId, 0);
    });
}

/**
 * Adds a confirm button beneath delete button
 */
function deleteExperimentConfirm() {
    $('#delete-experiment').after(
        '<br/><div id="confirm-delete-div" style="clear:both; float: left;">' +
        '<br/><span style="margin:10px 0" >Are you sure you want to delete this experiment?</span>' +
        '<br/><strong class="text-alert" style="margin:10px 0">NB: Results!</strong>' +
        '<br/><button id="confirm-delete" class="button danger sets" style="margin:10px 0; width: 140px">Confirm Delete</button>' +
        '</div>');
    $('#add-image').goTo();
}

/**
 * Adds a confirm and cancel buttons beneath clear experiment data
 */
function clearResultsConfirm() {
    $('#clear-results').after(
        '<div id="clear-results-div"><div id="" style="clear:both; float: left;">' +
        '<br/><span style="margin:10px 0" >Are you sure you want to ALL results for experiment?</span>' +
        '<br/><span class="text-alert" style="margin:10px 0" ><strong>This is NOT reversible!</strong></span>' +
            //'<br/><strong class="text-alert" style="margin:10px 0">NB: R</strong>' +
        '<br/><button id="confirm-clear-results" class="button danger sets" style="margin:10px 0; width: 140px">Confirm Delete</button>' +
        '&nbsp;<button id="cancel-clear-results" class="button success sets" style="margin:10px 0; width: 140px">CANCEL</button>' +
        '</div><br/><br/><br/><br/><br/></div>');
    //$('#add-image').goTo();
}

/**
 * Calls for deletion of ALL results connected to an experiment.
 * Data such as experiment setup, belonging instructions, images are preserved.
 * USE WITH CAUTION!
 * @param experimentId
 */
function clearResultsForExperiment(experimentId) {
    console.log(experimentId);
    $.ajax({
        url: 'ajax/observer/deleteOldResults.php',
        data: {
            'experimentId': experimentId,
            'check': 1
        },
        type: 'post',
        dataType: 'json',
        async: false,
        success: function (data) {
            $.Notify({ //notifies user about successfull experiment deletion change.
                content: "All experiment data cleared",
                style: {
                    background: 'lime'
                }
            });
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
}


//1 = hidden, 0 = public
/**
 * Sets an experiment hidden or public
 * @param {int} $experimentId Id of experiment
 * @param {int} $hidden       1 if set to hidden, 0 if set to public
 */
function setExperimentHidden($experimentId, $hidden) {
    var hiddenType = ($hidden == 0) ? '1 = Public' : '0 = Hidden';

    $.ajax({
        type: 'POST',
        url: 'ajax/scientist/setExperimentHidden.php',
        data: {
            'experimentId': $experimentId,
            'hidden': hiddenType
        },
        dataType: 'json',
        success: function (data) {
            if (data == 1) { //that particular experiment was successfully deleted.
                $.Notify({ //notifies user about successfull experiment deletion change.
                    content: "Experiment successfully set " + (($hidden == 1) ? "hidden" : "public"),
                    style: {
                        background: 'lime'
                    }
                });
                setUpExperimentView($experimentId);
            }
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Initilizes listeners, list displaying experiments
 */
function setUpExperimentView() {
    $.ajax({
        async: false,
        url: "ajax/scientist/viewExperiments.html",
        success: function (data) {

            //Initializes UI
            $($('#right-panel')).html(data);
            $('#right-menu').empty();
            $('#selected-experiment').hide();
            $('#set-hidden').hide();
            $('#set-public').hide();

            //Sets up UI and listeners

            setUpListListener();
            setUpExperimentList();
            setUpListeners();

        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
    setActive($(this));
}


function exportResults($experimentId) {
    $.ajax({
        type: 'POST',
        url: 'ajax/scientist/exportResults.php',
        data: {
            'experimentId': $experimentId
        },
        dataType: 'json',
        success: function (data) {

        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Gets and returns results for experiment
 * @param  {int} $experimentId id of experiment
 * @return {string}               array containing all results for experiment
 */
function getExperimentResults($experimentId) {
    var result;

    $.ajax({
        type: "post",
        url: 'ajax/scientist/getResults.php',
        async: false,
        data: {
            'experimentId': $experimentId
        },
        dataType: 'json',
        success: function (data) {
            result = data;
        },
        error: function (request, status, error) {
            alert(request.responseText);
            result = 0;
        }
    });

    return result;
}
/**
 * Displays all results on screen based on experiment type
 * @param  {int} $experimentId id of experiment
 */
function printResults($experimentId) {
    var data = getExperimentResults($experimentId);
    var experimentType = data[0];
    var experimentResults = data[1];

    var resultsArray;
    var zScoreArray = [];       //stores calculated data for one picture set
    var imageTitleArray = [];   //stores the title of each picture

    $('#experiment-results').empty(); //Empties list before filling


    //Shows warning if there are no results
    if (data == "") {
        $('#export-results').hide();
        $('#experiment-results').hide();
        $('#no-results-warning').show();

        //Displays all results
    } else {
        $('#export-results').show();
        $('#experiment-results').show();
        $('#no-results-warning').hide();

        loadHighChartsBoxPlot();    //loads graph calc with dummy data.

        $('#box-plot').after('<div id="zScores-container"></div>')      //appends container for the z-score tables.

        //If pairing experiment
        if (experimentType == 2) {
            var roundCounter = 0;   //used to identify each round and the belonging divs and elements

            //Loads table

            //Iterates through all imagesets and creates a table for each
            data[1].forEach(function (t, i) {
                //$('#experiment-results').append('<br/><h4>' + t['name'] + '</h4>');
                var div = $('<div></div>');

                //Loads table
                div.load('ajax/scientist/pairingExperimentTable.html', function () {
                    var element = $(this);

                    //console.log(element);

                    $("#zScores-container").append('</br></br><div id=raw-' + roundCounter + '><h1>Raw data</h1><hr></div>');


                    $("#raw-" + roundCounter + "").append('<table class="table bordered hovered">' +
                    '<thead>' +
                    '<tr class="header-list' + roundCounter + '">' +
                    '<th>' +
                    '<span class="hint-trigger icon-help" data-hint="Images on the y-axis are the images picked. For example if the value of image x and image y is 2,' +
                    'the image on the y axis is the one picked twice." data-hint-position="right" style="margin: 0 auto"></span>' +
                    '</th>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody class="result-list' + roundCounter + '">' +
                    '</tbody>' +
                    '</table>');


                    $('.hint-trigger').hint(); //Sets up hints as DOM is loaded

                    //Iterates through all images for the corresponding imageset
                    data[2][i].forEach(function (y, j) {


                        //Adds imagename to table
                        $('.header-list' + roundCounter + '').append('<th class="text-left" imageId=' + y['id'] + '>' + y['name'] + '</th>');

                        var tableRow = '<tr imageId=' + y['id'] + '><th>' + y['name'] + '</th>';

                        imageTitleArray.push(y['name']);        //saves each picture title into array for later use

                        //Adds empty cells for data
                        for (var ii = 0; ii < data[2][i].length; ii++) {
                            tableRow += '<td></td>';
                        }
                        //Appends row
                        $('.result-list' + roundCounter + '').append(tableRow + '</tr>');
                    });


                    //Initializes results array
                    resultsArray = new Array(data[2][i].length);

                    for (var it = 0; it < resultsArray.length; it++) {
                        resultsArray[it] = new Array(data[2][i].length);

                        for (ita = 0; ita < resultsArray[it].length; ita++) {
                            resultsArray[it][ita] = 0;
                        }
                    }


                    //var currentSet = -1; //Used to split points when no picture was chosen

                    //Iterates through all results
                    data[3][i].forEach(function (y, index) {

                        //If there are chosen pictures in results
                        if (y['chooseNone'] == null) {
                            pairAddPoints(y['won'], y['pictureId'], y['wonAgainst'], element, data[2][i], roundCounter);

                        } else { //If there is "choose none" as results

                            //Means points was not distributed last iteration
                            //if (currentSet != y['orderId']) {

                            pairAddPoints('0.5', y['pictureId'], y['wonAgainst'], element, data[2][i], roundCounter);

                            pairAddPoints('0.5', y['wonAgainst'], y['pictureId'], element, data[2][i], roundCounter);

                            //currentSet = y['orderId'];
                            //}
                        }

                        //--------------------------------------------------------

                        var row = arrayObjectIndexOf(data[2][i], y['pictureId'], 'id');
                        var column = arrayObjectIndexOf(data[2][i], y['wonAgainst'], 'id');

                        resultsArray[row][column] += 1;

                        //--------------------------------------------------------

                    });

                    calculatePlots(resultsArray);
                    //console.log("IMAGE URL: "+data['imageUrl'][i]['url']);

                    zScoreArray = calculatePlots(resultsArray);

                    //console.log(data['imageUrl'][i]['url']);
                    addSeries(imageTitleArray, zScoreArray, t['name']);        //Add experiments data to graph

                    $("#zScores-container").append('</br></br><h1>Z-Scores</h1><hr>');

                    //sends all imagestitles, calculated results and the name of picture set
                    setZScores(imageTitleArray, zScoreArray, t['name'], data['imageUrl'][i]['url']);
                    imageTitleArray = [];   //empties array for next picture set


                    highLightFirstTable();
                    activeSeriesClickListener();

                    roundCounter++;    //counts up for next round.
                });

                //prepareEditLabels();
                $('#experiment-results').append(div);

            });


            //If rank order
        } else if (experimentType == 1) {
            var roundCounter = 0;        //used to identify each round and the belonging divs and elements

            //For each imageset
            data[1].forEach(function (t, i) {

                var div = $('<div></div>');
                //Loads raw data table
                div.load('ajax/scientist/ratingExperimentTable.html', function () {
                    var element = $(this);

                    $("#zScores-container").append('</br></br><div id=raw-' + roundCounter + '><h1>Raw data</h1><hr></div>');

                    //each round/image set it appends a new table
                    $("#raw-" + roundCounter + "").append('<table class="table bordered hovered">' +
                    '<thead>' +
                    '<tr class="header-list' + roundCounter + '">' +
                    '<th>' +
                    ' <span class="hint-trigger icon-help" data-hint="The number represents the position the observer ranked the corresponding image on the x-axis."' +
                    'data-hint-position="right" style="margin: 0 auto"></span>' +
                    '</th>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody class="result-list' + roundCounter + '">' +
                    '</tbody>' +
                    '</table>');

                    $("#raw-" + roundCounter + " table").before('</br><h4>' + t['name'] + '</h4>'); //title of image set

                    $('.hint-trigger').hint(); //Sets up hints as DOM is loaded

                    //Iterates through all images for the corresponding imageset
                    data[2][i].forEach(function (y, j) {
                        $('.header-list' + roundCounter + '').append('<th class="text-left" imageId=' + y['id'] + '>' + y['name'] + '</th>');
                        imageTitleArray.push(y['name']);        //saves each picture title into array for later use
                    });

                    var rowIndex = 0; //defines which row of results, each row consists of images=n results

                    //Iterates through all results
                    for (var j = 0; j < data[3][i].length; j++) {

                        var currentRow = data[3][i][j]; //saves current row
                        var tableRow = '<tr><th>' + currentRow['person'] + '</th>'; //creates new row and adds observer name

                        //console.log("Object size = "+(Object.size(currentRow) - 2));
                        //Iterates through all results and adds them to row. -3 compensates for original?
                        for (var index = 0; index < (Object.size(currentRow) - 3); index++) {
                            tableRow += '<td>' + currentRow[index] + '</td>';
                            //console.log("Counter = "+dummy++);

                        }

                        //Ends and appends row
                        //element.find('.result-list'+roundCounter+'').append(tableRow + '</tr>');
                        $('.result-list' + roundCounter + '').append(tableRow + '</tr>');

                    }
                    //console.log(data);
                    var resultTable = convertRankToPair(data[3][i]);
                    calculatePlots(resultTable);
                    zScoreArray = calculatePlots(resultTable);

                    addSeries(imageTitleArray, zScoreArray, t['name']);        //Add experiments data to graph

                    $("#zScores-container").append('</br></br><div id=zscore-' + roundCounter + '><h1>Z-Scores</h1><hr></div>');

                    //sends all imagestitles, calculated results and the name of picture set
                    setZScores(imageTitleArray, zScoreArray, t['name'], data['imageUrl'][i]['url']);
                    imageTitleArray = [];   //empties array for next picture set

                    highLightFirstTable();
                    activeSeriesClickListener();

                    roundCounter++; //counts up for next round.
                });

                $('#experiment-results').append(div);
            });
            //If category experiment
        } else if (experimentType == 3) {
            var imageUrlIterator = 0;              //used to iterate an array containing original for each picture set
            var roundCounter = 0;     //used to identify each round and the belonging divs and elements

            //console.log(data);
            data['experimentOrders'].forEach(function (experimentOrder, l) {
                //$('#experiment-results').append('<h4>' + data[1][l]['name'] + '</h4>');

                //Loads table
                var div = $('<div></div>');
                var tableResult = [];

                div.load('ajax/scientist/categoryExperimentTable.html', function () {

                    $("#zScores-container").append('</br></br><div id=raw-' + roundCounter + '><h1>Raw data</h1><hr></div>');

                    $("#raw-" + roundCounter + "").append('<table class="table striped bordered hovered">' +
                    ' <thead>' +
                    '<tr class="table-coloumns' + roundCounter + '">' +
                    '<th class="text-left">Image Name</th>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody class="result-list' + roundCounter + '">' +
                    '</tbody>' +
                    '</table>');

                    $("#raw-" + roundCounter + " table").before('</br><h4>' + data[1][l]['name'] + '</h4>'); //title of image set

                    //Fills categories
                    data[3].forEach(function (category) {
                        $('.table-coloumns' + roundCounter + '').append('<th class="text-left" category=' + category['id'] + '>' + category['name'] + '</th>');
                    });

                    //Fills images, and result data
                    data[4].forEach(function (picture, index) {
                        var tableRow = [];
                        var row = $('<tr class=""></tr>');
                        row.append('<td>' + picture['name'] + '</td>');

                        imageTitleArray.push(picture['name']);        //saves each picture title into array for later use

                        //Adds empty cells
                        for (var i = 0; i < data[3].length; i++) {
                            row.append('<td></td>');
                        }

                        //Matches images to results, and adds all results to row
                        data[5][l].forEach(function (result, index2) {
                            if (picture['id'] == result['pictureId']) {
                                var index = $('[category=' + result['categoryId'] + ']').index();
                                row.children().eq(index).append(result['points']);
                            }
                        });

                        //Adds row to table
                        $('.result-list' + roundCounter + '').append(row);
                        row.children().each(function (index) {
                            if (index != 0) {
                                tableRow.push($(this).text() == "" ? 0 : parseInt($(this).text()));
                            }
                        });
                        tableResult.push(tableRow);
                        //console.log(tableRow);
                    });

                    // calculate z-scores for categoryjudgement
                    zScoreArray = calculatePlotsCategory(tableResult, true);
                    addSeries(imageTitleArray, zScoreArray, data[1][l]['name']);        //Add experiments data to graph

                    $("#zScores-container").append('</br></br><h1>Z-Scores</h1><hr>');

                    //sends all imagestitles, calculated results and the name of picture set
                    setZScores(imageTitleArray, zScoreArray, data[1][l]['name'], data['imageUrl'][imageUrlIterator]['url']);
                    imageTitleArray = [];   //empties array for next picture set

                    imageUrlIterator++;

                    highLightFirstTable();
                    activeSeriesClickListener();

                    roundCounter++; //counts up for next round.
                })
                //prepareEditLabels();
                $('#experiment-results').append(div);
            });
        }
    }
    return resultsArray;
}

//http://stackoverflow.com/questions/8668174/indexof-method-in-an-object-array
function arrayObjectIndexOf(myArray, searchTerm, property) {
    for (var i = 0, len = myArray.length; i < len; i++) {
        if (myArray[i][property] === searchTerm)
            return i;
    }
    return -1;
}

/**
 * Adds points to table based on parameters
 * @param  {int} $points      amount of points to add
 * @param  {int} $firstImage  id of image who won
 * @param  {int} $secondImage id of image who lost
 * @param  {JQuery object} $table       jquery object of table where images are
 * @param  {array} $array       array of image ids
 */
function pairAddPoints($points, $firstImage, $secondImage, $table, $array, $roundIterator) {
    var imageIndex = arrayObjectIndexOf($array, $firstImage, 'id');
    var wonAgainstIndex = arrayObjectIndexOf($array, $secondImage, 'id');


    //var resultList = $table.find('.result-list'+$roundIterator+'');

    var resultList = $('.result-list' + $roundIterator + '');
    var cell = resultList.find('tr:eq(' + imageIndex + ')').children().eq(wonAgainstIndex + 1);
    cell.html((cell.html() == "") ? parseFloat($points) : +cell.html() + parseFloat($points));
}


/**
 * @author  //http://stackoverflow.com/questions/5223/length-of-javascript-object-ie-associative-array
 * Returns size of object, how many keys
 * @param  {object} obj object
 * @return {int}     size
 */
Object.size = function (obj) {
    var size = 0,
        key;
    for (key in obj) {
        if (obj.hasOwnProperty(key))
            size++;
    }
    return size;
};

/**
 * Returns an url for export of experiments, based on parameters set using get data
 * @param  {int} $experimentId id of experiment
 * @return {string}               url of export
 */
function getExportResultsURL($experimentId) {
    var url = 'exportResults.php?id=' + $experimentId;
    ($('#parameter-metadata').find('input').prop('checked')) ? url += '&metadata=1' : '';
    ($('#parameter-parameters').find('input').prop('checked')) ? url += '&parameters=1' : '';
    ($('#parameter-instructions').find('input').prop('checked')) ? url += '&instructions=1' : '';
    ($('#parameter-image-sets').find('input').prop('checked')) ? url += '&image-sets=1' : '';
    ($('#parameter-observer-data').find('input').prop('checked')) ? url += '&observer-data=1' : '';
    ($('#parameter-input-field-data').find('input').prop('checked')) ? url += '&input-field-data=1' : '';
    ($('#parameter-results').find('input').prop('checked')) ? url += '&results=1' : '';
    ($('#parameter-complete').find('input').prop('checked')) ? url += '&complete=1' : '';
    return url;
}

function calculatePlots($frequencyMatrix, $category) {

	$frequencyMatrix = transpose($frequencyMatrix); //transposing in order to get correct z-score calculation

    var observerAmount = 0;
    var cumulativeFrequencyTable;
    var cumulativePercentageTable;

    //Calculates number of observers
    if ($category) { //Category uses non square matrix, counts first row results
        for (var i = 0; i < $frequencyMatrix[0].length; i++) {
            observerAmount += parseInt($frequencyMatrix[0][i]);
        }
    } else {
        observerAmount = $frequencyMatrix[0][1] + $frequencyMatrix[1][0];
    }

    if ($category) {
        cumulativeFrequencyTable = calculateCumulative($frequencyMatrix, true);
    }

    //Calculates a percentage matrix of the results
    var PercentageMatrix = calculatePercentageMatrix($category ? cumulativeFrequencyTable : $frequencyMatrix, observerAmount);

    //Calculates a LFMatrix of the results, using percentage matrix

    var LFMatrix = calculateLFMatrix($category ? cumulativeFrequencyTable : $frequencyMatrix, observerAmount, $category);

    //Parses the LF Matrix into a single row of results
    var LFMValues = parseLFMValues(LFMatrix, $category);


    //Calculates the Z-score values as a single array
    var ZScoreValues = calculateZScoreValues(PercentageMatrix, $category);


    var feedback = ZScoreValues[1];

    //Calculates the slope between LFM and Z-scores
    var slope = calculateSlope(ZScoreValues[0], LFMValues);

    //Calculates the Z-score matrix to be displayed using confidence interval
    var ZScoreMatrix = calculateZScoreMatrix(LFMatrix, slope['slope'], $category);

    //Calculates the mean z score values per image
    var meanZScore = calculateMeanZScore(ZScoreMatrix, $category).map(function (num) {
        return parseFloat(num.toFixed(3));
    });

    var standardDeviation = 1.96 * (1 / Math.sqrt(2)) / Math.sqrt(observerAmount);  //Must be changed

    var SDArray = calculateSDMatrix($frequencyMatrix);

    //Calculates the high confidence interval limits
    var highCILimit = meanZScore.map(function (num, i) {
        return parseFloat((num + SDArray[i]).toFixed(3));
    });

    //Calculates the low confidence interval limits
    var lowCILimit = meanZScore.map(function (num, i) {
        return parseFloat((num - SDArray[i]).toFixed(3));
    });

    /*
     console.log("frequencymatrix");
     console.log($frequencyMatrix);
     if($category) {
     console.log("cumulative table");
     console.log(cumulativeFrequencyTable);
     }
     console.log("observeramount");
     console.log(observerAmount);
     console.log("percentagematrix");
     console.log(PercentageMatrix);
     console.log("LFMValues");
     console.log(LFMValues);
     console.log("LFMatrix");
     console.log(LFMatrix);
     console.log("ZScoreValues");
     console.log(ZScoreValues);
     console.log("slope");
     console.log(slope);
     console.log("ZScoreMatrix");
     console.log(ZScoreMatrix);
     console.log("meanZScore");
     console.log(meanZScore);
     console.log("SDArray");
     console.log(SDArray);
     console.log("high and low limits");
     console.log(highCILimit);
     console.log(lowCILimit);
     console.log("feedback");
     console.log(feedback);
     console.log("result table");
     console.log([highCILimit, meanZScore, lowCILimit]);
     */

    return [lowCILimit, meanZScore, highCILimit, feedback];
}


function calculatePlotsCategory($frequencyMatrix, $category) {
    var observerAmount = 0;
    var cumulativeFrequencyTable;
    var cumulativePercentageTable;

    //Calculates number of observers
    if ($category) { //Category uses non square matrix, counts first row results
        for (var i = 0; i < $frequencyMatrix[0].length; i++) {
            observerAmount += parseInt($frequencyMatrix[0][i]);
        }
    } else {
        observerAmount = $frequencyMatrix[0][1] + $frequencyMatrix[1][0];
    }

    if ($category) {
        cumulativeFrequencyTable = calculateCumulative($frequencyMatrix, true);
    }

    //Calculates a percentage matrix of the results
    var PercentageMatrix = calculatePercentageMatrix($category ? cumulativeFrequencyTable : $frequencyMatrix, observerAmount);

    //Calculates a LFMatrix of the results, using percentage matrix

    var LFMatrix = calculateLFMatrix($category ? cumulativeFrequencyTable : $frequencyMatrix, observerAmount, $category);

    //Parses the LF Matrix into a single row of results
    var LFMValues = parseLFMValues(LFMatrix, $category);


    //Calculates the Z-score values as a single array
    var ZScoreValues = calculateZScoreValues(PercentageMatrix, $category);

    //Setting up identity matrix
    var eyeMatrix = im($frequencyMatrix[0].length - 1);

    //Setting up X matrix (code is a bit messy, but it works). Done accoring to the "Colour Engineering Toolbox" by Phil Green
    var X1 = [];
    X1 = matrix(cumulativeFrequencyTable.length * ($frequencyMatrix[0].length - 1), $frequencyMatrix[0].length - 1, 0);
    var roundCounter = 0;
    for (var j = 0; j < $frequencyMatrix[0].length - 1; j++) {
        for (var k = 0; k < cumulativeFrequencyTable.length; k++) {
            for (var i = 0; i < $frequencyMatrix[0].length - 1; i++) {
                X1[roundCounter][i] = eyeMatrix[j][i];
            }
            roundCounter++;
        }
    }


    var eyeMatrixNegative = imnegative(cumulativeFrequencyTable.length);
    var X2 = [];
    X2 = matrix(cumulativeFrequencyTable.length * ($frequencyMatrix[0].length - 1), eyeMatrixNegative.length, 0);

    var roundCounter = 0;
    for (var k = 0; k < $frequencyMatrix[0].length - 1; k++) {
        for (var j = 0; j < eyeMatrixNegative.length; j++) {
            for (var i = 0; i < eyeMatrixNegative.length; i++) {
                X2[roundCounter][i] = eyeMatrixNegative[j][i];
            }
            roundCounter++;
        }
    }

    var X = [];
    for (var k = 0; k < X2.length; k++) {
        X[k] = X1[k].concat(X2[k]);
    }

    var Xtemp = [];
    for (var i = 0; i < X[0].length; i++) {
        if (i < $frequencyMatrix[0].length - 1) {
            Xtemp[i] = 0;
        }
        else {
            Xtemp[i] = 1;
        }
    }
    X.push(Xtemp);

    //initial z-scores values extracted
    var v = ZScoreValues[0];

    //reformat V to fit with analysis later
    var vv = [];
    for (var j = 0; j < ($frequencyMatrix[0].length - 1); ++j) {
        for (var k = j; k < v.length; k += (PercentageMatrix[0].length)) {
            vv.push(v[k]);
        }
    }
    v = vv;
    v.push(0);

    var indexes = getAllIndexes(v, 3); //findng "infs"
    indexes = indexes.concat(getAllIndexes(v, -3)); //findng "infs" (also the negative ones)
    indexes.sort();

    for (var i = 0; i < indexes.length; i++) {
        v.splice(indexes[i] - i, 1);
        X.splice(indexes[i] - i, 1);
    }

    // least-squares solution
    var Xtransposed = [];
    Xtransposed = transpose(X);
    var Xtemp2 = [];
    Xtemp2 = matrix(X[0].length, X[0].length, 0);
    var OneDirection = [];
    var TwoDirection = [];
    for (var i = 0; i < X[0].length; i++) {
        for (var j = 0; j < X[0].length; j++) {
            for (var k = 0; k < Xtransposed[0].length; k++) {
                OneDirection[k] = X[k][j];
                TwoDirection[k] = Xtransposed[i][k];
            }
            Xtemp2[j][i] = dot_product(TwoDirection, OneDirection);	// X'*X
        }
    }


    var Xtemp3 = [];
    for (var i = 0; i < X[0].length; i++) {
        for (var k = 0; k < Xtransposed[0].length; k++) {
            TwoDirection[k] = Xtransposed[i][k]
        }
        Xtemp3[i] = dot_product(TwoDirection, v);	// X'*v
    }

    //including math.js to to inverse, sum and absolute.
    includeJs("/Quickeval-develop/js/scientist/math.js");

    //if we cannot invert Xtemp2, then we cannot calculate z-scores.  Does a check here, and then display an error message
    breakC = 0;
    var meanZScore = [];
    var lowCILimit = [];
    var highCILimit = [];
    for (var i = 0; i < Xtemp2[0].length; i++) {
        if (math.sum(math.abs(Xtemp2[i])) == 0) {
            breakC = 1;

            for (var j = 0; j < ZScoreValues[0].length; j++) {
                meanZScore[j] = 0; //setting values to 0 if we cannot invert Xtemp2
                lowCILimit[j] = 0;
                highCILimit[j] = 0;
            }
        }
    }

    if (breakC == 1) {
        window.alert("Not enough data to calculate Z-scores. In order to calculate z-scores at least one row needs to be complete. All values are set to '0'.");
    }
    else { //If we can invert it, then do the calculations.
        Ytemp = math.inv(Xtemp2);

        var Y = [];
        var ThreeDirection = [];
        for (var i = 0; i < X[0].length; i++) {
            for (var k = 0; k < Ytemp[0].length; k++) {
                ThreeDirection[k] = Ytemp[i][k];
            }
            Y[i] = dot_product(ThreeDirection, Xtemp3);
        }
        Y = Y.slice($frequencyMatrix[0].length - 1, Y.length); //the two first numbers are category boundaries, so they are not needed here.

        var feedback = ZScoreValues[1];

        //Formatting the z-scores to have 3 decimals
        var meanZScore = Y.map(function (num) {
            return parseFloat(num.toFixed(3));
        });

        //Finding standard deviation
        var standardDeviation = 1.96 * (1 / Math.sqrt(2)) / Math.sqrt(observerAmount);  //Must be changed

        var SDArray = calculateSDMatrix($frequencyMatrix);

        //Calculates the high confidence interval limits
        var highCILimit = meanZScore.map(function (num, i) {
            return parseFloat((num + SDArray[i]).toFixed(3));
        });

        //Calculates the low confidence interval limits
        var lowCILimit = meanZScore.map(function (num, i) {
            return parseFloat((num - SDArray[i]).toFixed(3));
        });

    }
    return [lowCILimit, meanZScore, highCILimit, feedback];

}

//Get indexes
function getAllIndexes(arr, val) {
    var indexes = [], i = -1;
    while ((i = arr.indexOf(val, i + 1)) != -1) {
        indexes.push(i);
    }
    return indexes;
}


//Function to include a JS file (needed for math.js) 
function includeJs(jsFilePath) {
    var js = document.createElement("script");

    js.type = "text/javascript";
    js.src = jsFilePath;

    document.body.appendChild(js);
}

//Function to calculate dot_product
function dot_product(ary1, ary2) {
    if (ary1.length != ary2.length)
        throw "can't find dot product: arrays have different lengths";
    var dotprod = 0;
    for (var i = 0; i < ary1.length; i++)
        dotprod += ary1[i] * ary2[i];
    return dotprod;
}

//Function to transpose a matrix
function transpose(a) {
    return Object.keys(a[0]).map(
        function (c) {
            return a.map(function (r) {
                return r[c];
            });
        }
    );
}

function matrix(rows, cols, defaultValue) {

    var arr = [];

    // Creates all lines:
    for (var i = 0; i < rows; i++) {

        // Creates an empty line
        arr.push([]);

        // Adds cols to the empty line:
        arr[i].push(new Array(cols));

        for (var j = 0; j < cols; j++) {
            // Initializes:
            arr[i][j] = defaultValue;
        }
    }

    return arr;
}

function im(n) {
    return Array.apply(null, new Array(n)).map(function (x, i, a) {
        return a.map(function (y, k) {
            return i === k ? 1 : 0;
        })
    });
}

function imnegative(n) {
    return Array.apply(null, new Array(n)).map(function (x, i, a) {
        return a.map(function (y, k) {
            return i === k ? -1 : 0;
        })
    });
}

//


function calculateCumulative($frequencyMatrix, $pop) {

    var cumulativeTable = [];

    for (var j = 0; j < $frequencyMatrix.length; j++) {

        var result = $frequencyMatrix[j].concat();

        for (var i = 0; i < $frequencyMatrix[j].length; i++) {
            result[i] = $frequencyMatrix[j].slice(0, i + 1).reduce(function (p, i) {
                return p + i;
            });
        }
        if ($pop) {
            result.pop(); //Removes last entry, as it is not wanted (will always be equal to number of observers)
        }
        cumulativeTable.push(result);
    }

    return cumulativeTable;
}


/**
 * Calculates a percentage matrix based on a n*n array of paired comparison results, and the amount of observers
 * @param $frequencyMatrix n*n matrix with paired comparison results
 * @param $observerAmount amount of observers who participated in the experiment
 * @returns {array} a n*n array, percentage matrix of paired comparison results
 */
function calculatePercentageMatrix($frequencyMatrix, $observerAmount) {

    //Performs a deep copy of the matrix, to void nested looping.
    var percentageMatrix = JSON.parse(JSON.stringify($frequencyMatrix));

    //Iterates through every row and column, then divides the frequency with amount of observers to
    // get a percentage
    for (var i = 0; i < percentageMatrix.length; i++) {
        for (var j = 0; j < percentageMatrix[i].length; j++) {
            percentageMatrix[i][j] /= $observerAmount;
        }
    }

    return percentageMatrix;
}

/**
 * Calculates a logistic function matrix of paired comparison results, to be further processed into z-scores.
 * @param $frequencyMatrix the frequency matrix with number of points for each image in a n*n array
 * @param $percentageMatrix the calculated percentage matrix in a n*n array
 * @param $observerAmount number of observations of each image
 * @returns {Array} the calculated logistic function matrix in a n*n array
 */
function calculateLFMatrix($frequencyMatrix, $observerAmount, $category) {
    var LFMatrix = new Array($frequencyMatrix.length);

    //Iterates through each row and column, performing a calculation on each cell to create the LFMatrix
    for (var i = 0; i < LFMatrix.length; i++) {
        LFMatrix[i] = new Array($frequencyMatrix[0].length);

        for (var j = 0; j < LFMatrix[i].length; j++) {
            //Empty cells (the diagonal through the table) shall stay empty

            if (i != j || $category) {
                //ln of ((same cell in FMatrix + 0,5) / (total observers - same cell in FMatrix + 0,5))
                LFMatrix[i][j] = Math.log(($frequencyMatrix[i][j] + 0.5) / ($observerAmount - $frequencyMatrix[i][j] + 0.5));

            } else {
                LFMatrix[i][j] = 0;
            }
        }
    }

    return LFMatrix;
}

/**
 * Parses a n*n two dimensional LFMatrix into a single array with n*n total cells, where the sequence is one column
 * from top to bottom, each value is added to the array, then next column.
 * @param $LFMatrix a n*n logistic function matrix
 * @returns {Array} a array with a length of observers^2 containing the LFMMatrix values
 */
function parseLFMValues($LFMatrix, $category) {

    var LFMValues = [];
    //console.log($LFMatrix);
    //Iterates through each cell and adds them to the LFMValues array
    for (var i = 0; i < $LFMatrix.length; i++) {
        for (var j = 0; j < $LFMatrix[i].length; j++) {

            //Skips empty cells (diagonal in table)
            if (i != j || $category) {
                LFMValues.push($category ? $LFMatrix[i][j] : $LFMatrix[j][i]);
            }
        }
    }
    return LFMValues;
}

/**
 * Calculates Z-score values based on a percentage matrix using the normsInv function
 * @param $percentageMatrix percentage matrix as a n*n array
 * @returns {Array} a single array with z-scores with a length of observations^2
 */
function calculateZScoreValues($percentageMatrix, $category) {
    var ZScoreValues = [];
    var feedback = 0; //If there has to be feedback to user based on results

    //Iterates through each cell and adds them to the ZScoreValues array
    for (var i = 0; i < $percentageMatrix.length; i++) {
        for (var j = 0; j < $percentageMatrix[i].length; j++) {

            //Skips empty cells (diagonal in table)
            if (i != j || $category) {
                if ($percentageMatrix[i][j] == 1) {
                    ZScoreValues.push(3);
                    feedback = 1;
                } else if ($percentageMatrix[i][j] == 0) {
                    ZScoreValues.push(-3);
                    feedback = 1;
                } else {
                    ZScoreValues.push(normsInv($category ? $percentageMatrix[i][j] : $percentageMatrix[j][i]));
                }
            }
        }
    }

    return [ZScoreValues, feedback];
}

/**
 * @author fuez at wilmott.com
 * @author http://www.wilmott.com/messageview.cfm?catid=10&threadid=38771
 * Calculates a coefficient based on the relationship between the inverse of the standard normal cumulative
 * distribution for the percentage matrix.
 * @returns {number} the coefficient, a z-score
 */
function normsInv(q) {
    if (q == .5)
        return 0;

    q = 1.0 - q;

    var p = (q > 0.0 && q < 0.5) ? q : (1.0 - q);
    var t = Math.sqrt(Math.log(1.0 / Math.pow(p, 2.0)));

    var c0 = 2.515517;
    var c1 = 0.802853;
    var c2 = 0.010328;

    var d1 = 1.432788;
    var d2 = 0.189269;
    var d3 = 0.001308;

    //Some magic going on here
    var x = t - (c0 + c1 * t + c2 * Math.pow(t, 2.0)) /
        (1.0 + d1 * t + d2 * Math.pow(t, 2.0) + d3 * Math.pow(t, 3.0));

    if (q > .5)
        x *= -1.0;

    return x;
}

/**
 * @author Trent Richardson
 * @author http://trentrichardson.com/2010/04/06/compute-linear-regressions-in-javascript/
 * @param y array of all known y values
 * @param x array of all known x values
 * @returns {{array}} array with slope, intercept and r2
 */
function calculateSlope(y, x) {
    var lr = {};
    var n = y.length;
    var sum_x = 0;
    var sum_y = 0;
    var sum_xy = 0;
    var sum_xx = 0;
    var sum_yy = 0;

	
		for (var i = 0; i < y.length; i++) {
			if(y[i]<3 && y[i] > -3)
			{
			sum_x += x[i];
			sum_y += y[i];
			sum_xy += (x[i] * y[i]);
			sum_xx += (x[i] * x[i]);
			sum_yy += (y[i] * y[i]);
			}
		}
	
    lr['slope'] = (n * sum_xy - sum_x * sum_y) / (n * sum_xx - sum_x * sum_x);
    lr['intercept'] = (sum_y - lr.slope * sum_x) / n;
    lr['r2'] = Math.pow((n * sum_xy - sum_x * sum_y) / Math.sqrt((n * sum_xx - sum_x * sum_x) * (n * sum_yy - sum_y * sum_y)), 2);

    return lr;
}

/**
 * Calculates Z-score matrix by multiplying the $LFMatrix with the slope
 * @param $LFMatrix the LFMatrix as a n*n array
 * @param $slope the slope as a decimal number
 * @returns {Array} a n*n array with Z-score values
 */
function calculateZScoreMatrix($LFMatrix, $slope, $category) {

    var ZScoreMatrix = new Array($LFMatrix.length);

    //Iterates through each cell and multiplies the current LFMatrix values with the slope value
    for (var i = 0; i < ZScoreMatrix.length; i++) {
        ZScoreMatrix[i] = [];


        for (var j = 0; j < $LFMatrix[i].length; j++) {

            //Only calculates values for non empty cells
            if (i != j || $category) {
                ZScoreMatrix[i][j] = $LFMatrix[i][j] * $slope;
            } else {
                ZScoreMatrix[i][j] = 0;
            }
        }
    }
    return ZScoreMatrix;

}

/**
 * Calculates the mean Z-score for each image, then returns the results as an array
 * @param $ZScoreMatrix Z-score matrix as a n*n array
 * @returns {Array} a single array returning all mean Z-scores where the results are ordered in parallel with images
 */
function calculateMeanZScore($ZScoreMatrix, $category) {

    var meanZScores = [];
    var totalZScore;

    //Iterates through all cells and finds the mean Z-score for each image by adding Z-scores then dividing by each
    // pair the image was a part of
    for (var j = 0; j < $ZScoreMatrix.length; j++) {

        totalZScore = 0;

        for (var i = 0; i < $ZScoreMatrix[0].length; i++) {


            totalZScore += $ZScoreMatrix[j][i];
        }

        meanZScores.push(totalZScore / ($ZScoreMatrix.length - 1));
    }

    return meanZScores;
}

/**
 * Calculates confidence intervals for each image used in the image set
 * @param $frequencyMatrix n*n matrix
 * @returns {Array} Array with n elements, one for each image, with confidence intervals for each respective image
 */
function calculateSDMatrix($frequencyMatrix) {

    var length = $frequencyMatrix.length;

    //Initialize array
    var SDArray = new Array(length);

    //Iterates through matrix
    for (var i = 0; i < length; i++) {
        var total = 0;

        //Iterates through the row as well as the column to sum the scores for each image
        for (var j = 0; j < length; j++) {
            total += $frequencyMatrix[i][j];
            total += $frequencyMatrix[j][i];
        }

        SDArray[i] = 1.96 * (1 / Math.sqrt(2)) / Math.sqrt(total / (length - 1));
    }

    return SDArray;

}


/**
 * Loads and initiates a box plot into the view experiment area
 * @param dataArray contains the data like z-scores
 * @param labelArray contains all labels on the axis
 */
function initiateHighCharts() {
    $('#box-plot-container').highcharts({
        chart: {
            zoomType: 'xy',
            events: {
                load: function () {

                }
            }
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: ['A', 'B', 'C'],
            title: {text: 'Reproductions'},
            labels: {
                formatter: function () {
                    return this.value.toString().substring(0, 15);
                }
            },
        },

        yAxis: {
            title: {
                text: 'Z-score'
            },
            ceiling: 5,             //defines displayed max
            floor: -5,              //defines displayed minimum
            tickInterval: 0.5,          //step interval
            allowDecimals: true,
            plotLines: [{
                value: 932, // whaat, Khai?
                color: 'red',
                width: 1,
                label: {
                    text: 'Theoretical mean: 932', // whaat, Khai?
                    align: 'center',
                    style: {
                        color: 'gray'
                    }
                }
            }]
        },

        series: [],

    });

    //$('#box-plot-containter').append("Where the box-plot goes");

}

/**
 * Loads the required html for the highchart plugin into the page
 */
function loadHighChartsBoxPlot() {
    //console.log('loading highchart');

    $('#experiment-results').append('<br><div id="box-plot"></div>');
    $('#box-plot').load("ajax/scientist/boxPlot.html", function () {
        initiateHighCharts();
        //prepareEditLabels();      //Moved after the chart is loaded
        $('#graph-labels').hide();              //hides/closes the element
    });


}

var imageSetCount = 0;
var imageSets = [];
function addSeries(imageTitleArray, zScoreArray, imageSetTitle) {
    var bufferArray = [];
    var meanValues = [];
    var highLows = [];
    var chart = $('#box-plot-container').highcharts(); //reference to the plot.


    for (var i = 0; i < imageTitleArray.length; i++) {    //makes a global array of image titles
        bufferArray.push(imageTitleArray[i]);
        //imageSets.push([imageSetCount][imageTitleArray[i]]);
    }

    imageSets.push(bufferArray);

    chart.setTitle({text: imageSetTitle});   //Sets the title (top of graph)
    chart.xAxis[0].setCategories(imageTitleArray); // Sets the title of the picture-sets at the x-axis

    for (var i = 0; i < zScoreArray[2].length; i++) {
        meanValues.push(zScoreArray[1][i]);  //Mean value
        highLows.push([zScoreArray[0][i], zScoreArray[2][i]]); //push high and low values. ready for th chart. 
    }

    chart.addSeries(
        {
            name: imageSetTitle,
            color: Highcharts.getOptions().colors['0'],
            type: 'scatter',
            //NYTT DATA FELT. 

            data: meanValues,
            marker: {
                fillColor: 'white',
                lineWidth: 3,
                radius: 4,
                lineColor: Highcharts.getOptions().colors['0']
            },
            tooltip: {
                headerFormat: '',
                pointFormat: 'Mean z-score: <b>{point.y}</b>'
            }
        }
    );

    chart.addSeries(
        {
            name: 'Observations error',
            type: 'errorbar',
            stemWidth: 3,
            whiskerWidth: 3,
            data: highLows,
            tooltip: {
                headerFormat: '<em>Reproduction {point.key}</em><br/>',
                pointFormat: 'Limit high: <b>{point.high}</b> <br>Limit low: <b>{point.low}</b>'
            }
        }
    );

    prepareEditLabels();
    imageSetCount++;
    //addButton

    hideAllButFirstSeries();
}

/**
 * Function hides all but one series on the graph.
 */
function hideAllButFirstSeries() {
    var series;
    var chart = $('#box-plot-container').highcharts(); //reference to the plot.
    var i = 0;

    $(chart.series).each(function () {
        //this.hide();
        this.setVisible(false, false);
    });

    chart.series[0].setVisible();
}

/**
 * Functions listens for when a data series are clicked and then highlights
 * the belonging z-scores table(s).
 */
function activeSeriesClickListener() {
    var id;
    var series;
    var chart = $('#box-plot-container').highcharts(); //reference to the plot.
    var color;
    var fontSize;

    //var images = [["bilde1","bilde2","bilde3"],["elefant1","elefant2","elefant3"],["Mus1","Mus2","Mus3"]];

    $(".highcharts-legend-item").click(function () {    //listens for when the user changes series selected series
        id = 0;
        $('.highcharts-legend-item').each(function () {
            color = $(this).find('text').css('color');      //reads property to determine if the series is selected

            if (color != "rgb(204, 204, 204)") {                //if series is selected, styles the correct table
                fontSize = parseInt($(this).css("font-size"));
                fontSize = fontSize + 3 + "px";                 //increases the font size

                $(".z-scores:eq( " + id + ")").css({"color": "blue"});
                $(".z-scores:eq( " + id + ") td").css({"font-size": fontSize});
                chart.xAxis[0].setCategories(imageSets[id]); // Sets the title of the picture-sets at the x-axis
            }
            else {              //when a series is not selected, reverts styling of the belonging table
                $(".z-scores:eq( " + id + ")").css({"color": "black"});
                $(".z-scores:eq( " + id + ") td").css({"font-size": "-4"});
            }

            id++;
        });


    });
}

/**
 * Highlights the first table since the first series is highlighted.
 */
function highLightFirstTable() {
    var fontSize;

    fontSize = parseInt($(".z-scores:eq( " + 0 + ")").css("font-size"));    //reads font-size property of first matching table
    fontSize = fontSize + 3 + "px";
    //changes color and size on the font:
    $(".z-scores:eq( " + 0 + ")").css({"color": "blue"});
    $(".z-scores:eq( " + 0 + ") td").css({"font-size": fontSize});
}


/**
 * Retrieves the new values for the labels and calls for reiniating of graph with new labels
 */
function editLabels() {

    // Retrieves all x-axis labels and puts into an array:
    var arr = new Array();
    $("#graph-labels input").each(function (index) {
        var newLabelName = $(this).val();
        arr.push(newLabelName);
    });

    // The graph, as set with GraphCalc:
    var chart = $('#box-plot-container').highcharts();

    // Reiniating the graph with new labels:
    chart.xAxis[0].setCategories(arr);
    chart.xAxis[0].setTitle({text: $("#xLabelInput").val()});
    chart.yAxis[0].setTitle({text: $("#yLabelInput").val()});
    chart.setTitle({text: $("#titleInput").val()});
    chart.setTitle(null, {text: $("#subTitleInput").val()});

}

/**
 * Preloads input fields into element for a smooth animation
 */
function prepareEditLabels() {
    $("#graph-labels fieldset").append('<p>Results </p> ');

    // For each label on the x-axis, make an input field:
    $(".highcharts-xaxis-labels text").each(function (index) {
        var labelName = $(this).text();
        //console.log(labelName);

        $("#graph-labels fieldset").append('<div class="input-control text span2" data-role="input-control">' +
        '<input type="text" value="' + labelName + '" placeholder=""/>' +
        '</div> <br/>');

    });

    var chart = $('#box-plot-container').highcharts(); //reference to the plot.
    var xLabell = chart.xAxis[0].categories[0];
    //chart.series.data[].category
    //chart.xAxis[0].categories[0]


    // Makes a input field for a Title of the x-axis:
    var xLabel = $(".highcharts-xaxis-title").text();
    $("#graph-labels fieldset").append('<hr style=" margin-top: 0.5em; margin-bottom: 0.5em; margin-left: auto; margin-right: auto; border-style: inset; border-width: 2px;"> ' +
    '<p>X-axis</p>' +
    '<div class="input-control text span2" data-role="input-control">' +
    '<input type="text" id="xLabelInput" value="' + xLabel + '" placeholder=""/>' +
    '</div> <br/>');

    // Makes a input field for the y-axis:
    var yLabel = $(".highcharts-yaxis-title").text();
    $("#graph-labels fieldset").append('<hr style=" margin-top: 0.5em; margin-bottom: 0.5em; margin-left: auto; margin-right: auto; border-style: inset; border-width: 2px;"> ' +
    '<p>Y-axis</p>' +
    '<div class="input-control text span2" data-role="input-control">' +
    '<input type="text" id="yLabelInput" value="' + yLabel + '" placeholder=""/>' +
    '</div> <br/>');

    // Makes a input field for Title of the graph:
    var plotTitle = $(".highcharts-title").text();
    $("#graph-labels fieldset").append('<hr style=" margin-top: 0.5em; margin-bottom: 0.5em; margin-left: auto; margin-right: auto; border-style: inset; border-width: 2px;"> ' +
    '<p>Plot title</p>' +
    '<div class="input-control text span4" data-role="input-control">' +
    '<input type="text" id="titleInput" value="' + plotTitle + '" placeholder=""/>' +
    '</div> <br/>');

    // Makes a input field for Subtitle of the graph:
    var plotSubTitle = $(".highcharts-subtitle").text();
    $("#graph-labels fieldset").append('<hr style=" margin-top: 0.5em; margin-bottom: 0.5em; margin-left: auto; margin-right: auto; border-style: inset; border-width: 2px;"> ' +
    '<p>Subtitle</p>' +
    '<div class="input-control text span4" data-role="input-control">' +
    '<input type="text" id="subTitleInput" value="' + plotSubTitle + '" placeholder=""/>' +
    '</div> <br/>');

}


var tableId = 0;
/**
 * Goes through analyzed results for one picture set and sets them into a table
 * @param imageTitleArray contains all pictures for one picture set
 * @param zScoresArray contains all calculated data for each picture in the picture set
 * @param pictureSetTitle the title of the picture set
 */
function setZScores(imageTitleArray, zScoresArray, pictureSetTitle, imageUrl) {

    tableDivHeader(tableId, pictureSetTitle, zScoresArray[3], imageUrl);       //inserts new table with picture set title

    var rowCount = $('#z-scores tr').length;       //count whether there is any existing rows in table

    if (rowCount > 0) {         //if true it means that there exist data in table,
        // appends a empty row to distinguish between multiple picture sets
        $('#z-scores').append('<tr><td class="right">---</td><td class="right">---</td><td class="right">---</td><td class="right">---</td></tr>');
    }


    //appends title of picture set
    //loops through array and for each picture prints title, low ci limit, mean z-score and high ci limit in that order.
    for (var i = 0; i < imageTitleArray.length; i++) {

        $('#' + tableId + '').append('<tr><td><b>' + imageTitleArray[i] + '</b></td><td class="right">' + zScoresArray[0][i] + '</td><td class="right">' + zScoresArray[1][i] + '</td><td class="right">' + zScoresArray[2][i] + '</td></tr>')
    }

    tableId++;      //counter used to identify each new table
}

/**
 * Function adds table and title of table each time it is called.
 * @param tableId the identifier of the table, used to insert data.
 * @param pictureSetTitle title of the picture set.
 */
function tableDivHeader(tableId, pictureSetTitle, check, imageUrl) {

    //when there is not enough data to properly calculate z-scores it sets a hint to explain it to the user

    console.log("URL = " + imageUrl);

    if (check == 1) {
        //appends the table right before the title of the table displaying raw data
        $("#zScores-container").append('<br/><div>' +
        '<h4>Z-Scores: ' + pictureSetTitle + '<span class="hint-trigger" data-hint="Need more observer-data to be calculated properly" data-hint-position="right"><i class="icon-info on-right"></i></h4>' +
        '<img src="' + imageUrl + '" alt="Original picture" height="20%" width="20%"> ' +
        '<br/>' +
        '<br/>' +
        '<table class="table bordered hovered z-scores">' +
        '<thead>' +
        '   <tr>' +
        ' <th class="text-left">Title</th>' +
        '<th class="text-left">Low CI limit</th>' +
        '<th class="text-left">Mean z-score</th>' +
        ' <th class="text-left">High CI limit</th>' +
        ' </tr>' +
        '</thead>' +
        '<tbody id="' + tableId + '">' +
        '   </tbody>' +
        '   <tfoot></tfoot>' +
        '</table>' +
        '</div>');
    }
    else {         //when there is enough data to properly calculate z-scores
        $("#zScores-container").append('<br/><div>' +
        '<h4>Z-Scores: ' + pictureSetTitle + '</h4>' +
        '<img src="' + imageUrl + '" alt="Original picture" height="20%" width="20%"> ' +
        '<br/>' +
        '<br/>' +
        ' <table class="table bordered hovered z-scores">' +
        '<ad>' +
        ' <th class="text-left">Title</th>' +
        '<th class="text-left">Low CI limit</th>' +
        '<th class="text-left">Mean z-score</th>' +
        ' <th class="text-left">High CI limit</th>' +
        ' </tr>' +
        '</thead>' +
        '<tbody id="' + tableId + '">' +
        '   </tbody>' +
        '   <tfoot></tfoot>' +
        '</table>' + '</div>');
    }

    //uses error functionality to hide img tags where the picture does not exist.
    $("img").error(function () {

        $(this).unbind("error").hide();

    });
}


/**
 * Creates a pair comparison result matrix from ranking data where table[i][j] refers to the number of times image[i] was chosen over image[j]k
 * @param $data Rank order data where data[person][imageRank] corresponds to the rank of one image
 * @returns {Array} Result matrix
 */
function convertRankToPair($data) {
    // Determines the number of images in the image set
    var max = 0;
    while (typeof $data[0][max] != 'undefined') max++;

    var table = [];
    for (var x = 0; x < max; x++)
        table[x] = [];

    // For each person, increment each cell[i][j] where image i has a higher rank than j
    for (var i = 0; i < max; i++) {
        for (var j = 0; j < max; j++) {
            table[i][j] = 0;
            $data.forEach(function (person, k) {
                if (person[i] < person[j])
                    table[i][j]++;
            });
        }
    }
    return table;
}

function getExperimentStatisticsOneExperiment($experimentId) {
    var totalElements;
    $.ajax
    ({
        url: 'ajax/scientist/getExperimentStatisticsOneExperiment.php',
        async: false,
        data: {experimentId: $experimentId},
        type: 'post',
        success: function (data) {
            //checks how many experiments are returned:
            data = JSON.parse(data);

            //intermediate saving of data for a more clear view of what is happening.
            var title1 = data.title1;
            var visitors1 = data.visitors1[0].total;
            var completion1 = data.completion1[0].total;
            var average1 = data.average1;

            (average1 === undefined) ? average1 = "N/A" : average1;

            //adding information to table
            $('#table3').html('<table><tr><td>' + 'Visitors' + '</td><td>' + 'Completed' + '</td><td>' + 'Average time' + '</td></tr><tr><td>' + visitors1 + '</td><td>' + completion1 + '</td><td>' + average1 + '</td></tr></table>');

        },
        error: function (xhr, ajaxOptions, thrownError) {

        }


    });
}
