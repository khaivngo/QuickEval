<template>
  <div>
    <div class="mt-12 pt-12 ml-4 mr-4" v-if="loading">
      <v-progress-linear v-if="loading" indeterminate class="ma-0"></v-progress-linear>
    </div>

    <div v-if="!loading && rawDataMap.length === 0 && zScoreMap.length === 0">
      Not enough data yet to calculate statistics.
    </div>

    <h2 class="mb-3 mt-12 pt-6">
      Scatter and errorbar plot
    </h2>
    <h3 class="text-h6 mb-6 mt-4 font-weight-light">
      <span v-for="(imageSet, iIndex) in rankedResults.imageSets" :key="iIndex">
        {{ imageSet.picture_set.title }}<span v-if="iIndex !== rankedResults.imageSets.length - 1">,</span>
      </span>
    </h3>
    <ScatterPlot :series="plotData"/>

    <h2 class="mb-0 pb-0 mt-12 pt-12">Raw data</h2>
    <div v-for="(set, gIndex) in rankedResults.resultsForEachImageSet" :key="gIndex">
      <!-- <h3 class="text-h6 mb-3 mt-4">
        {{ rankedResults.imagesForEachImageSet[gIndex].picture_set.title }}
      </h3> -->
      <h3 class="text-h6 mb-3 mt-8 font-weight-light">
        {{ rankedResults.imagesForEachImageSet[gIndex].picture_set.title }}
      </h3>

      <div class="pa-1 d-flex justify-center align-center qe-table-title">
        <h4 class="text-center">Ranking</h4>
        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on">
              <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
            </v-btn>
          </template>
          <div class="pl-2 pr-2 pt-3 pb-3 body-1">
            <!--  -->
          </div>
        </v-tooltip>
      </div>
      <table class="table bordered hovered">
        <thead>
          <tr>
            <th>Observer ID</th>
            <th v-for="(image, m) in rankedResults.imagesForEachImageSet[gIndex].picture_set.pictures" :key="m" class="overflow-wrap">
              {{ image.name }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(observer, c) in set" :key="c">
            <td class="overflow-wrap">
              <b>{{ observer[0][0].experiment_result_id }}</b>
            </td>
            <template v-for="(answers) in observer">
              <td v-for="answer in answers" :key="answer.id">{{ answer.ranking }}</td>
            </template>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Z-scores -->
    <h2 class="mt-12 pt-12">Z-Scores</h2>
    <div v-for="(imageSet, b) in rankedResults.imagesForEachImageSet" :key="imageSet.id">
      <h3 class="text-h6 mb-3 mt-8 font-weight-light">
        {{ rankedResults.imagesForEachImageSet[b].picture_set.title }}
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
          <tr v-for="(y, j) in imageSet.picture_set.pictures" :key="y.id">
            <td class="overflow-wrap"><b>{{ y.name }}</b></td>

            <td>{{ isNumber(zScoreMap[b][0][j]) }}</td>
            <td>{{ isNumber(zScoreMap[b][1][j]) }}</td>
            <td>{{ isNumber(zScoreMap[b][2][j]) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
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
  convertRankToPair
} from '@/maths.js'
import ScatterPlot from '@/components/scientist/HighchartsScatterPlot'
import { isNumber } from '@/helpers.js'

export default {
  components: {
    ScatterPlot
  },

  data () {
    return {
      rawDataMap: [],
      zScoreMap: [],
      activeTab: null,
      plotData: [],

      rankedResults: []
    }
  },

  created () {
    this.loading = true
    this.$axios.get(`/rank-order-result/${this.$route.params.id}/statistics`).then(data => {
      this.results = data.data
      this.rankedResults = data.data
      console.log(this.rankedResults)

      // if (this.results.resultsForEachImageSet.length) {
      // console.log(this.rankedResults.resultsForEachImageSet)
      this.rankedResults.resultsForEachImageSet.forEach((imageSet, index) => {
        // TODO: bruk object.values
        let array = Object.entries(imageSet)
        // console.log(array)

        // TODO: JUST USE THIS WHEN IN RAW DATA TEMPLATE
        var arrayTwo = []
        array.forEach(observer => {
          observer[1].forEach(answer => {
            var objectOne = {}
            answer.forEach((rank, index) => {
              objectOne[index] = rank.ranking
            })
            arrayTwo.push(objectOne)
          })
        })

        let resultTable = convertRankToPair(arrayTwo)
        let zScoreArray = this.calculatePlots(resultTable)

        // store calculated z-scores for an image set
        this.zScoreMap.push(zScoreArray)

        this.plotData.push({
          imageSet: this.rankedResults.imagesForEachImageSet[index].picture_set,
          // only get the file names
          label: this.rankedResults.imagesForEachImageSet[index].picture_set.pictures.map(obj => obj.name),
          zScores: zScoreArray
        })
      })

      this.loading = false
    }).catch(() => {
      this.loading = false
    })
  },

  methods: {
    isNumber,

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
