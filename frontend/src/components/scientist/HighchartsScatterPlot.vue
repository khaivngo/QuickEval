<template>
  <div v-if="chartOptions.series.length !== 0" class="qe-highcharts-container">
    <!-- <h2 class="mb-8 mt-12">
      Scatter and errorbar plot
    </h2> -->

    <highcharts
      :options="chartOptions"
    ></highcharts>

    <div :style="position" style="display: none; width: 300px; z-index: 100; position: fixed;">
      <v-card class="pa-4 pb-0 d-flex">
        <v-text-field
          outlined dense
          label="Title"
          v-model="label"
        ></v-text-field>

        <v-btn @click="update" color="success" class="ml-2">
          Save
        </v-btn>
      </v-card>
    </div>

    <div class="d-flex align-center mt-4">
      <v-icon small>mdi-lightbulb-on-outline</v-icon>
      <p class="caption ma-0 ml-2 mt-1 pa-0">
        Tip: Click on the chart labels to edit the text before exporting (top right of chart). Or undo edited one's:
      </p>
      <v-btn @click="resetLabels" icon>
        <!-- Reset labels -->
        <v-icon>mdi-undo-variant</v-icon>
      </v-btn>
    </div>
  </div>
</template>

<script>
import { Chart } from 'highcharts-vue'
import Highcharts from 'highcharts'
import exportingInit from 'highcharts/modules/exporting'
import highchartsMoreInit from 'highcharts/highcharts-more'
import HighchartsCustomEvents from 'highcharts-custom-events'

