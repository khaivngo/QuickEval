<template>
  <div v-if="chartOptions.series.length !== 0" class="qe-highcharts-container">
    <h2 class="mb-8 mt-12">
      <!-- Scatter and errorbar plot -->
    </h2>
    <highcharts
      :options="chartOptions"
    ></highcharts>

    <!-- <div v-for="(serie, i) in series" :key="i">
      <div v-for="(label, j) in serie.label" :key="j">
        {{ label }}
        <v-text-field
          outlined dense
          label="Title"
          v-model="label.label"
        ></v-text-field>
      </div>
    </div> -->

    <!-- <div v-for="(serie, i) in labels" :key="i">
      <div v-for="(label, j) in serie" :key="j">
        <v-text-field
          outlined dense
          label="Title"
          v-model="label.label"
          class="max-width-250"
        ></v-text-field>
      </div>

      <v-btn @click="updateLabels(i)" color="success">
        Save
      </v-btn>
    </div>

    <h4 class="mb-3">Chart titles</h4>
    <v-text-field
      outlined dense
      label="Chart Title"
      v-model="title"
      class="max-width-250"
    ></v-text-field>

    <v-text-field
      outlined dense
      label="Y-label"
      v-model="yTitle"
      class="max-width-250"
    ></v-text-field>

    <v-text-field
      outlined dense
      label="X-label"
      v-model="xTitle"
      class="max-width-250"
    ></v-text-field>
    <v-btn @click="updateTitles" color="success">
      Save
    </v-btn> -->

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

    <p class="caption">
      <v-icon>mdi-lightbulb-on-outline</v-icon>
      Tip: Edit the text of the chart labels by clicking on the labels.
    </p>
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
      newVal.forEach((serie, index) => {
        this.addSeries(serie, index)

        let labels = serie.label.map(label => {
          return { label: label }
        })
        this.labels.push(labels)
      })
    }
  },

  data () {
    return {
      chartOptions: {
        title: {
          text: 'Plot Title',
          style: {
            'cursor': 'pointer'
          }
        },
        subtitle: {
          text: 'subtitle',
          style: {
            'cursor': 'pointer'
          }
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
            text: 'Z-score',
            // useHTML: true,
            style: {
              'cursor': 'pointer'
            }
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
        plotOptions: {
          series: {
            cursor: 'pointer',
            events: {
              legendItemClick: (event) => {
                this.hideSeries(event)
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
        }
      },

      labels: [],
      yTitle: '',
      xTitle: '',
      title: '',

      position: '',
      label: '',
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

        // Chart.setTitle('cake')
      }
    }
  },

  methods: {
    update (type) {
      if (this.type === 'yAxisTitle') {
        this.chartOptions.yAxis.title.text = this.label
      }

      if (this.type === 'title') {
        this.chartOptions.title.text = this.label
      }

      if (this.type === 'subtitle') {
        this.chartOptions.subtitle.text = this.label
      }

      if (this.type === 'xAxisCategories') {
        this.chartOptions.xAxis[this.currentSet].categories[this.currentPos] = this.label

        // hide then show in order to update the chart
        this.chartOptions.xAxis[this.currentSet].visible = false
        this.chartOptions.xAxis[this.currentSet].visible = true
      }

      this.position = ''

      // persistent saving
    },

    updateLabels (serie) {
      let labels = this.labels[serie].map(label => {
        return label.label
      })
      this.chartOptions.xAxis[serie].categories = labels

      // this.chartOptions.xAxis[serie].categories[this.pos] = this.Label
      // save in DB
    },

    updateTitles () {
      this.chartOptions.title.text = this.title
      this.chartOptions.yAxis.title.text = this.yTitle
      this.chartOptions.xTitle.text = this.xTitle
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

      // save a reference to the vue instance
      var vm = this

      this.chartOptions.series.push({
        labelId: index,
        xAxis: index,
        name: zScoreArray.imageSet.title,
        type: 'scatter',
        data: meanValues,
        marker: {
          // fillColor: 'white', // strokeColor: 'red', // lineWidth: 6,
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
        setID: index,
        title: {
          text: zScoreArray.imageSet.title
        },
        labels: {
          enabled: true,
          style: {
            'cursor': 'pointer'
          },
          // limit length of labels to 15 characters
          // formatter: function () {
          //   return this.value.toString().substring(0, 15)
          // },
          events: {
            click: function (event) {
              vm.label = this.value
              vm.position = `left: ${event.x - 125}px; top: ${event.y - 110}px; display: block;`
              vm.currentSet = this.axis.userOptions.setID
              vm.currentPos = this.pos
              vm.type = 'xAxisCategories'
            }
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
    },

    hideSeries (event) {
      if (this.chartOptions.xAxis[event.target.userOptions.labelId].visible === true) {
        this.chartOptions.xAxis[event.target.userOptions.labelId].visible = false
      } else {
        this.chartOptions.xAxis[event.target.userOptions.labelId].visible = true
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
