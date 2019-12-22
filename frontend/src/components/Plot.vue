<template>
  <div style="padding-bottom: 100px;">
    <h2 style="margin-top: 150px; margin-bottom: 50px;" class="text-xs-center">
      Statistics
    </h2>

    <div id="box-plot-container" style="height: 400px; margin-right: 20px;"></div>
    <!-- <button id="change-label" class="primary" style="...">Change labels</button> -->

    <div id="experiment-results">
      <!--  -->
    </div>

    <div id="graph-labels">
      <!-- <fieldset> -->
        <!-- <br/> -->
        <!--<div class="input-control text span2" data-role="input-control">-->
        <!--<input type="text" value="" placeholder="input text"/>-->
        <!--<button class="btn-clear"></button>-->
        <!--</div> <br/>-->
      <!-- </fieldset> -->
      <!-- <button id="submit-labels">Submit</button> -->
    </div>

    <!-- <div v-for="(imageSet, i) in results.imageSets" :key="i">
      <div>{{ imageSet.title }}</div>

      <div v-for="(image, j) in results.imagesForEachImageSet">
        <div>{{ image }}</div>
      </div>
    </div> -->

  </div>
</template>

<script>
import { create, all } from 'mathjs'
import {
  calculateZScoreValues,
  parseLFMValues,
  calculateLFMatrix,
  calculatePercentageMatrix,
  calculateCumulative,
  imnegative,
  im,
  matrix,
  transpose,
  dotProduct,
  getAllIndexes,
  // normsInv,
  calculateSlope,
  calculateZScoreMatrix,
  calculateMeanZScore,
  calculateSDMatrix,
  arrayObjectIndexOf
  // ,convertRankToPair
} from '@/maths.js'

const config = {}
const math = create(all, config)

