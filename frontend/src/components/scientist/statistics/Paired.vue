<template>
  <div>
    <div>
      <h4>Settings</h4>
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
        <span v-for="(imageSet, k) in rawDataMap" :key="k">
          {{ resultsByImageSet[k].picture_set.title }}<span v-if="k !== rawDataMap.length - 1">,</span>
        </span>
      </h3>

      <ScatterPlot
        :series="plotData"
      />

      <!-- Raw data -->
      <!-- v-if="rawDataMap.length > 0 && zScoreMap.length > 0 && results.resultsForEachImageSet.length > 0" -->
      <div>
        <h2 class="mb-3 mt-12 pt-12">Raw data</h2>
        <div
          v-for="(imageSet, f) in rawDataMap"
          :key="f"
        >
          <h3 class="text-h6 mb-3 mt-8 font-weight-light">
            {{ resultsByImageSet[f].picture_set.title }}
          </h3>

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
                    <div class="pl-2 pr-2 pt-3 pb-3 body-1">
                      Images on the y-axis are the images picked.<br><br>
                      For example: if the value of image x and image y is 2,<br>the image on the y axis is the one picked 2 times.
                    </div>
                  </v-tooltip>
                </th>
                <th v-for="(y, j) in resultsByImageSet[f].picture_set.pictures" :key="j" class="overflow-wrap">
                  {{ y.name }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(y, j) in resultsByImageSet[f].picture_set.pictures" :key="j">
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
      </div>

      <!-- Z-scores -->
      <!-- v-if="rawDataMap.length > 0 && zScoreMap.length > 0 && results.resultsForEachImageSet.length > 0" -->
      <div>
        <h2 class="mb-3 mt-12 pt-12">Z-score</h2>
        <div
          v-for="(imageSet, p) in rawDataMap"
          :key="p"
        >
          <h3 class="text-h6 mb-3 mt-8 font-weight-light">
            {{ resultsByImageSet[p].picture_set.title }}
          </h3>

          <p v-if="zScoreMap[p][3] == 1">
            Need more observer data to calculate z-scores properly.
          </p>

          <!-- v-if="zScoreMap[p][3] == 0" -->
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
              <tr v-for="(y, j) in resultsByImageSet[p].picture_set.pictures" :key="j">
                <td class="overflow-wrap"><b>{{ y.name }}</b></td>

                <td>{{ isNumber(zScoreMap[p][0][j]) }}</td>
                <td>{{ isNumber(zScoreMap[p][1][j]) }}</td>
                <td>{{ isNumber(zScoreMap[p][2][j]) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <Heatmap/>
  </div>
</template>

<script>
import {
  calculateZScoreValues,
  parseLFMValues,
  calculateLFMatrix,
  calculatePercentageMatrix,
  calculateCumulative,
  calculateSlope,
  calculateZScoreMatrix,
  calculateMeanZScore,
  calculateSDMatrix,
  calculateSDMatrixMontag
} from '@/maths.js'
import ScatterPlot from '@/components/scientist/HighchartsScatterPlot'
import Heatmap from '@/components/scientist/Heatmap'
import { isNumber } from '@/helpers.js'

export default {
  components: {
    ScatterPlot,
    Heatmap
  },

  data () {
    return {
      resultsByImageSet: {
        resultsForEachImageSet: []
      },
      resultsMatrix: null,
      rawDataMap: [],
      zScoreMap: [],
      plotData: [],

      loading: false,

      includeIncomplete: null,
      confidenceIntervalType: 'standard'
    }
  },

  watch: {
    includeIncomplete (newVal, oldVal) {
      if (this.includeIncomplete !== null && oldVal !== null && newVal !== null) {
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

      this.$axios.post(`/result-pairs/${this.$route.params.id}/statistics`, {
        includeIncomplete: this.includeIncomplete
      }).then(data => {
        this.resultsByImageSet = data.data.imageSetSequences

        this.resultsMatrix = null
        this.rawDataMap = []
        this.zScoreMap = []
        this.plotData = []

        this.resultsByImageSet.forEach((sequence, i) => {
          // create a two-dimensional array of 0 values,
          // with the dimensions of image set length * image set length
          this.resultsMatrix = new Array(sequence.picture_set.pictures.length)
          for (var k = 0; k < this.resultsMatrix.length; k++) {
            this.resultsMatrix[k] = new Array(sequence.picture_set.pictures.length)

            for (var j = 0; j < this.resultsMatrix[k].length; j++) {
              this.resultsMatrix[k][j] = 0
            }
          }

          // fill our two-dimensional array with occurences of selected image combinations, by incrementing when we find a match
          // images on the y-axis (column) are the images picked
          sequence.results.forEach((result, index) => {
            let column = sequence.picture_set.pictures.findIndex(obj => obj.id === result.wonAgainst)
            let row = sequence.picture_set.pictures.findIndex(obj => obj.id === result.pictureId)
            this.resultsMatrix[row][column] += 1
          })

          // save the raw data map for the image set
          this.rawDataMap.push(this.resultsMatrix)

          // calculate z-scores from the raw data map
          let zScoreArray = this.calculatePlots(this.resultsMatrix)
          this.zScoreMap.push(zScoreArray)

          // add z-score and image set names for one image set to the array used by the scatter plot
          this.plotData.push({
            imageSet: sequence.picture_set,
            label: sequence.picture_set.pictures.map(obj => obj.name), // only get the file names
            zScores: zScoreArray
          })
        })
      }).catch((err) => {
        console.log(err)
      }).finally(() => {
        this.loading = false
      })
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

      if (this.confidenceIntervalType === 'montag') {
        var SDArray = calculateSDMatrixMontag($frequencyMatrix)
      } else {
        var SDArray = calculateSDMatrix($frequencyMatrix)
      }

      // Calculates the high confidence interval limits
      var highCILimit = meanZScore.map(function (num, i) {
          return parseFloat((num + SDArray[i]).toFixed(3))
      })

      // Calculates the low confidence interval limits
      var lowCILimit = meanZScore.map(function (num, i) {
          return parseFloat((num - SDArray[i]).toFixed(3))
      })

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
