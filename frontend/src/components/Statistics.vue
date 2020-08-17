<template>
  <div class="qe-statistics-container">
    <h2 class="mb-6">
      Statistics
    </h2>

    <ScatterPlot :series="plotData" v-if="results.resultsForEachImageSet.length > 0"/>

    <div v-if="rawDataMap.length === 0 && zScoreMap.length === 0">
      Not enough data yet to calculate statistics.
    </div>

    <v-tabs v-if="rawDataMap.length > 0 && zScoreMap.length > 0 && results.resultsForEachImageSet.length > 1" v-model="activeTab">
      <v-tab
        v-for="(imageSet, index) in results.imageSets"
        :key="index"
        :centered="true"
        ripple
        class="text-none"
      >
        {{ imageSet.title }}
      </v-tab>
      <v-tab-item
        v-for="(imageSet, f) in rawDataMap"
        :key="f"
      >
        <v-card flat>
          <div class="mt-3">
            <h3 class="text-h6 mb-3 mt-8">Raw data</h3>
            <!-- {{ results.imageSets[f].title }} -->

            <table class="table bordered hovered body-1">
              <thead>
                <tr>
                  <th>
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on">
                          <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
                        </v-btn>
                      </template>
                      <div class="pl-2 pr-2 body-1">
                        Images on the y-axis are the images picked.<br>
                        For example if the value of image x and image y is 2, the image on the y axis is the one picked twice.
                      </div>
                    </v-tooltip>
                  </th>

                  <th v-for="(y, j) in results.imagesForEachImageSet[f]" :key="j" class="overflow-wrap">
                    {{ y.name }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(y, j) in results.imagesForEachImageSet[f]" :key="j">
                  <td class="overflow-wrap"><b>{{ y.name }}</b></td>

                  <td v-for="(score, scoreIndex) in imageSet[j]" :key="scoreIndex">
                    <span v-if="score > 0">
                      {{ score }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-5">
            <!-- <h3 class="headline">Z-Scores: {{ results.imageSets[f].title }}</h3> -->
            <h3 class="text-h6 mb-3 mt-12">Z-Scores</h3>

            <!-- <div style="width: 150px;">
              <v-img :src="$UPLOADS_FOLDER + results.imageUrl[f].path" alt="" contain></v-img>
            </div> -->

            <p v-if="zScoreMap[f][3] == 1">
              Warning: Need more observer-data to be calculated properly.
            </p>

            <table class="table bordered hovered">
              <thead>
                <tr>
                  <th class="overflow-wrap">Title</th>
                  <th class="overflow-wrap">Low CI limit</th>
                  <th class="overflow-wrap">Mean z-score</th>
                  <th class="overflow-wrap">High CI limit</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(y, j) in results.imagesForEachImageSet[f]" :key="j">
                  <td class="overflow-wrap"><b>{{ y.name }}</b></td>

                  <td>{{ ifNotNaN(zScoreMap[f][0][j]) }}</td>
                  <td>{{ ifNotNaN(zScoreMap[f][1][j]) }}</td>
                  <td>{{ ifNotNaN(zScoreMap[f][2][j]) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </v-card>
      </v-tab-item>
    </v-tabs>

    <!-- <div>
      <h3 class="title mb-3">Raw data</h3>

      <table class="table bordered hovered">
        <thead>
          <tr>
            <th></th>
            <th v-for="(rRes, m) in rankedResults.resultsForEachImageSet[0]" :key="m" class="overflow-wrap">
              {{ rRes.name }}
            </th>
          </tr>
        </thead>
        <tbody>
          <template v-for="(y, c) in rankedResults.resultsForEachImageSet">
            <tr>
              <td class="overflow-wrap"><b>{{ y[c].user }}</b></td>
              <td v-for="(g, h) in y">{{ g.ranking }}</td>
            </tr>
          </template>
        </tbody>
      </table>
    </div> -->

    <div v-if="rawDataMap.length > 0 && zScoreMap.length > 0 && results.resultsForEachImageSet.length === 1" style="margin-top: 50px;">
      <v-card flat>
        <div class="mt-3">
          <h3 class="text-h6 mb-3 mt-8">Raw data</h3>
          <!-- {{ results.imageSets[f].title }} -->

          <table class="table bordered hovered body-1">
            <thead>
              <tr>
                <th>
                  <v-tooltip top>
                    <template v-slot:activator="{ on }">
                      <v-btn icon v-on="on">
                        <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
                      </v-btn>
                    </template>
                    <div class="pl-2 pr-2 body-1">
                      Images on the y-axis are the images picked.<br>
                      For example if the value of image x and image y is 2, the image on the y axis is the one picked twice.
                    </div>
                  </v-tooltip>
                </th>

                <th v-for="(y, j) in results.imagesForEachImageSet[0]" :key="j" class="overflow-wrap">
                  {{ y.name }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(y, j) in results.imagesForEachImageSet[0]" :key="j">
                <td class="overflow-wrap"><b>{{ y.name }}</b></td>

                <td v-for="(score, scoreIndex) in rawDataMap[0][j]" :key="scoreIndex">
                  <span v-if="score > 0">
                    {{ score }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div>
          <!-- <h3 class="headline">Z-Scores: {{ results.imageSets[f].title }}</h3> -->
          <h3 class="text-h6 mb-3 mt-12">Z-Scores</h3>

          <!-- <div style="width: 150px;">
            <v-img :src="$UPLOADS_FOLDER + results.imageUrl[f].path" alt="" contain></v-img>
          </div> -->

          <p v-if="zScoreMap[0][3] == 1">
            Warning: Need more observer-data to be calculated properly.
          </p>

          <table class="table bordered hovered">
            <thead>
              <tr>
                <th class="overflow-wrap">Title</th>
                <th class="overflow-wrap">Low CI limit</th>
                <th class="overflow-wrap">Mean z-score</th>
                <th class="overflow-wrap">High CI limit</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(y, j) in results.imagesForEachImageSet[0]" :key="j">
                <td class="overflow-wrap"><b>{{ y.name }}</b></td>

                <td>{{ ifNotNaN(zScoreMap[0][0][j]) }}</td>
                <td>{{ ifNotNaN(zScoreMap[0][1][j]) }}</td>
                <td>{{ ifNotNaN(zScoreMap[0][2][j]) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </v-card>
    </div>

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
  // convertRankToPair
} from '@/maths.js'
import ScatterPlot from '@/components/scientist/HighchartsScatterPlot'

const config = {}
const math = create(all, config)

export default {
  components: {
    ScatterPlot
  },
  data () {
    return {
      results: {
        resultsForEachImageSet: []
      },
      resultsArray: null,
      imageSets: [],
      rawDataMap: [],
      zScoreMap: [],
      activeTab: null,
      plotData: [],

      rankedResults: []
    }
  },
  created () {
    // var experimentType = 2

    // if (experimentType === 1) {
    this.$axios.get(`/paired-result/${this.$route.params.id}/statistics`).then(data => {
      this.results = data.data

      this.results.imageSets.forEach((imageSet, i) => {
        // create an empty this.resultsArray array with the length of data['imagesForEachImageSet'][i].length
        // push empty this.resultsArray[it] array with length of data['imagesForEachImageSet'][i].length in each spot of this.resultsArray
        // push a 0 value in every slot of the sub arrays
        this.resultsArray = new Array(this.results.imagesForEachImageSet[i].length)
        for (var it = 0; it < this.resultsArray.length; it++) {
          this.resultsArray[it] = new Array(this.results.imagesForEachImageSet[i].length)

          for (var ita = 0; ita < this.resultsArray[it].length; ita++) {
            this.resultsArray[it][ita] = 0
          }
        }

        if (this.results.resultsForEachImageSet.length) {
          this.results.resultsForEachImageSet[i].forEach((result, index) => {
            let row    = arrayObjectIndexOf(this.results.imagesForEachImageSet[i], result.pictureId,  'id')
            let column = arrayObjectIndexOf(this.results.imagesForEachImageSet[i], result.wonAgainst, 'id')
            this.resultsArray[row][column] += 1 // result['won'] here?
          })
        }

        // save all raw data maps
        this.rawDataMap.push(this.resultsArray)

        // stores calculated data for one picture set
        let zScoreArray = this.calculatePlots(this.resultsArray)
        this.zScoreMap.push(zScoreArray)

        this.plotData.push({
          imageSet: imageSet,
          label: this.results.imagesForEachImageSet[i].map(obj => obj.name), // only get the file names
          zScores: zScoreArray
        })
      })
    })
    // } else if (experimentType === 2) {
    //   this.$axios.get(`/rank-order-result/${this.$route.params.id}/statistics`).then(data => {
    //     this.results = data.data
    //     this.rankedResults = data.data

    //     this.results.imageSets.forEach((imageSet, i) => {

    //       // <template v-for="(y, c) in rankedResults.resultsForEachImageSet">
    //       //   <tr>
    //       //     <td class="overflow-wrap"><b>{{ y[c].user }}</b></td>
    //       //     <td v-for="(g, h) in y">{{ g.ranking }}</td>
    //       //   </tr>
    //       // </template>

    //       // var resultTable = convertRankToPair(this.rankedResults.resultsForEach[i])
    //       // calculatePlots(resultTable)
    //       // zScoreArray = calculatePlots(resultTable)

    //       // addSeries(imageTitleArray, zScoreArray, t['name'])

    //       // $("#zScores-container").append('</br></br><div id=zscore-' + roundCounter + '><h1>Z-Scores</h1><hr></div>')

    //       // //sends all imagestitles, calculated results and the name of picture set
    //       // setZScores(imageTitleArray, zScoreArray, t['name'], data['imageUrl'][i]['url'])
    //     })
    //   })
    // } else if (experimentType === 3) {
    //   //
    // }
  },
  methods: {
    ifNotNaN (value) {
      return !Number.isNaN(value) ? value : ''
    },

    /* eslint-disable */
    calculatePlots ($frequencyMatrix, $category) {
      // $frequencyMatrix = transpose($frequencyMatrix); //transposing in order to get correct z-score calculation
      var observerAmount = 0
      var cumulativeFrequencyTable
      var cumulativePercentageTable

      // Calculates number of observers
      if ($category) { // Category uses non square matrix, counts first row results
        for (var i = 0; i < $frequencyMatrix[0].length; i++) {
          observerAmount += parseInt($frequencyMatrix[0][i])
        }
      } else {
        observerAmount = $frequencyMatrix[0][1] + $frequencyMatrix[1][0]
      }

      if ($category) {
        cumulativeFrequencyTable = calculateCumulative($frequencyMatrix, true)
      }

      // Calculates a percentage matrix of the results
      var PercentageMatrix = calculatePercentageMatrix($category ? cumulativeFrequencyTable : $frequencyMatrix, observerAmount)

      // Calculates a LFMatrix of the results, using percentage matrix
      var LFMatrix = calculateLFMatrix($category ? cumulativeFrequencyTable : $frequencyMatrix, observerAmount, $category)

      // Parses the LF Matrix into a single row of results
      var LFMValues = parseLFMValues(LFMatrix, $category)

      // Calculates the Z-score values as a single array
      var ZScoreValues = calculateZScoreValues(PercentageMatrix, $category)

      var feedback = ZScoreValues[1]

      // Calculates the slope between LFM and Z-scores
      var slope = calculateSlope(ZScoreValues[0], LFMValues)

      // Calculates the Z-score matrix to be displayed using confidence interval
      var ZScoreMatrix = calculateZScoreMatrix(LFMatrix, slope['slope'], $category)

      // Calculates the mean z score values per image
      var meanZScore = calculateMeanZScore(ZScoreMatrix, $category).map(function (num) {
          return parseFloat(num.toFixed(3))
      })

      // var standardDeviation = 1.96 * (1 / Math.sqrt(2)) / Math.sqrt(observerAmount) //Must be changed

      var SDArray = calculateSDMatrix($frequencyMatrix)

      // Calculates the high confidence interval limits
      var highCILimit = meanZScore.map(function (num, i) {
          return parseFloat((num + SDArray[i]).toFixed(3))
      })

      // Calculates the low confidence interval limits
      var lowCILimit = meanZScore.map(function (num, i) {
          return parseFloat((num - SDArray[i]).toFixed(3))
      })

      return [lowCILimit, meanZScore, highCILimit, feedback]
    },

    calculatePlotsCategory ($frequencyMatrix, $category) {
      var observerAmount = 0
      var cumulativeFrequencyTable
      var cumulativePercentageTable

      // Calculates number of observers
      if ($category) { // Category uses non square matrix, counts first row results
        for (var i = 0; i < $frequencyMatrix[0].length; i++) {
          observerAmount += parseInt($frequencyMatrix[0][i])
        }
      } else {
        observerAmount = $frequencyMatrix[0][1] + $frequencyMatrix[1][0]
      }

      if ($category) {
        cumulativeFrequencyTable = calculateCumulative($frequencyMatrix, true)
      }

      // Calculates a percentage matrix of the results
      var PercentageMatrix = calculatePercentageMatrix($category ? cumulativeFrequencyTable : $frequencyMatrix, observerAmount)

      // Calculates a LFMatrix of the results, using percentage matrix
      var LFMatrix = calculateLFMatrix($category ? cumulativeFrequencyTable : $frequencyMatrix, observerAmount, $category)

      // Parses the LF Matrix into a single row of results
      var LFMValues = parseLFMValues(LFMatrix, $category)

      // Calculates the Z-score values as a single array
      var ZScoreValues = calculateZScoreValues(PercentageMatrix, $category)

      // Setting up identity matrix
      var eyeMatrix = im($frequencyMatrix[0].length - 1)

      // Setting up X matrix (code is a bit messy, but it works). Done accoring to the "Colour Engineering Toolbox" by Phil Green
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

      var Xtemp = []
      for (var i = 0; i < X[0].length; i++) {
        if (i < $frequencyMatrix[0].length - 1) {
          Xtemp[i] = 0;
        }
        else {
          Xtemp[i] = 1
        }
      }
      X.push(Xtemp)

      // initial z-scores values extracted
      var v = ZScoreValues[0]

      // reformat V to fit with analysis later
      var vv = []
      for (var j = 0; j < ($frequencyMatrix[0].length - 1); ++j) {
        for (var k = j; k < v.length; k += (PercentageMatrix[0].length)) {
          vv.push(v[k])
        }
      }
      v = vv
      v.push(0)

      var indexes = getAllIndexes(v, 3) // findng "infs"
      indexes = indexes.concat(getAllIndexes(v, -3)) // findng "infs" (also the negative ones)
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

      // if we cannot invert Xtemp2, then we cannot calculate z-scores.  Does a check here, and then display an error message
      breakC = 0
      var meanZScore = []
      var lowCILimit = []
      var highCILimit = []
      for (var i = 0; i < Xtemp2[0].length; i++) {
        if (math.sum(math.abs(Xtemp2[i])) == 0) {
          breakC = 1

          for (var j = 0; j < ZScoreValues[0].length; j++) {
            meanZScore[j] = 0 // setting values to 0 if we cannot invert Xtemp2
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
      else { // If we can invert it, then do the calculations.
        Ytemp = math.inv(Xtemp2)

        var Y = []
        var ThreeDirection = []
        for (var i = 0; i < X[0].length; i++) {
          for (var k = 0; k < Ytemp[0].length; k++) {
            ThreeDirection[k] = Ytemp[i][k]
          }
          Y[i] = dotProduct(ThreeDirection, Xtemp3)
        }
        Y = Y.slice($frequencyMatrix[0].length - 1, Y.length) // the two first numbers are category boundaries, so they are not needed here.

        var feedback = ZScoreValues[1]

        // Formatting the z-scores to have 3 decimals
        meanZScore = Y.map(function (num) {
          return parseFloat(num.toFixed(3))
        })

        // Finding standard deviation
        // var standardDeviation = 1.96 * (1 / Math.sqrt(2)) / Math.sqrt(observerAmount)  // Must be changed

        var SDArray = calculateSDMatrix($frequencyMatrix)

        // Calculates the high confidence interval limits
        highCILimit = meanZScore.map(function (num, i) {
          return parseFloat((num + SDArray[i]).toFixed(3))
        })

        // Calculates the low confidence interval limits
        lowCILimit = meanZScore.map(function (num, i) {
          return parseFloat((num - SDArray[i]).toFixed(3))
        })
      }

      return [lowCILimit, meanZScore, highCILimit, feedback]
    }
    /* eslint-enable */
  }
}
</script>

<style lang="css" scoped>
  .qe-statistics-container {
    padding-bottom: 100px;
    margin-top: 150px;
    color: #000;
  }
  .table td, .table tr {
    padding: 10px 5px;
    color: #000;
  }
  .table tr {
    border: 1px solid #ddd;
  }
  .table.bordered {
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
    border-left: 1px solid rgba(0,0,0,0.12);
    border-bottom: 1px solid rgba(0,0,0,0.12);
    padding: 10px;
    max-width: 50px;
  }
  .table.bordered tr:hover {
    background: #eee;
  }
  .overflow-wrap {
    /*overflow: hidden;*/
    overflow-wrap: break-word;
  }
</style>

<style lang="css">
  .v-tabs__item--active {
    font-weight: bold;
  }
</style>
