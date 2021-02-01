import Vue from 'vue'

export const storage = Vue.observable({
  experimentType: null
})

export const mutations = {
  setExperimentType (val) {
    storage.experimentType = val
  }
}
