<template>
  <div>
    <div>
      <v-checkbox
        v-model="includeIncomplete"
        :label="`Include unfinished experiments in calculations`"
      ></v-checkbox>
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
          {{ results.imageSets[k].title }}<span v-if="k !== rawDataMap.length - 1">,</span>
        </span>
      </h3>

      <ScatterPlot
        :series="plotData"
      />

      <!-- Raw data -->
      <div v-if="rawDataMap.length > 0 && zScoreMap.length > 0 && results.resultsForEachImageSet.length > 0">
        <h2 class="mb-3 mt-12 pt-12">Raw data</h2>
        <div
          v-for="(imageSet, f) in rawDataMap"
          :key="f"
        >
          <h3 class="text-h6 mb-3 mt-8 font-weight-light">
            {{ results.imageSets[f].title }}
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
      </div>

      <!-- Z-scores -->
      <div v-if="rawDataMap.length > 0 && zScoreMap.length > 0 && results.resultsForEachImageSet.length > 0">
        <h2 class="mb-3 mt-12 pt-12">Z-score</h2>
        <div
          v-for="(imageSet, p) in rawDataMap"
          :key="p"
        >
          <h3 class="text-h6 mb-3 mt-8 font-weight-light">
            {{ results.imageSets[p].title }}
          </h3>

          <p v-if="zScoreMap[p][3] == 1">
            Need more observer data to calculate z-scores properly.
          </p>

          <table v-if="zScoreMap[p][3] == 0" class="table bordered hovered">
            <thead>
              <tr>
                <th class="overflow-wrap">Title</th>
                <th class="overflow-wrap">Low CI limit</th>
                <th class="overflow-wrap">Mean z-score</th>
                <th class="overflow-wrap">High CI limit</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(y, j) in results.imagesForEachImageSet[p]" :key="j">
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

    <Heatmap :artifacts="results.artifact"/>
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
  arrayObjectIndexOf
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
      results: {
        artifact: [],
        resultsForEachImageSet: []
      },
      resultsArray: null,
      rawDataMap: [],
      zScoreMap: [],
      plotData: [],

      loading: false,

      includeIncomplete: null
    }
  },

  watch: {
    includeIncomplete (newVal, oldVal) {
      // console.log(oldVal)
      // console.log(newVal)
      // if (this.includeIncomplete === )

      if (this.includeIncomplete !== null && oldVal !== null && newVal !== null) {
        localStorage.setItem(this.$route.params.id + '-includeIncomplete', newVal)
      }

      // re-calculate statistics
      this.init()
    }
  },

  created () {
    const incomplete = localStorage.getItem(this.$route.params.id + '-includeIncomplete')
    // replace with json?
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

      this.$axios.post(`/paired-result/${this.$route.params.id}/statistics`, {
        includeIncomplete: this.includeIncomplete
      }).then(data => {
        this.results = data.data

        this.resultsArray = null
        this.rawDataMap = []
        this.zScoreMap = []
        this.plotData = []

        this.results.imageSets.forEach((imageSet, i) => {
          // create an empty this.resultsArray array with the length of the number of images in the image set
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
              let row = arrayObjectIndexOf(this.results.imagesForEachImageSet[i], result.pictureId,  'id')
              let column = arrayObjectIndexOf(this.results.imagesForEachImageSet[i], result.wonAgainst, 'id')
              this.resultsArray[row][column] += 1 // result['won'] here?
            })
          }

          // save all raw data maps
          this.rawDataMap.push(this.resultsArray)

          // stores calculated data for one image set
          let zScoreArray = this.calculatePlots(this.resultsArray)
          this.zScoreMap.push(zScoreArray)

          // add z-score and image set names for one image set to the array used by the scatter plot
          this.plotData.push({
            imageSet: imageSet,
            label: this.results.imagesForEachImageSet[i].map(obj => obj.name), // only get the file names
            zScores: zScoreArray
          })
        })

        this.loading = false
      }).catch(() => {
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
