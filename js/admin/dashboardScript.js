
var dataSource = new Array();

$(document).ready(function() {

    $(function()
    {
        $("#chartContainer").dxPieChart({
            dataSource: dataSource,
            title: "Total Users",
            tooltip: {
                enabled: true,
                format: "",
                percentPrecision: 0,
                customizeText: function() {
                    return this.valueText + " - " + this.percentText;
                }
            },
            legend: {
                horizontalAlignment: "right",
                verticalAlignment: "top",
                margin: 0
            },
            series: [{
                    type: "doughnut",
                    argumentField: "region",
                    label: {
                        visible: true,
                        format: "",
                        connector: {
                            visible: true
                        }
                    }
                }]
        });
    });

    getTotalUsers();

});

/**
 * Gets alle user types and their amount and binds the data to an array
 * of objects for later use in ChartJs initialisation.
 * @returns {undefined}
 */
function getTotalUsers() {
    $.ajax
            ({
                url: 'ajax/admin/getTotalUsers.php',
                async: false,
                data: {},
                dataType: 'json',
                type: 'post',
                success: function(data) {
                    var type1 = data[0].title;
                    var total1 = parseInt(data[0].total);

                    var type2 = data[1].title;
                    var total2 = parseInt(data[1].total);

                    var type3 = data[2].title;
                    var total3 = parseInt(data[2].total);

                    var type4 = data[3].title;
                    var total4 = parseInt(data[3].total);

                    dataSource = [
                        {region: type1, val: total1},
                        {region: type2, val: total2},
                        {region: type3, val: total3},
                        {region: type4, val: total4}
                    ];

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log("Error");
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
}
