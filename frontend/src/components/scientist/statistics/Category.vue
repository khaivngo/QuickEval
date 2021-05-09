<template>
  <div>
    <div>
      <v-checkbox
        v-model="includeIncomplete"
        :label="`Include unfinished experiments in calculations`"
      ></v-checkbox>

      <div class="mt-6">
        <h5 class="body-2 mb-0 pb-0">Confidence interval</h5>
        <v-radio-group v-model="confidenceIntervalType">
          <v-row class="mt-0">
            <v-col cols="auto" class="pr-12 pt-0">
              <v-radio
                :label="`Standard`"
                :value="'standard'"
              ></v-radio>
            </v-col>
            <v-col cols="auto" class="pt-0">
              <v-radio
                :label="`Montag`"
                :value="'montag'"
              ></v-radio>
            </v-col>
          </v-row>
        </v-radio-group>
      </div>
    </div>

    <div class="mt-12 mb-12" v-if="loading">
      <v-progress-linear v-if="loading" indeterminate class="ma-0"></v-progress-linear>
    </div>

    <div v-show="!loading">
      <div v-if="rawDataMap.length === 0 && zScoreMap.length === 0">
        Not enough data yet to calculate statistics.
      </div>

      <h2 class="mb-3 mt-12 pt-6">
        Scatter and errorbar plot
      </h2>

      <h3 class="text-h6 mb-6 mt-4 font-weight-light">
        <span v-for="(group, gIndex) in sequences" :key="gIndex">
          {{ group.picture_set.title }}<span v-if="gIndex !== sequences.length - 1">,</span>
        </span>
      </h3>

      <ScatterPlot :series="plotData" class="mb-12"/>

      <h2 class="mb-3 mt-12 pt-12">
        Raw data
      </h2>

      <div v-for="(group, gIndex) in sequences" :key="gIndex">
        <h3 class="text-h6 mb-3 mt-8 font-weight-light">
          {{ group.picture_set.title }}
        </h3>

        <div class="pa-1 d-flex justify-center align-center qe-table-title">
          <h4 class="text-center">Number of times selected</h4>
          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-btn icon v-on="on">
                <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
              </v-btn>
            </template>
            <div class="pl-2 pr-2 pt-3 pb-3 body-1">
              Number of times each image/category combination is selected.
            </div>
          </v-tooltip>
        </div>
        <table class="table bordered hovered">
          <thead>
            <tr>
              <th>Image</th>
              <th v-for="(cat, m) in group.picture_set.pictures[0].categories" :key="m" class="overflow-wrap">
                {{ cat.category.title }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(picture, c) in group.picture_set.pictures" :key="c">
              <td class="overflow-wrap"><b>{{ picture.name }}</b></td>
              <td v-for="(category, cIndex) in picture.categories" :key="cIndex">
                {{ category.result.length }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <h2 class="mb-3 mt-12 pt-12">
        Z-Scores
      </h2>

      <div v-for="(group, b) in sequences" :key="group.id">
        <!-- <h3 class="text-h6 mb-3 mt-12">Z-Scores</h3> -->
        <h3 class="text-h6 mb-3 mt-8 font-weight-light">
          {{ group.picture_set.title }}
        </h3>

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
            <tr v-for="(y, j) in group.picture_set.pictures" :key="y.id">
              <td class="overflow-wrap"><b>{{ y.name }}</b></td>

              <td>{{ isNumber(zScoreMap[b][0][j]) }}</td>
              <td>{{ isNumber(zScoreMap[b][1][j]) }}</td>
              <td>{{ isNumber(zScoreMap[b][2][j]) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Heatmap/>
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
  calculateSDMatrix,
  calculateSDMatrixMontag
} from '@/maths.js'
import ScatterPlot from '@/components/scientist/HighchartsScatterPlot'
import { isNumber } from '@/helpers.js'
import Heatmap from '@/components/scientist/Heatmap'

const config = {}
const math = create(all, config)

export default {
  components: {
    ScatterPlot,
    Heatmap
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

      sequences: [],
      loading: false,

      includeIncomplete: false,
      confidenceIntervalType: 'standard'
    }
  },

  watch: {
    includeIncomplete (newVal, oldVal) {
      if (oldVal !== null) {
        localStorage.setItem(this.$route.params.id + '-includeIncomplete', newVal)
      }
      // re-calculate statistics
      this.init()
    },
    confidenceIntervalType (newVal, oldVal) {
      this.confidenceIntervalType = newVal
      // re-calculate statistics
      this.init()
    }
  },

  created () {
    const incomplete = localStorage.getItem(this.$route.params.id + '-includeIncomplete')
    // convert to boolean, so the checkbox will work. replace with json?
    if (incomplete === 'false') {
      this.includeIncomplete = false
    } else if (incomplete === 'true') {
      this.includeIncomplete = true
    }

    this.init()
  },

  methods: {
    isNumber,

    init () {
      this.loading = true

      this.$axios.post(`/result-categories/${this.$route.params.id}/statistics`, {
        includeIncomplete: this.includeIncomplete
      }).then(data => {
        this.resultsArray = null
        this.rawDataMap = []
        this.zScoreMap = []
        this.plotData = []

        let answers = data.data.results
        let sequences = data.data.imagesetSequences

        sequences.forEach(sequence => {
          sequence.picture_set.pictures.forEach(image => {
            image.categories.forEach(item => {
              answers.forEach(answer => {
                if (answer.category_id === item.category_id && answer.picture_id === image.id) {
                  item.result.push(answer)
                }
              })
            })
          })
        })

        this.sequences = sequences

        sequences.forEach(sequence => {
          let set = []
          sequence.picture_set.pictures.forEach(image => {
            let row = []
            image.categories.forEach(categ => {
              // categ.forEach(result => {
              row.push(categ.result.length)
              // })
            })
            set.push(row)
          })

          // calc z-scores
          let zScoreArray = this.calculatePlotsCategory(set, true)

          // store calculated z-scores for image sets
          this.zScoreMap.push(zScoreArray)

          this.plotData.push({
            imageSet: sequence.picture_set,
            // only get the file names
            label: sequence.picture_set.pictures.map(obj => obj.name),
            zScores: zScoreArray
          })
        })

        this.loading = false
      }).catch(error => {
        console.log(error)
        this.loading = false
      })
    },

    /* eslint-disable */
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
      var breakC = 0
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
        // window.alert(`
        //   Not enough data to calculate Z-scores.
        //   In order to calculate z-scores at least one row needs to be complete.
        //   All values are set to '0'.
        // `)
      } else { // If we can invert it, then do the calculations.
        var Ytemp = math.inv(Xtemp2)

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

        // var SDArray = calculateSDMatrix($frequencyMatrix)
        // switch (this.confidenceIntervalType) {
        //   case 'montag':
        //     var SDArray = calculateSDMatrixMontag($frequencyMatrix)
        //     break
        //   default:
        //     var SDArray = calculateSDMatrix($frequencyMatrix)
        // }
        
        if (this.confidenceIntervalType === 'montag') {
          var SDArray = calculateSDMatrixMontag($frequencyMatrix)
        } else {
          var SDArray = calculateSDMatrix($frequencyMatrix)
        }

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
  .qe-table-title {
    border-top: 1px solid #ddd;
    border-right: 1px solid #ddd;
    border-left: 1px solid #ddd;
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
