/* eslint-disable */

//Get indexes
function getAllIndexes(arr, val) {
    var indexes = [], i = -1
    while ((i = arr.indexOf(val, i + 1)) != -1) {
        indexes.push(i)
    }
    return indexes
}

function dotProduct(ary1, ary2) {
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
    var arr = []

    // Creates all lines:
    for (var i = 0; i < rows; i++) {

        // Creates an empty line
        arr.push([])

        // Adds cols to the empty line:
        arr[i].push(new Array(cols))

        for (var j = 0; j < cols; j++) {
            // Initializes:
            arr[i][j] = defaultValue
        }
    }

    return arr
}

function im(n) {
    return Array.apply(null, new Array(n)).map(function (x, i, a) {
        return a.map(function (y, k) {
            return i === k ? 1 : 0
        })
    })
}

function imnegative(n) {
    return Array.apply(null, new Array(n)).map(function (x, i, a) {
        return a.map(function (y, k) {
            return i === k ? -1 : 0
        })
    })
}

//
function calculateCumulative($frequencyMatrix, $pop) {
    var cumulativeTable = []

    for (var j = 0; j < $frequencyMatrix.length; j++) {

        var result = $frequencyMatrix[j].concat()

        for (var i = 0; i < $frequencyMatrix[j].length; i++) {
            result[i] = $frequencyMatrix[j].slice(0, i + 1).reduce(function (p, i) {
                return p + i
            })
        }
        if ($pop) {
            result.pop() //Removes last entry, as it is not wanted (will always be equal to number of observers)
        }
        cumulativeTable.push(result)
    }

    return cumulativeTable
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
    var percentageMatrix = JSON.parse(JSON.stringify($frequencyMatrix))

    //Iterates through every row and column, then divides the frequency with amount of observers to
    // get a percentage
    for (var i = 0; i < percentageMatrix.length; i++) {
        for (var j = 0; j < percentageMatrix[i].length; j++) {
            percentageMatrix[i][j] /= $observerAmount
        }
    }

    return percentageMatrix
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
    var LFMatrix = new Array($frequencyMatrix.length)

    //Iterates through each row and column, performing a calculation on each cell to create the LFMatrix
    for (var i = 0; i < LFMatrix.length; i++) {
        LFMatrix[i] = new Array($frequencyMatrix[0].length)

        for (var j = 0; j < LFMatrix[i].length; j++) {
            //Empty cells (the diagonal through the table) shall stay empty

            if (i != j || $category) {
                //ln of ((same cell in FMatrix + 0,5) / (total observers - same cell in FMatrix + 0,5))
                LFMatrix[i][j] = Math.log(($frequencyMatrix[i][j] + 0.5) / ($observerAmount - $frequencyMatrix[i][j] + 0.5))

            } else {
                LFMatrix[i][j] = 0
            }
        }
    }

    return LFMatrix
}

/**
 * Parses a n*n two dimensional LFMatrix into a single array with n*n total cells, where the sequence is one column
 *
 * from top to bottom, each value is added to the array, then next column.
 * @param $LFMatrix a n*n logistic function matrix
 * @returns {Array} a array with a length of observers^2 containing the LFMMatrix values
 */
function parseLFMValues($LFMatrix, $category) {
    var LFMValues = []
    //Iterates through each cell and adds them to the LFMValues array
    for (var i = 0; i < $LFMatrix.length; i++) {
        for (var j = 0; j < $LFMatrix[i].length; j++) {
            //Skips empty cells (diagonal in table)
            if (i != j || $category) {
                LFMValues.push($category ? $LFMatrix[i][j] : $LFMatrix[j][i])
            }
        }
    }
    return LFMValues
}

/**
 * Calculates Z-score values based on a percentage matrix using the normsInv function
 *
 * @param $percentageMatrix percentage matrix as a n*n array
 * @returns {Array} a single array with z-scores with a length of observations^2
 */
