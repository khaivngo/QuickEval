<template>
  <div class="qe-statistics-container">
    <h2 class="mb-6">
      Statistics
    </h2>

    <StatsCategory
      v-if="experimentType === 'category'"
    />
    <StatsPaired
      v-if="experimentType === 'paired'"
    />
    <StatsRankOrder
      v-if="experimentType === 'rank-order'"
    />
    <StatsTriplet
      v-if="experimentType === 'triplet'"
    />
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
  arrayObjectIndexOf,
  convertRankToPair
} from '@/maths.js'
import ScatterPlot from '@/components/scientist/HighchartsScatterPlot'

import StatsCategory from '@/components/scientist/statistics/Category'
import StatsPaired from '@/components/scientist/statistics/Paired'
import StatsRankOrder from '@/components/scientist/statistics/RankOrder'
import StatsTriplet from '@/components/scientist/statistics/Triplet'

const config = {}
const math = create(all, config)

export default {
  components: {
    ScatterPlot,
    StatsCategory,
    StatsPaired,
    StatsRankOrder,
    StatsTriplet
  },

  props: {
    experimentType: String
  }
}
</script>

<style lang="css" scoped>
  .qe-statistics-container {
    padding-bottom: 300px;
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
