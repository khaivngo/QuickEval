<template>
  <ExperimentCreationForm/>
</template>

<script>
import ExperimentCreationForm from '@/components/scientist/ExperimentCreationForm'

export default {
  components: {
    ExperimentCreationForm
  },

  data () {
    return {
      experimentId: this.$route.params.id
    }
  }
}
</script>
