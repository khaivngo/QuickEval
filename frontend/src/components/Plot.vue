<template>
  <div>
    <h2 style="margin-top: 50px;" class="text-xs-center">
      Statistics
    </h2>

    <!-- <div id="selected-experiment" class="primary">
      <table class="table bordered" id="table3">
        <tbody id="statistics">
        </tbody>
      </table>
    </div> -->

    <div id="experiment-results">
      <!--  -->
    </div>

    <div id="box-plot-container" style="height: 400px; min-width: 310px; max-width: 600px"></div>
    <button id="change-label" class="primary" style="...">Change labels</button>

    <div id='graph-labels'>
      <fieldset>
        <br/>
        <!--<div class="input-control text span2" data-role="input-control">-->
        <!--<input type="text" value="" placeholder="input text"/>-->
        <!--<button class="btn-clear"></button>-->
        <!--</div> <br/>-->
      </fieldset>
      <button id="submit-labels">Submit</button>
    </div>

  </div>
</template>

<script>
/* eslint-disable */
export default {
  data () {
    return {
    }
  },
  created () {
    function calculatePlots($frequencyMatrix, $category) {
        //$frequencyMatrix = transpose($frequencyMatrix); //transposing in order to get correct z-score calculation
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
    } // calculatPlots


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
                Xtemp2[j][i] = dot_product(TwoDirection, OneDirection); // X'*X
            }
        }


        var Xtemp3 = [];
        for (var i = 0; i < X[0].length; i++) {
            for (var k = 0; k < Xtransposed[0].length; k++) {
                TwoDirection[k] = Xtransposed[i][k]
            }
            Xtemp3[i] = dot_product(TwoDirection, v); // X'*v
        }

        //including math.js to do inverse, sum and absolute.
        // includeJs("/Quickeval-develop/js/scientist/math.js");
        // includeJs('math.js');

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

    } // end calculatePlotsCategory

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

    function dot_product(ary1, ary2) {
        if (ary1.length != ary2.length)
            throw "can't find dot product: arrays have different lengths";
        var dotprod = 0;
        for (var i = 0; i < ary1.length; i++)
            dotprod += ary1[i] * ary2[i];
        return dotprod;
    }

    //function to transpose a matrix
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
     *
     * @param   $frequencyMatrix n*n matrix with paired comparison results
     * @param   $observerAmount amount of observers who participated in the experiment
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
     *
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
     *
     * from top to bottom, each value is added to the array, then next column.
     * @param $LFMatrix a n*n logistic function matrix
     * @returns {Array} a array with a length of observers^2 containing the LFMMatrix values
     */
    function parseLFMValues($LFMatrix, $category) {
        var LFMValues = [];
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
     *
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
     *
     * Calculates a coefficient based on the relationship between the inverse of the standard normal cumulative
     * distribution for the percentage matrix.
     *
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
     *
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
     *
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
     *
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

    /****************************************************************************************
      
        HTML stuff

    ***************************************************************************************/


    /**
     * Loads and initiates a box plot into the view experiment area
     *
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

        });

        //$('#box-plot-containter').append("Where the box-plot goes");
    }

    /**
     * Loads the required html for the highchart plugin into the page
     */
    function loadHighChartsBoxPlot () {
        $('#experiment-results').append('<br><div id="box-plot"></div>');
        // $('#box-plot').load("ajax/scientist/boxPlot.html", function () {
        initiateHighCharts();
        //prepareEditLabels();      //Moved after the chart is loaded
        $('#graph-labels').hide();  //hides/closes the element
        // });
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
        // $(".z-scores:eq( " + 0 + ")").css({"color": "blue"});
        // $(".z-scores:eq( " + 0 + ") td").css({"font-size": fontSize});
    }


    /**
     * Retrieves the new values for the labels and calls for reiniating of graph with new labels
     */
    function editLabels() {
        // Retrieves all x-axis labels and puts into an array:
        var arr = [];
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
        //inserts new table with picture set title
        // console.log(zScoresArray)
        tableDivHeader(tableId, pictureSetTitle, zScoresArray[3], imageUrl);

        //count whether there is any existing rows in table
        var rowCount = $('#z-scores tr').length;
        if (rowCount > 0) {
            //appends a empty row to distinguish between multiple picture sets
            $('#z-scores').append('<tr><td class="right">---</td><td class="right">---</td><td class="right">---</td><td class="right">---</td></tr>');
        }

        //loops through array and for each picture prints title, low ci limit, mean z-score and high ci limit
        for (var i = 0; i < imageTitleArray.length; i++) {
            $('#' + tableId + '').append('<tr><td><b>' +
                imageTitleArray[i] +
                '</b></td><td class="right">' +
                zScoresArray[0][i] +
                '</td><td class="right">' +
                zScoresArray[1][i] +
                '</td><td class="right">' +
                zScoresArray[2][i] +
                '</td></tr>'
            );
        }

        tableId++;
    }

    /**
     * Function adds table and title of table each time it is called.
     * @param tableId the identifier of the table, used to insert data.
     * @param pictureSetTitle title of the picture set.
     */
    function tableDivHeader(tableId, pictureSetTitle, check, imageUrl) {
        //when there is not enough data to properly calculate z-scores it sets a hint to explain it to the user
        if (check == 1) {
            //appends the table right before the title of the table displaying raw data
            $("#zScores-container").append('<br/><div>' +
            '<h4>Z-Scores: ' + pictureSetTitle + '<span class="hint-trigger" data-hint="Need more observer-data to be calculated properly" data-hint-position="right"><i class="icon-info on-right"></i></h4>' +
            '<img src="http://127.0.0.1/QuickEval/storage/app/' + imageUrl + '" alt="Original picture" height="20%" width="20%"> ' +
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
        else { //when there is enough data to properly calculate z-scores
            $("#zScores-container").append('<br/><div>' +
            '<h4>Z-Scores: ' + pictureSetTitle + '</h4>' +
            '<img src="http://127.0.0.1/QuickEval/storage/app/' + imageUrl + '" alt="Original picture" height="20%" width="20%"> ' +
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
        // $("img").error(function () {
        //     $(this).unbind("error").hide();
        // });
    }


    /**
     * Creates a pair comparison result matrix from ranking data where table[i][j] refers to the number of times image[i] was chosen over image[j]k
     * @param $data Rank order data where data[person][imageRank] corresponds to the rank of one image
     * @returns {Array} Result matrix
     */
    function convertRankToPair ($data) {
        // Determines the number of images in the image set
        var max = 0;
        while (typeof $data[0][max] != 'undefined') max++;

        var table = [];
        for (var x = 0; x < max; x++) {
          table[x] = [];
        }

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
     *
     * @param  {int} $points            amount of points to add
     * @param  {int} $firstImage        id of image who won
     * @param  {int} $secondImage       id of image who lost
     * @param  {JQuery object} $table   jquery object of table where images are
     * @param  {array} $array           array of image ids
     */
    function pairAddPoints($points, $firstImage, $secondImage, $table, $array, $roundIterator) {
        var imageIndex = arrayObjectIndexOf($array, $firstImage, 'id')
        var wonAgainstIndex = arrayObjectIndexOf($array, $secondImage, 'id')

        var resultList = $('.result-list' + $roundIterator + '')
        var cell = resultList.find('tr:eq(' + imageIndex + ')').children().eq(wonAgainstIndex + 1)

        cell.html((cell.html() == "") ? parseFloat($points) : +cell.html() + parseFloat($points))
    }



    /*****************************************
                  let's go
    *****************************************/

    this.$axios.get(`/paired-result/${this.$route.params.id}/statistics`).then(data => {

      // var experimentType = 2;
      // var experimentResults = data[1];

      var data = data.data;
      // console.log(data);
      var resultsArray;
      var imageTitleArray = []

      // if (paired) {
        loadHighChartsBoxPlot()

        $('#box-plot').after('<div id="zScores-container"></div>')
        //used to identify each round and the belonging divs and elements
        var roundCounter = 0;
        // iterates through all image sets and creates a table for each
        data['imageSets'].forEach(function (t, i) {
          var div = $('<div></div>');
          var element = $(this);

          $("#zScores-container").append('</br></br><div id=raw-' + roundCounter + '><h2>Raw data</h2><hr></div>');
          $("#raw-" + roundCounter).append('<table class="table bordered hovered">' +
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


          //Iterates through all images for the corresponding imageset
          data['imagesForEachImageSet'][i].forEach(function (y, j) {
            //Adds imagename to table
            $('.header-list' + roundCounter + '').append('<th class="text-left" imageId=' + y['id'] + '>' + y['name'] + '</th>');

            var tableRow = '<tr imageId=' + y['id'] + '><th>' + y['name'] + '</th>';

            imageTitleArray.push(y['name']);        //saves each picture title into array for later use

            //Adds empty cells for data
            for (var ii = 0; ii < data['imagesForEachImageSet'][i].length; ii++) {
                tableRow += '<td></td>';
            }
            //Appends row
            $('.result-list' + roundCounter + '').append(tableRow + '</tr>');
          });


          resultsArray = new Array(data['imagesForEachImageSet'][i].length) // create an array with the length of data['imagesForEachImageSet'][i].length
          for (var it = 0; it < resultsArray.length; it++) {
            resultsArray[it] = new Array(data['imagesForEachImageSet'][i].length)

            for (var ita = 0; ita < resultsArray[it].length; ita++) {
              resultsArray[it][ita] = 0
            }
          }

          // for each observer result
          data['resultsForEachImageSet'][i].forEach(function (result, index) {
            pairAddPoints(
              result['won'],
              result['pictureId'],
              result['wonAgainst'],
              element,
              data['imagesForEachImageSet'][i],
              roundCounter
            )

            var row = arrayObjectIndexOf(data['imagesForEachImageSet'][i], result['pictureId'], 'id')
            var column = arrayObjectIndexOf(data['imagesForEachImageSet'][i], result['wonAgainst'], 'id')

            resultsArray[row][column] += 1
          })

          // calculatePlots(resultsArray)
          // var zScoreArray = []
          var zScoreArray = calculatePlots(resultsArray); //stores calculated data for one picture set
          
          //Add experiments data to graph
          addSeries(imageTitleArray, zScoreArray, t['title'])

          $("#zScores-container").append('</br></br><h2>Z-Scores</h2><hr>');

          //sends all imagestitles, calculated results and the name of picture set
          setZScores(imageTitleArray, zScoreArray, t['title'], data['imageUrl'][i]['path']);
          imageTitleArray = [];   //empties array for next picture set

          highLightFirstTable();
          activeSeriesClickListener();

          roundCounter++;    //counts up for next round.

          //prepareEditLabels();
          $('#experiment-results').append(div);
        });

      });

    // }

  }
}
/* eslint-enable */
</script>

<style lang="css">
  .table td, .table tr {
    padding: 10px;
  }
  .table tr {
    border: 1px solid #ddd;
  }
</style>
