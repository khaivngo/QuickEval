
var dataSource = new Array();

$(document).ready(function() {
//Refer to http://chartjs.devexpress.com/Documentation/Tutorial/Configure_Charts?version=13_2
// for documentation.

    $(function()
    {
        $("#chartContainer").dxChart({
            dataSource: dataSource,
            commonSeriesSettings: {
                argumentField: "type",
                type: "stackedBar"
            },
            valueAxis: {
                valueType: 'numeric',
                min: 0,
                tickInterval: 1,
                title: {
                },
                position: "right"
            },
            series: [
                {valueField: "public", name: "Public experiments"},
                {valueField: "hidden", name: "Hidden experiments"}
            ],
            title: "Number of experiments",
            legend: {
                verticalAlignment: "bottom",
                horizontalAlignment: "center",
                itemTextPosition: 'top'
            },
            tooltip: {
                enabled: true,
                customizeText: function() {
                    return this.seriesName + ": " + this.valueText;
                }
            }
        });
    }
    );

//-----------------------------------------------------------------------------------------------------------------------------------------

    getTotalExperiments();
    getExperimentStatistics();

});


/**
 * Inserts data into Chartjs's function.
 * @param {type} dataRank total experiments of Rank order.
 * @param {type} hiddenRank how many Rank order experiments are hidden.
 * @param {type} dataPair total experiments of Pair comparison.
 * @param {type} hiddenPair how many Pair comparison experiments are hidden.
 * @param {type} dataCat total experiments of Category.
 * @param {type} hiddenCat how Category experiments are hidden.
 * @returns {undefined}
 */
function setData(dataRank, hiddenRank, dataPair, hiddenPair, dataCat, hiddenCat) {
    dataSource = [
        {type: "Pair comparison", public: dataPair, hidden: hiddenPair},
        {type: "Rank order", public: dataRank, hidden: hiddenRank},
        {type: "Category", public: dataCat, hidden: hiddenCat}
    ];
}

/**
 * Fetches total experiments of all three types and how many of them are hidden.
 * @returns {undefined}
 */
function getTotalExperiments() {
    $.ajax
            ({
                url: 'ajax/scientist/getTotalExperiments.php',
                async: false,
                data: {},
                dataType: 'json',
                type: 'post',
                success: function(data) {
                    var rating = data.totalRating[0].total;
                    var pair = data.totalPair[0].total;
                    var category = data.totalCategory[0].total;
                    var hiddenRating = data.hiddenRating[0].total;
                    var hiddenPair = data.hiddenPair[0].total;
                    var hiddenCategory = data.hiddenCategory[0].total;
                    var publicRating = rating - hiddenRating;
                    var publicPair = pair - hiddenPair;
                    var publicCategory = category - hiddenCategory;
                    //sends fetched data to function for insertion:
                    setData(publicRating, hiddenRating, publicPair, hiddenPair, publicCategory, hiddenCategory);
                },
                error: function(xhr, ajaxOptions, thrownError) {

                }
            });
}

/**
 * Fetches the three latest experiments belonging to user.
 * Then adds them to a table.
 * @returns {undefined}
 */
function getExperimentStatistics() {
    var totalElements;
    $.ajax
            ({
                url: 'ajax/scientist/getExperimentStatistics.php',
                async: false,
                data: {},
                dataType: 'json',
                type: 'post',
                success: function(data) {
                    //checks how many experiments are returned:
                    if (typeof data.title3 != 'undefined') {
                        totalElements = 3;
                    }
                    else if (typeof data.title2 != 'undefined') {
                        totalElements = 2;
                    }
                    else if (typeof data.title1 != 'undefined') {
                        totalElements = 1;
                    }
                    else {                      //successfully runned backend script, but for some reason returns empty/none experiments
                        // exits function.

                        return;
                    }
                    //intermediate saving of data for a more clear view of what is happening.
                    var title1 = data.title1;
                    var visitors1 = data.visitors1[0].total;
                    var completion1 = data.completion1[0].total;
                    var average1 = data.average1;
                    var title2 = data.title2;
                    var visitors2 = data.visitors2[0].total;
                    var completion2 = data.completion2[0].total;
                    var average2 = data.average2;
                    var title3 = data.title3;
                    var visitors3 = data.visitors3[0].total;
                    var completion3 = data.completion3[0].total;
                    var average3 = data.average3;
                    //sets content of variable if they are undefined:
                    (average1 === undefined) ? average1 = "N/A" : average1;
                    (average2 === undefined) ? average2 = "N/A" : average2;
                    (average3 === undefined) ? average3 = "N/A" : average3;
                    //adding each row to table
                    $('#table1 tr:last').after('<tr><td>' + title1 + '</td><td>' + visitors1 + '</td><td>' + completion1 + '</td><td>' + average1 + '</td></tr>');
                    $('#table1 tr:last').after('<tr><td>' + title2 + '</td><td>' + visitors2 + '</td><td>' + completion2 + '</td><td>' + average2 + '</td></tr>');
                    $('#table1 tr:last').after('<tr><td>' + title3 + '</td><td>' + visitors3 + '</td><td>' + completion3 + '</td><td>' + average3 + '</td></tr>');
                },
                error: function(xhr, ajaxOptions, thrownError) {
//                    console.log("Error");
//                    console.log(xhr.status);
//                    console.log(thrownError);
                }
            });
}