function calculateZScoreValues($percentageMatrix, $category) {
    var ZScoreValues = []
    var feedback = 0 //If there has to be feedback to user based on results

    //Iterates through each cell and adds them to the ZScoreValues array
    for (var i = 0; i < $percentageMatrix.length; i++) {
        for (var j = 0; j < $percentageMatrix[i].length; j++) {

            //Skips empty cells (diagonal in table)
            if (i != j || $category) {
                if ($percentageMatrix[i][j] == 1) {
                    ZScoreValues.push(3)
                    feedback = 1
                } else if ($percentageMatrix[i][j] == 0) {
                    ZScoreValues.push(-3)
                    feedback = 1
                } else {
                    ZScoreValues.push(normsInv($category ? $percentageMatrix[i][j] : $percentageMatrix[j][i]))
                }
            }
        }
    }

    return [ZScoreValues, feedback]
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
        return 0

    q = 1.0 - q;

    var p = (q > 0.0 && q < 0.5) ? q : (1.0 - q)
    var t = Math.sqrt(Math.log(1.0 / Math.pow(p, 2.0)))

    var c0 = 2.515517
    var c1 = 0.802853
    var c2 = 0.010328

    var d1 = 1.432788
    var d2 = 0.189269
    var d3 = 0.001308

    //Some magic going on here
    var x = t - (c0 + c1 * t + c2 * Math.pow(t, 2.0)) /
        (1.0 + d1 * t + d2 * Math.pow(t, 2.0) + d3 * Math.pow(t, 3.0))

    if (q > .5)
        x *= -1.0

    return x
}

/**
 * @author Trent Richardson
 * @author http://trentrichardson.com/2010/04/06/compute-linear-regressions-in-javascript/
 * @param y array of all known y values
 * @param x array of all known x values
 * @returns {{array}} array with slope, intercept and r2
 */
function calculateSlope(y, x) {
    var lr = {}
    var n = y.length
    var sum_x = 0
    var sum_y = 0
    var sum_xy = 0
    var sum_xx = 0
    var sum_yy = 0

    for (var i = 0; i < y.length; i++) {
      if (y[i] < 3 && y[i] > -3)
      {
        sum_x += x[i]
        sum_y += y[i]
        sum_xy += (x[i] * y[i])
        sum_xx += (x[i] * x[i])
        sum_yy += (y[i] * y[i])
      }
    }

    lr['slope'] = (n * sum_xy - sum_x * sum_y) / (n * sum_xx - sum_x * sum_x)
    lr['intercept'] = (sum_y - lr.slope * sum_x) / n
    lr['r2'] = Math.pow((n * sum_xy - sum_x * sum_y) / Math.sqrt((n * sum_xx - sum_x * sum_x) * (n * sum_yy - sum_y * sum_y)), 2)

    return lr
}

/**
 * Calculates Z-score matrix by multiplying the $LFMatrix with the slope
 *
 * @param $LFMatrix the LFMatrix as a n*n array
 * @param $slope the slope as a decimal number
 * @returns {Array} a n*n array with Z-score values
 */
function calculateZScoreMatrix($LFMatrix, $slope, $category) {
    var ZScoreMatrix = new Array($LFMatrix.length)

    //Iterates through each cell and multiplies the current LFMatrix values with the slope value
    for (var i = 0; i < ZScoreMatrix.length; i++) {
        ZScoreMatrix[i] = []

        for (var j = 0; j < $LFMatrix[i].length; j++) {

            //Only calculates values for non empty cells
            if (i != j || $category) {
                ZScoreMatrix[i][j] = $LFMatrix[i][j] * $slope
            } else {
                ZScoreMatrix[i][j] = 0
            }
        }
    }

    return ZScoreMatrix
}

/**
 * Calculates the mean Z-score for each image, then returns the results as an array
 *
 * @param $ZScoreMatrix Z-score matrix as a n*n array
 * @returns {Array} a single array returning all mean Z-scores where the results are ordered in parallel with images
 */
