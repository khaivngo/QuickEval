// $(document).ready(function() {

//     $("#results").click(function() {
//         $.ajax({
//             async: false,
//             url: "ajax/scientist/viewResults.html",
//             success: function(data) {
//                 //Fills panel
//                 $($('#right-panel')).html(data);
//                 $('#right-menu').empty();

//                 //Sets up UI and listener
//                 $('#no-results-warning').hide();
//                 $('#export-results').hide();
//                 $('#experiment-results').hide();
//                 $('#export-parameters').hide()
//                 setUpExperimentList();
//                 setUpListListener(1);
//             },
//             error: function(request, status, error) {
//                 alert(request.responseText);
//             }
//         });
//         setActive($(this));
//     });
// });

// function displayExperiment($experimentId) {
//     var data = getExperimentById($experimentId);
//     $('.listview-outlook').siblings(':eq(0)').remove();
//     $('.listview-outlook').remove();
//     $('#experiment-name').html('<a class="icon-arrow-left-3" style="margin-right: 10px"/>' + data['title']);
//     printResults($experimentId);
// }