export default {
  data () {
    return {
      results: [],
      resultsArray: null,
      imageSets: [],
      tableId: 0
    }
  },
  created () {
    /* eslint-disable */
    this.$axios.get(`/paired-result/${this.$route.params.id}/statistics`).then(data => {
      this.results = data.data
      var self = this

      // var experimentType = 2;
      // var experimentResults = data[1];

      var data = data.data
      // console.log(data);
      // var resultsArray
      var imageTitleArray = []

      // if (paired) {
        this.loadHighChartsBoxPlot()

        $('#box-plot').after('<div id="zScores-container"></div>')
        //used to identify each round and the belonging divs and elements
        // var roundCounter = 0;
        // iterates through all image sets and creates a table for each
        data['imageSets'].forEach((imageSet, i) => {
          var div = $('<div></div>')

          $("#zScores-container").append('</br></br><div id=raw-' + i + '><h2>Raw data</h2></div>');
          $("#raw-" + i).append('<table class="table bordered hovered">' +
            '<thead>' +
              '<tr class="header-list' + i + '">' +
                '<th>' +
                  '<span class="hint-trigger icon-help" data-hint="Images on the y-axis are the images picked. For example if the value of image x and image y is 2,' +
                  'the image on the y axis is the one picked twice." data-hint-position="right" style="margin: 0 auto"></span>' +
                '</th>' +
              '</tr>' +
            '</thead>' +
            '<tbody class="result-list' + i + '">' +
            '</tbody>' +
          '</table>');


          //Iterates through all images for the corresponding imageset
          data['imagesForEachImageSet'][i].forEach((y, j) => {
            //Adds imagename to table
            $('.header-list' + i).append('<th class="text-left" imageId=' + y['id'] + '>' + y['name'] + '</th>')

            var tableRow = '<tr imageId=' + y['id'] + '><th>' + y['name'] + '</th>'

            imageTitleArray.push(y['name'])

            //Adds empty cells for data
            for (var td = 0; td < data['imagesForEachImageSet'][i].length; td++) {
                tableRow += '<td></td>'
            }
            //Appends row
            $('.result-list' + i + '').append(tableRow + '</tr>')
          })


          self.resultsArray = new Array(data['imagesForEachImageSet'][i].length) // create an array with the length of data['imagesForEachImageSet'][i].length
          for (var it = 0; it < self.resultsArray.length; it++) {
            self.resultsArray[it] = new Array(data['imagesForEachImageSet'][i].length)

            for (var ita = 0; ita < self.resultsArray[it].length; ita++) {
              self.resultsArray[it][ita] = 0
            }
          }

          // for each observer result
          data['resultsForEachImageSet'][i].forEach((result, index) => {
            this.pairAddPoints(
              result['won'],
              result['pictureId'],
              result['wonAgainst'],
              data['imagesForEachImageSet'][i],
              i
            )

            var row    = arrayObjectIndexOf(data['imagesForEachImageSet'][i], result['pictureId'], 'id')
            var column = arrayObjectIndexOf(data['imagesForEachImageSet'][i], result['wonAgainst'], 'id')

            self.resultsArray[row][column] += 1
          })

          var zScoreArray = self.calculatePlots(self.resultsArray) //stores calculated data for one picture set

          //add experiments data to highcharts graph
          this.addSeries(imageTitleArray, zScoreArray, imageSet['title'])

          $("#zScores-container").append('</br></br><h2>Z-Scores</h2>')
          this.setZScores(imageTitleArray, zScoreArray, imageSet['title'], data['imageUrl'][i]['path'])

          this.highLightFirstTable()
          this.activeSeriesClickListener()

          imageTitleArray = [] //empties array for next picture set
          // roundCounter++

          //prepareEditLabels();
          $('#experiment-results').append(div)
        })

      })

    // }
  },
  methods: {
    calculatePlots ($frequencyMatrix, $category) {
        //$frequencyMatrix = transpose($frequencyMatrix); //transposing in order to get correct z-score calculation
        var observerAmount = 0
        var cumulativeFrequencyTable
        var cumulativePercentageTable

        //Calculates number of observers
        if ($category) { //Category uses non square matrix, counts first row results
          for (var i = 0; i < $frequencyMatrix[0].length; i++) {
            observerAmount += parseInt($frequencyMatrix[0][i])
          }
        } else {
          observerAmount = $frequencyMatrix[0][1] + $frequencyMatrix[1][0]
        }

        if ($category) {
            cumulativeFrequencyTable = calculateCumulative($frequencyMatrix, true)
        }

        //Calculates a percentage matrix of the results
        var PercentageMatrix = calculatePercentageMatrix($category ? cumulativeFrequencyTable : $frequencyMatrix, observerAmount)

        //Calculates a LFMatrix of the results, using percentage matrix
        var LFMatrix = calculateLFMatrix($category ? cumulativeFrequencyTable : $frequencyMatrix, observerAmount, $category)

        //Parses the LF Matrix into a single row of results
        var LFMValues = parseLFMValues(LFMatrix, $category)

        //Calculates the Z-score values as a single array
        var ZScoreValues = calculateZScoreValues(PercentageMatrix, $category)

        var feedback = ZScoreValues[1]

        //Calculates the slope between LFM and Z-scores
        var slope = calculateSlope(ZScoreValues[0], LFMValues)

        //Calculates the Z-score matrix to be displayed using confidence interval
        var ZScoreMatrix = calculateZScoreMatrix(LFMatrix, slope['slope'], $category)

        //Calculates the mean z score values per image
        var meanZScore = calculateMeanZScore(ZScoreMatrix, $category).map(function (num) {
            return parseFloat(num.toFixed(3))
        });

        var standardDeviation = 1.96 * (1 / Math.sqrt(2)) / Math.sqrt(observerAmount) //Must be changed

        var SDArray = calculateSDMatrix($frequencyMatrix)

        //Calculates the high confidence interval limits
        var highCILimit = meanZScore.map(function (num, i) {
            return parseFloat((num + SDArray[i]).toFixed(3))
        });

        //Calculates the low confidence interval limits
        var lowCILimit = meanZScore.map(function (num, i) {
            return parseFloat((num - SDArray[i]).toFixed(3))
        });

        return [lowCILimit, meanZScore, highCILimit, feedback]
    },

    calculatePlotsCategory ($frequencyMatrix, $category) {
        var observerAmount = 0
        var cumulativeFrequencyTable
        var cumulativePercentageTable

        //Calculates number of observers
        if ($category) { //Category uses non square matrix, counts first row results
            for (var i = 0; i < $frequencyMatrix[0].length; i++) {
                observerAmount += parseInt($frequencyMatrix[0][i])
            }
        } else {
            observerAmount = $frequencyMatrix[0][1] + $frequencyMatrix[1][0]
        }

        if ($category) {
            cumulativeFrequencyTable = calculateCumulative($frequencyMatrix, true)
        }

        //Calculates a percentage matrix of the results
        var PercentageMatrix = calculatePercentageMatrix($category ? cumulativeFrequencyTable : $frequencyMatrix, observerAmount)

        //Calculates a LFMatrix of the results, using percentage matrix

        var LFMatrix = calculateLFMatrix($category ? cumulativeFrequencyTable : $frequencyMatrix, observerAmount, $category)

        //Parses the LF Matrix into a single row of results
        var LFMValues = parseLFMValues(LFMatrix, $category)


        //Calculates the Z-score values as a single array
        var ZScoreValues = calculateZScoreValues(PercentageMatrix, $category)

        //Setting up identity matrix
        var eyeMatrix = im($frequencyMatrix[0].length - 1)

        //Setting up X matrix (code is a bit messy, but it works). Done accoring to the "Colour Engineering Toolbox" by Phil Green
        var X1 = []
        X1 = matrix(cumulativeFrequencyTable.length * ($frequencyMatrix[0].length - 1), $frequencyMatrix[0].length - 1, 0)
        var roundCounter = 0
        for (var j = 0; j < $frequencyMatrix[0].length - 1; j++) {
            for (var k = 0; k < cumulativeFrequencyTable.length; k++) {
                for (var i = 0; i < $frequencyMatrix[0].length - 1; i++) {
                    X1[roundCounter][i] = eyeMatrix[j][i]
                }
                roundCounter++;
            }
        }

        var eyeMatrixNegative = imnegative(cumulativeFrequencyTable.length)
        var X2 = []
        X2 = matrix(cumulativeFrequencyTable.length * ($frequencyMatrix[0].length - 1), eyeMatrixNegative.length, 0)

        var roundCounter = 0
        for (var k = 0; k < $frequencyMatrix[0].length - 1; k++) {
            for (var j = 0; j < eyeMatrixNegative.length; j++) {
                for (var i = 0; i < eyeMatrixNegative.length; i++) {
                    X2[roundCounter][i] = eyeMatrixNegative[j][i]
                }
                roundCounter++
            }
        }

        var X = []
        for (var k = 0; k < X2.length; k++) {
            X[k] = X1[k].concat(X2[k])
        }

        var Xtemp = [];
        for (var i = 0; i < X[0].length; i++) {
            if (i < $frequencyMatrix[0].length - 1) {
                Xtemp[i] = 0;
            }
            else {
                Xtemp[i] = 1
            }
        }
        X.push(Xtemp)

        //initial z-scores values extracted
        var v = ZScoreValues[0]

        //reformat V to fit with analysis later
        var vv = []
        for (var j = 0; j < ($frequencyMatrix[0].length - 1); ++j) {
            for (var k = j; k < v.length; k += (PercentageMatrix[0].length)) {
                vv.push(v[k])
            }
        }
        v = vv
        v.push(0)

        var indexes = getAllIndexes(v, 3) //findng "infs"
        indexes = indexes.concat(getAllIndexes(v, -3)) //findng "infs" (also the negative ones)
        indexes.sort()

        for (var i = 0; i < indexes.length; i++) {
            v.splice(indexes[i] - i, 1)
            X.splice(indexes[i] - i, 1)
        }

        // least-squares solution
        var Xtransposed = []
        Xtransposed = transpose(X)
        var Xtemp2 = []
        Xtemp2 = matrix(X[0].length, X[0].length, 0)
        var OneDirection = []
        var TwoDirection = []
        for (var i = 0; i < X[0].length; i++) {
            for (var j = 0; j < X[0].length; j++) {
                for (var k = 0; k < Xtransposed[0].length; k++) {
                    OneDirection[k] = X[k][j]
                    TwoDirection[k] = Xtransposed[i][k]
                }
                Xtemp2[j][i] = dotProduct(TwoDirection, OneDirection) // X'*X
            }
        }


        var Xtemp3 = []
        for (var i = 0; i < X[0].length; i++) {
            for (var k = 0; k < Xtransposed[0].length; k++) {
                TwoDirection[k] = Xtransposed[i][k]
            }
            Xtemp3[i] = dotProduct(TwoDirection, v) // X'*v
        }

        // including math.js to do inverse, sum and absolute.
        // includeJs('math.js');

        //if we cannot invert Xtemp2, then we cannot calculate z-scores.  Does a check here, and then display an error message
        breakC = 0
        var meanZScore = []
        var lowCILimit = []
        var highCILimit = []
        for (var i = 0; i < Xtemp2[0].length; i++) {
            if (math.sum(math.abs(Xtemp2[i])) == 0) {
                breakC = 1

                for (var j = 0; j < ZScoreValues[0].length; j++) {
                    meanZScore[j] = 0 //setting values to 0 if we cannot invert Xtemp2
                    lowCILimit[j] = 0
                    highCILimit[j] = 0
                }
            }
        }

        if (breakC == 1) {
            window.alert(`
              Not enough data to calculate Z-scores.
              In order to calculate z-scores at least one row needs to be complete.
              All values are set to '0'.
            `)
        }
        else { //If we can invert it, then do the calculations.
            Ytemp = math.inv(Xtemp2)

            var Y = []
            var ThreeDirection = []
            for (var i = 0; i < X[0].length; i++) {
                for (var k = 0; k < Ytemp[0].length; k++) {
                    ThreeDirection[k] = Ytemp[i][k]
                }
                Y[i] = dotProduct(ThreeDirection, Xtemp3)
            }
            Y = Y.slice($frequencyMatrix[0].length - 1, Y.length); //the two first numbers are category boundaries, so they are not needed here.

            var feedback = ZScoreValues[1]

            //Formatting the z-scores to have 3 decimals
            var meanZScore = Y.map(function (num) {
                return parseFloat(num.toFixed(3))
            })

            //Finding standard deviation
            var standardDeviation = 1.96 * (1 / Math.sqrt(2)) / Math.sqrt(observerAmount)  //Must be changed

            var SDArray = calculateSDMatrix($frequencyMatrix)

            //Calculates the high confidence interval limits
            var highCILimit = meanZScore.map(function (num, i) {
                return parseFloat((num + SDArray[i]).toFixed(3))
            });

            //Calculates the low confidence interval limits
            var lowCILimit = meanZScore.map(function (num, i) {
                return parseFloat((num - SDArray[i]).toFixed(3))
            })
        }

        return [lowCILimit, meanZScore, highCILimit, feedback]
    },

    /**
     * Loads and initiates a box plot into the view experiment area
     *
     * @param dataArray contains the data like z-scores
     * @param labelArray contains all labels on the axis
     */
    initiateHighCharts () {
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
                tickInterval: 0.5,      //step interval
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
        })

        //$('#box-plot-containter').append("Where the box-plot goes");
    },

    /**
     * Loads the required html for the highchart plugin into the page
     */
    loadHighChartsBoxPlot () {
        $('#experiment-results').append('<br><div id="box-plot"></div>')
        // $('#box-plot').load("ajax/scientist/boxPlot.html", function () {
        this.initiateHighCharts()
        //prepareEditLabels(); //Moved after the chart is loaded
        $('#graph-labels').hide()  //hides/closes the element
        // });
    },

    addSeries (imageTitleArray, zScoreArray, imageSetTitle) {
        var bufferArray = []
        var meanValues = []
        var highLows = []
        var chart = $('#box-plot-container').highcharts() //reference to the plot.

        for (var i = 0; i < imageTitleArray.length; i++) {    //makes a global array of image titles
            bufferArray.push(imageTitleArray[i])
        }

        this.imageSets.push(bufferArray)

        chart.setTitle({text: imageSetTitle})   //Sets the title (top of graph)
        chart.xAxis[0].setCategories(imageTitleArray) // Sets the title of the picture-sets at the x-axis

        for (var i = 0; i < zScoreArray[2].length; i++) {
            meanValues.push(zScoreArray[1][i])  //Mean value
            highLows.push([zScoreArray[0][i], zScoreArray[2][i]]) //push high and low values. ready for th chart.
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

        this.prepareEditLabels()

        this.hideAllButFirstSeries()
    },

    /**
     * Hides all but one series in the boxplot.
     */
    hideAllButFirstSeries () {
        var series
        var chart = $('#box-plot-container').highcharts() //reference to the plot.
        var i = 0

        $(chart.series).each(function () {
          this.setVisible(false, false)
        })

        chart.series[0].setVisible()
    },

    /**
     * Functions listens for when a data series are clicked and then highlights
     * the belonging z-scores table(s).
     */
    activeSeriesClickListener () {
        var id
        var series
        var chart = $('#box-plot-container').highcharts() //reference to the plot.
        var color
        var fontSize

        //var images = [["bilde1","bilde2","bilde3"],["elefant1","elefant2","elefant3"],["Mus1","Mus2","Mus3"]];

        $(".highcharts-legend-item").click(function () {    //listens for when the user changes series selected series
            id = 0
            $('.highcharts-legend-item').each(function () {
                color = $(this).find('text').css('color')      //reads property to determine if the series is selected

                if (color != "rgb(204, 204, 204)") {                //if series is selected, styles the correct table
                    fontSize = parseInt($(this).css("font-size"))
                    fontSize = fontSize + 3 + "px"                  //increases the font size

                    $(".z-scores:eq( " + id + ")").css({"color": "blue"})
                    $(".z-scores:eq( " + id + ") td").css({"font-size": fontSize})
                    chart.xAxis[0].setCategories(this.imageSets[id]) // Sets the title of the picture-sets at the x-axis
                }
                else {              //when a series is not selected, reverts styling of the belonging table
                    $(".z-scores:eq( " + id + ")").css({"color": "black"})
                    $(".z-scores:eq( " + id + ") td").css({"font-size": "-4"})
                }

                id++
            })
        })
    },

    /**
     * Highlights the first table since the first series is highlighted.
     */
    highLightFirstTable () {
        var fontSize

        fontSize = parseInt($(".z-scores:eq( " + 0 + ")").css("font-size"))    //reads font-size property of first matching table
        fontSize = fontSize + 3 + "px"
        //changes color and size on the font:
        $(".z-scores:eq( " + 0 + ")").css({"color": "blue"});
        $(".z-scores:eq( " + 0 + ") td").css({"font-size": fontSize});
    },


    /**
     * Retrieves the new values for the labels and calls for reiniating of graph with new labels
     */
    editLabels () {
        // Retrieves all x-axis labels and puts into an array:
        var arr = []
        $("#graph-labels input").each(function (index) {
            var newLabelName = $(this).val()
            arr.push(newLabelName)
        })

        // The graph, as set with GraphCalc:
        var chart = $('#box-plot-container').highcharts()

        // Reiniating the graph with new labels:
        chart.xAxis[0].setCategories(arr)
        chart.xAxis[0].setTitle({text: $("#xLabelInput").val()})
        chart.yAxis[0].setTitle({text: $("#yLabelInput").val()})
        chart.setTitle({text: $("#titleInput").val()})
        chart.setTitle(null, {text: $("#subTitleInput").val()})
    },

    /**
     * Preloads input fields into element for a smooth animation
     */
    prepareEditLabels () {
        $("#graph-labels fieldset").append('<p>Results </p> ')

        // For each label on the x-axis, make an input field:
        $(".highcharts-xaxis-labels text").each(function (index) {
            var labelName = $(this).text()
            //console.log(labelName)

            $("#graph-labels fieldset").append('<div class="input-control text span2" data-role="input-control">' +
            '<input type="text" value="' + labelName + '" placeholder=""/>' +
            '</div> <br/>')
        })

        var chart = $('#box-plot-container').highcharts() //reference to the plot.
        var xLabell = chart.xAxis[0].categories[0]
        //chart.series.data[].category
        //chart.xAxis[0].categories[0]


        // Makes a input field for a Title of the x-axis:
        var xLabel = $(".highcharts-xaxis-title").text()
        $("#graph-labels fieldset").append('<hr style=" margin-top: 0.5em; margin-bottom: 0.5em; margin-left: auto; margin-right: auto; border-style: inset; border-width: 2px;"> ' +
        '<p>X-axis</p>' +
        '<div class="input-control text span2" data-role="input-control">' +
        '<input type="text" id="xLabelInput" value="' + xLabel + '" placeholder=""/>' +
        '</div> <br/>')

        // Makes a input field for the y-axis:
        var yLabel = $(".highcharts-yaxis-title").text()
        $("#graph-labels fieldset").append('<hr style=" margin-top: 0.5em; margin-bottom: 0.5em; margin-left: auto; margin-right: auto; border-style: inset; border-width: 2px;"> ' +
        '<p>Y-axis</p>' +
        '<div class="input-control text span2" data-role="input-control">' +
        '<input type="text" id="yLabelInput" value="' + yLabel + '" placeholder=""/>' +
        '</div> <br/>')

        // Makes a input field for Title of the graph:
        var plotTitle = $(".highcharts-title").text()
        $("#graph-labels fieldset").append('<hr style=" margin-top: 0.5em; margin-bottom: 0.5em; margin-left: auto; margin-right: auto; border-style: inset; border-width: 2px;"> ' +
        '<p>Plot title</p>' +
        '<div class="input-control text span4" data-role="input-control">' +
        '<input type="text" id="titleInput" value="' + plotTitle + '" placeholder=""/>' +
        '</div> <br/>')

        // Makes a input field for Subtitle of the graph:
        var plotSubTitle = $(".highcharts-subtitle").text()
        $("#graph-labels fieldset").append('<hr style=" margin-top: 0.5em; margin-bottom: 0.5em; margin-left: auto; margin-right: auto; border-style: inset; border-width: 2px;"> ' +
        '<p>Subtitle</p>' +
        '<div class="input-control text span4" data-role="input-control">' +
        '<input type="text" id="subTitleInput" value="' + plotSubTitle + '" placeholder=""/>' +
        '</div> <br/>')
    },

    /**
     * Goes through analyzed results for one picture set and sets them into a table
     * @param imageTitleArray contains all pictures for one picture set
     * @param zScoresArray contains all calculated data for each picture in the picture set
     * @param pictureSetTitle the title of the picture set
     */
    setZScores (imageTitleArray, zScoresArray, pictureSetTitle, imageUrl) {
        //inserts new table with picture set title
        this.tableDivHeader(this.tableId, pictureSetTitle, zScoresArray[3], imageUrl)

        //count whether there is any existing rows in table
        var rowCount = $('#z-scores tr').length
        if (rowCount > 0) {
            //appends a empty row to distinguish between multiple picture sets
            $('#z-scores').append('<tr><td class="right">---</td><td class="right">---</td><td class="right">---</td><td class="right">---</td></tr>');
        }

        //loops through array and for each picture prints title, low ci limit, mean z-score and high ci limit
        for (var i = 0; i < imageTitleArray.length; i++) {
            $('#' + this.tableId).append('<tr>' +
              '<td><b>' + imageTitleArray[i] + '</b></td>' +
              '<td>' + zScoresArray[0][i] + '</td>' +
              '<td>' + zScoresArray[1][i] + '</td>' +
              '<td>' + zScoresArray[2][i] + '</td>' +
              '</tr>'
            );
        }

        this.tableId++;
    },

    /**
     * Function adds table and title of table each time it is called.
     * @param tableId the identifier of the table, used to insert data.
     * @param pictureSetTitle title of the picture set.
     */
    tableDivHeader (tableId, pictureSetTitle, check, imageUrl) {
        //when there is not enough data to properly calculate z-scores it sets a hint to explain it to the user
        if (check == 1) {
            //appends the table right before the title of the table displaying raw data
            $("#zScores-container").append('<div>' +
            '<h4>' +
              'Z-Scores: ' + pictureSetTitle +
              '<span class="hint-trigger" data-hint="Need more observer-data to be calculated properly" data-hint-position="right"><i class="icon-info on-right"></i>' +
            '</h4>' +
            '<img src="http://127.0.0.1/QuickEval/storage/app/' + imageUrl + '" alt="Original picture" height="20%" width="20%"> ' +
            '<br/>' +
            '<br/>' +
            '<table class="table bordered hovered z-scores">' +
              '<thead>' +
                '<tr>' +
                  '<th class="text-left">Title</th>' +
                  '<th class="text-left">Low CI limit</th>' +
                  '<th class="text-left">Mean z-score</th>' +
                  '<th class="text-left">High CI limit</th>' +
                '</tr>' +
              '</thead>' +
              '<tbody id="' + tableId + '">' +
              '</tbody>' +
              '<tfoot></tfoot>' +
            '</table>' +
            '</div>');
        }
        else { //when there is enough data to properly calculate z-scores
            $("#zScores-container").append('<div>' +
            '<h4>Z-Scores: ' + pictureSetTitle + '</h4>' +
            '<img src="http://127.0.0.1/QuickEval/storage/app/' + imageUrl + '" alt="Original picture" height="20%" width="20%"> ' +
            '<br/>' +
            '<table class="table bordered hovered z-scores">' +
              '<thead>' +
                '<tr>' +
                  '<th class="text-left">Title</th>' +
                  '<th class="text-left">Low CI limit</th>' +
                  '<th class="text-left">Mean z-score</th>' +
                  '<th class="text-left">High CI limit</th>' +
                '</tr>' +
              '</thead>' +
              '<tbody id="' + tableId + '">' +
              '</tbody>' +
              // '<tfoot></tfoot>' +
            '</table>' +
            '</div>');
        }
    },

    /**
     * Adds points to table based on parameters
     *
     * @param  {int} $points            amount of points to add
     * @param  {int} $firstImage        id of image who won
     * @param  {int} $secondImage       id of image who lost
     * @param  {array} $array           array of image ids
     */
    pairAddPoints ($points, $firstImage, $secondImage, $array, $roundIterator) {
        var imageIndex = arrayObjectIndexOf($array, $firstImage, 'id')
        var wonAgainstIndex = arrayObjectIndexOf($array, $secondImage, 'id')

        var resultList = $('.result-list' + $roundIterator + '')
        var cell = resultList.find('tr:eq(' + imageIndex + ')').children().eq(wonAgainstIndex + 1)

        cell.html((cell.html() == "") ? parseFloat($points) : +cell.html() + parseFloat($points))
    }
  }
  /* eslint-enable */
}
</script>

<style lang="css">
  .table td, .table tr {
    padding: 10px;
  }
  .table tr {
    border: 1px solid #ddd;
  }
  .table.bordered {
    /*border: 1px #eaeaea solid;*/
    border: 1px solid rgba(0,0,0,0.12);
    border-left: 0;
  }
  .table {
      width: 100%;
      margin-bottom: 14pt;
  }
  .table {
      max-width: 100%;
      background-color: #ffffff;
      border-collapse: collapse;
      border-spacing: 0;
  }
  .table.bordered td, .table.bordered th {
      /*border-left: 1px #eaeaea solid;
      border-bottom: 1px #eaeaea solid;*/
      border-left: 1px solid rgba(0,0,0,0.12);
      border-bottom: 1px solid rgba(0,0,0,0.12);
      padding: 10px;
      max-width: 50px;
      overflow: hidden;
  }
</style>