function calculateMeanZScore($ZScoreMatrix, $category) {
    var meanZScores = []
    var totalZScore

    // Iterates through all cells and finds the mean Z-score for each image by adding Z-scores then dividing by each
    // pair the image was a part of
    for (var j = 0; j < $ZScoreMatrix.length; j++) {
      totalZScore = 0

      for (var i = 0; i < $ZScoreMatrix[0].length; i++) {
        totalZScore += $ZScoreMatrix[j][i]
      }

      meanZScores.push(totalZScore / ($ZScoreMatrix.length - 1))
    }

    return meanZScores
}

/**
 * Calculates confidence intervals for each image used in the image set
 *
 * @param $frequencyMatrix n*n matrix
 * @returns {Array} Array with n elements, one for each image, with confidence intervals for each respective image
 */
function calculateSDMatrix($frequencyMatrix) {
    var length = $frequencyMatrix.length

    //Initialize array
    var SDArray = new Array(length)

    //Iterates through matrix
    for (var i = 0; i < length; i++) {
        var total = 0

        //Iterates through the row as well as the column to sum the scores for each image
        for (var j = 0; j < length; j++) {
            total += $frequencyMatrix[i][j]
            total += $frequencyMatrix[j][i]
        }

        SDArray[i] = 1.96 * (1 / Math.sqrt(2)) / Math.sqrt(total / (length - 1))
    }

    return SDArray
}

/**
 * Calculates confidence intervals for each image used in the image set. From the paper "Empirical Formula for Creating Error Bars for the Method of Paired Comparison" by Ethan D. Montag
 *
 * @param $frequencyMatrix n*n matrix
 * @returns {Array} Array with n elements, one for each image, with confidence intervals for each respective image
 */

function calculateSDMatrixMontag($frequencyMatrix) {
    var length = $frequencyMatrix.length
    var b1 = 1.76
    var b2 = -3.08
    var b3 = -0.613
    var b4 = 2.55
    var b5 = -0.491

    //Initialize array
    var SDArray = new Array(length)

    //Iterates through matrix
    for (var i = 0; i < length; i++) {
        var total = 0

        //Iterates through the row as well as the column to sum the scores for each image
        for (var j = 0; j < length; j++) {
            total += $frequencyMatrix[i][j]
            total += $frequencyMatrix[j][i]
        }

        var nobs = total / (length - 1)
        SDArray[i] = 1.96 * (b1 * Math.pow((length - b2), b3) * Math.pow((nobs - b4), b5))
    }

    return SDArray
}

/**
 * Creates a pair comparison result matrix from ranking data where table[i][j] refers to
 * the number of times image[i] was chosen over image[j]k
 *
 * @param $data Rank order data where data[person][imageRank] corresponds to the rank of one image
 * @returns {Array} Result matrix
 */
function convertRankToPair ($data) {
    // Determines the number of images in the image set
    var max = 0
    while (typeof $data[0][max] != 'undefined') max++

    var table = []
    for (var x = 0; x < max; x++) {
      table[x] = []
    }

    // For each person, increment each cell[i][j] where image i has a higher rank than j
    for (var i = 0; i < max; i++) {
        for (var j = 0; j < max; j++) {
            table[i][j] = 0
            $data.forEach(function (person, k) {
                if (person[i] < person[j])
                    table[i][j]++
            });
        }
    }
    return table
}

/**
 * http://stackoverflow.com/questions/8668174/indexof-method-in-an-object-array
 */
function arrayObjectIndexOf(myArray, searchTerm, property) {
    for (var i = 0, len = myArray.length; i < len; i++) {
        if (myArray[i][property] === searchTerm)
            return i
    }
    return -1
}

export {
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
  normsInv,
  calculateSlope,
  calculateZScoreMatrix,
  calculateMeanZScore,
  calculateSDMatrix,
  calculateSDMatrixMontag,
  convertRankToPair,
  arrayObjectIndexOf
}

/* eslint-enable */
