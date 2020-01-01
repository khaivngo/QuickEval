<template>
  <div class="qe-highcharts-container">
    <highcharts v-if="chartOptions.series.length !== 0" :options="chartOptions"></highcharts>
  </div>
</template>

<script>
import { Chart } from 'highcharts-vue'
import Highcharts from 'highcharts'
import exportingInit from 'highcharts/modules/exporting'
import highchartsMoreInit from 'highcharts/highcharts-more'

exportingInit(Highcharts)
highchartsMoreInit(Highcharts)

export default {
  components: {
    highcharts: Chart
  },

  props: {
    series: {
      type: Array,
      default: function () {
        return []
      }
    }
  },

  watch: {
    series (newVal) {
      newVal.forEach((serie, index) => {
        this.addSeries(serie, index)
      })
    }
  },

  data () {
    return {
      chartOptions: {
        title: {
          text: ''
        },

        chart: {
          // type: 'scatter',
          // zoomType: 'xy'
        },

        credits: {
          enabled: false
        },

        series: [],

        xAxis: [],

        yAxis: {
          title: {
            text: 'Z-score'
          },
          ceiling: 5,
          floor: -5,
          tickInterval: 0.5,
          allowDecimals: true,
          plotLines: [{
            value: 932, // what's this?
            color: 'red',
            width: 1,
            label: {
              text: 'Theoretical mean: 932',
              align: 'center',
              style: {
                color: 'gray'
              }
            }
          }]
        },

        plotOptions: {
          series: {
            cursor: 'pointer',
            events: {
              legendItemClick: (event) => {
                this.changeCategories(event)
              }
            }
          },
          errorbar: {
            // fillColor: '#F0F0E0',
            // lineWidth: 2,
            // medianColor: '#0C5DA5',
            // medianWidth: 3,
            // stemColor: '#A63400',
            // stemDashStyle: 'dot',
            // stemWidth: 1,
            // whiskerColor: '#3D9200',
            // whiskerLength: '20%',
            // whiskerWidth: 3
          }
        }
      }
    }
  },
  methods: {
    changeCategories (event) {
      if (this.chartOptions.xAxis[event.target.userOptions.labelId].visible === true) {
        this.chartOptions.xAxis[event.target.userOptions.labelId].visible = false
      } else {
        this.chartOptions.xAxis[event.target.userOptions.labelId].visible = true
      }
    },
    addSeries (zScoreArray, index) {
      var meanValues = []
      var highLows = []

      for (var i = 0; i < zScoreArray.zScores[2].length; i++) {
        meanValues.push(zScoreArray.zScores[1][i])
        highLows.push([zScoreArray.zScores[0][i], zScoreArray.zScores[2][i]])
      }

      this.chartOptions.series.push({
        labelId: index,
        name: zScoreArray.imageSet.title,
        type: 'scatter',
        data: meanValues,
        marker: {
          // fillColor: 'white',
          // strokeColor: 'red',
          // lineWidth: 6,
          radius: 5
        },
        tooltip: {
          headerFormat: '',
          pointFormat: 'Mean z-score: <b>{point.y}</b>'
        },
        xAxis: index
      })

      this.chartOptions.series.push({
        type: 'errorbar',
        data: highLows,
        stemWidth: 2,
        whiskerWidth: 2,
        tooltip: {
          // headerFormat: '<em>{point.name}</em><br/>',
          headerFormat: '',
          pointFormat: `
            Limit high: <b>{point.high}</b><br>
            Limit low: <b>{point.low}</b>
          `
        }
      })

      // push the xAxis filename labels, one row for each image set
      this.chartOptions.xAxis.push({
        categories: zScoreArray.label,
        title: {
          text: zScoreArray.imageSet.title
        },
        labels: {
          enabled: true,
          // limit length of labels to 15 characters
          formatter: function () {
            return this.value.toString().substring(0, 15)
          }
          // step: 1
        },
        visible: true
      })

      // only display the first series
      // this.chartOptions.series.forEach((serie) => {
      //   serie.visible = false
      // })
      // this.chartOptions.series[0].visible = true
      // this.chartOptions.series[1].visible = true

      // this.chartOptions.title.text = ''
      // this.chartOptions.xAxis[1].categories = this.series[0].label
      // this.chartOptions.xAxis.categories = this.series[1].label
    }
  }
}
</script>

<style lang="css">
  .qe-highcharts-container {
    height: 400px;
    margin: auto;
    min-width: 400px;
    /*max-width: 600px*/
  }
  .highcharts-title {
    font-size: 16px !important;
    font-weight: 400;
    line-height: 1 !important;
    letter-spacing: 0.02em !important;
    font-family: 'Roboto', sans-serif !important;
    text-align: left;
  }
  .highcharts-axis-labels,
  .highcharts-axis-title,
  .highcharts-legend-item  {
    font-family: 'Roboto' !important;
    /*font-size: 16px !important;*/
  }
</style>