exportingInit(Highcharts)
highchartsMoreInit(Highcharts)
HighchartsCustomEvents(Highcharts)

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
      // this.series = []
      this.orginalChartLabels.labels = []
      this.chartLabels = {
        labels: [],
        title: '',
        subtitle: '',
        yTitle: '',
        xTitle: ''
      }
      this.chartOptions.series = []
      this.chartOptions.xAxis = []

      newVal.forEach((serie, index) => {
        this.addSeries(serie, index)
        this.orginalChartLabels.labels.push(serie.label)
        this.chartLabels.labels.push(serie.label)
      })

      // overwrite chart labels if we have anything in storage
      let storage = localStorage.getItem(this.$route.params.id + '-labels')
      if (storage) {
        this.chartLabels = JSON.parse(storage)

        this.chartOptions.yAxis.title.text = this.chartLabels.yTitle || 'Z-score' // Chart.setTitle('cake')
        this.chartOptions.title.text = this.chartLabels.title || 'Plot'
        this.chartOptions.subtitle.text = this.chartLabels.subtitle || 'subtitle'

        this.chartLabels.labels.forEach((labels, index) => {
          this.chartOptions.xAxis[index].categories = labels
        })
      }
    }
  },

  data () {
    return {
      chartOptions: {
        title: {
          text: 'Plot',
          style: { 'cursor': 'pointer' }
        },
        subtitle: {
          text: 'subtitle',
          style: { 'cursor': 'pointer' }
        },
        yAxis: {
          title: {
            text: 'Z-score',
            style: { 'cursor': 'pointer' }
          },
          ceiling: 5,
          floor: -5,
          tickInterval: 0.5,
          allowDecimals: true
          // plotLines: [{
          //   value: 932, // The position of the line in axis units.
          //   width: 1,
          //   label: {
          //     text: 'Theoretical mean: 932',
          //     align: 'center',
          //     style: {
          //       color: 'gray'
          //     }
          //   }
          // }]
        },
        series: [],
        xAxis: [],
        plotOptions: {
          series: {
            cursor: 'pointer',
            events: {
              legendItemClick: (event) => {
                this.toggleSeries(event)
              }
            }
          },
          errorbar: {
            fillColor: '#F0F0E0',
            lineWidth: 2,
            medianColor: '#0C5DA5',
            medianWidth: 3,
            stemColor: '#A63400',
            stemDashStyle: 'dot',
            stemWidth: 1,
            whiskerColor: '#3D9200',
            whiskerLength: '20%',
            whiskerWidth: 3
          }
        },
        credits: {
          enabled: false
        }
      },

      // keep track of original chart labels for easy reset
      orginalChartLabels: {
        labels: [],
        title: 'Plot',
        subtitle: 'subtitle',
        yTitle: 'Z-score',
        xTitle: ''
      },

      // everything handling editing of chart labels
      chartLabels: {
        labels: [],
        title: '',
        subtitle: '',
        yTitle: '',
        xTitle: ''
      },
      label: '',
      position: '',
      type: '',
      currentSet: null,
      currentPos: null
    }
  },

  created () {
    var vm = this

    this.chartOptions.yAxis.title.events = {
      click: function (event) {
        vm.label = event.srcElement.innerHTML
        vm.position = `left: ${event.x - 125}px; top: ${event.y - 110}px; display: block;`
        vm.type = 'yAxisTitle'
      }
    }

    this.chartOptions.title.events = {
      click: function (event) {
        vm.label = event.srcElement.innerHTML
        vm.position = `left: ${event.x - 125}px; top: ${event.y - 110}px; display: block;`
        vm.type = 'title'
      }
    }

    this.chartOptions.subtitle.events = {
      click: function (event) {
        vm.label = event.srcElement.innerHTML
        vm.position = `left: ${event.x - 125}px; top: ${event.y - 110}px; display: block;`
        vm.type = 'subtitle'
      }
    }
  },

  methods: {
    update (type) {
      let experiment = this.$route.params.id

      if (this.type === 'title') {
        this.chartOptions.title.text = this.label
        this.chartLabels.title = this.label

        if (localStorage.getItem('cookies-preferences') === 'true') {
          let json = JSON.stringify(this.chartLabels)
          localStorage.setItem(experiment + '-labels', json)
        }
      }

      if (this.type === 'subtitle') {
        this.chartOptions.subtitle.text = this.label
        this.chartLabels.subtitle = this.label

        if (localStorage.getItem('cookies-preferences') === 'true') {
          let json = JSON.stringify(this.chartLabels)
          localStorage.setItem(experiment + '-labels', json)
        }
      }

      if (this.type === 'yAxisTitle') {
        this.chartOptions.yAxis.title.text = this.label
        this.chartLabels.yTitle = this.label

        if (localStorage.getItem('cookies-preferences') === 'true') {
          let json = JSON.stringify(this.chartLabels)
          localStorage.setItem(experiment + '-labels', json)
        }
      }

      if (this.type === 'xAxisCategories') {
        this.chartOptions.xAxis[this.currentSet].categories[this.currentPos] = this.label
        this.chartLabels.labels[this.currentSet][this.currentPos] = this.label

        if (localStorage.getItem('cookies-preferences') === 'true') {
          let json = JSON.stringify(this.chartLabels)
          localStorage.setItem(experiment + '-labels', json)
        }

        // hide then show in order to update the chart
        this.chartOptions.xAxis[this.currentSet].visible = false
        this.chartOptions.xAxis[this.currentSet].visible = true
      }

      this.position = ''
    },

    resetLabels () {
      localStorage.removeItem(this.$route.params.id + '-labels')

      this.orginalChartLabels.labels.forEach((labels, index) => {
        this.chartOptions.xAxis[index].categories = labels
      })

      this.chartOptions.title.text = this.orginalChartLabels.title
      this.chartLabels.title = this.orginalChartLabels.title

      this.chartOptions.subtitle.text = this.orginalChartLabels.subtitle
      this.chartLabels.subtitle = this.orginalChartLabels.subtitle

      this.chartOptions.yAxis.title.text = this.orginalChartLabels.yTitle
      this.chartLabels.yTitle = this.orginalChartLabels.yTitle
    },

    addSeries (zScoreArray, index) {
      let meanValues = []
      let highLows = []

      for (let i = 0; i < zScoreArray.zScores[2].length; i++) {
        meanValues.push(zScoreArray.zScores[1][i])
        highLows.push([
          zScoreArray.zScores[0][i],
          zScoreArray.zScores[2][i]
        ])
      }

      // save a reference to the vue instance, allows us to access vue instance data inside the charts click events
      var vm = this

      this.chartOptions.series.push({
        type: 'scatter',
        id: index,
        xAxis: index,
        name: zScoreArray.imageSet.title,
        data: meanValues,
        marker: {
          radius: 5
        },
        tooltip: {
          headerFormat: '',
          pointFormat: 'Mean z-score: <b>{point.y}</b>'
        }
      })

      this.chartOptions.series.push({
        type: 'errorbar',
        data: highLows,
        stemWidth: 2,
        whiskerWidth: 2,
        tooltip: {
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
        setID: index,
        title: {
          text: zScoreArray.imageSet.title
        },
        labels: {
          enabled: true,
          style: { 'cursor': 'pointer' },
          events: {
            click: function (event) {
              vm.label = this.value
              vm.position = `left: ${event.x - 125}px; top: ${event.y - 110}px; display: block;`
              vm.currentSet = this.axis.userOptions.setID
              vm.currentPos = this.pos
              vm.type = 'xAxisCategories'
            }
          }
        },
        visible: true
      })
    },

    toggleSeries (event) {
      if (this.chartOptions.xAxis[event.target.userOptions.id].visible === true) {
        this.chartOptions.xAxis[event.target.userOptions.id].visible = false
      } else {
        this.chartOptions.xAxis[event.target.userOptions.id].visible = true
      }
    }
  }
}
</script>

<style lang="css">
  .qe-highcharts-container {
    /*height: 400px;*/
    margin: auto;
    /*min-width: 400px;*/
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

  .max-width-250 {
    max-width: 200px;
  }
</style>
