<template>
  <div class="qe-statistics-container">
    <h2 class="mb-12">
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
import StatsCategory from '@/components/scientist/statistics/Category'
import StatsPaired from '@/components/scientist/statistics/Paired'
import StatsRankOrder from '@/components/scientist/statistics/RankOrder'
import StatsTriplet from '@/components/scientist/statistics/Triplet'

export default {
  components: {
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
</style>

<style lang="css">
  .v-tabs__item--active {
    font-weight: bold;
  }
</style>
