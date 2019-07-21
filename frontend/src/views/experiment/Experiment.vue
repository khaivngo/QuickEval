<template>
  <v-card>
    <v-container class="ma-0 pa-2" style="border-bottom: 1px solid #ccc;">
      <v-layout>
        <v-btn outline fab small @click="$router.back()">
          <v-icon>arrow_back</v-icon>
        </v-btn>
      </v-layout>
    </v-container>
    <v-container>
      <v-layout>
        <h4 class="display-1">
          {{ experiment.title }}
        </h4>

        <!-- <div class="ml-4">
          <div class="body-1">
            {{ experiment.is_public === true ? 'Public' : 'Hidden' }}
          </div>

          <v-switch
            class="mt-0"
            v-model="experiment.is_public"
            color="success"
            @change="visibility"
          ></v-switch>
        </div> -->
      </v-layout>

      <v-layout>
        <div>
          System details
        </div>
      </v-layout>

      <v-layout column class="mt-4">
        <p class="subheading">
          Type: {{ experiment.experiemnt_type }}
        </p>

        <p>{{ experiment.short_description }}</p>

        <InviteLink :code="experiment.invite_hash"/>
      </v-layout>

      <v-layout mt-5 justify-space-between>
        <ExportMenu/>

        <v-btn depressed color="error text-none">
          Wipe results <v-icon :size="20" class="ml-2">delete</v-icon>
        </v-btn>
      </v-layout>

      <!-- <v-layout>
        <h2>Results</h2>
      </v-layout> -->

    </v-container>
  </v-card>
</template>

<script>
import ExportMenu from '@/components/scientist/ExportMenu'
import InviteLink from '@/components/scientist/InviteLink'

export default {
  components: {
    ExportMenu,
    InviteLink
  },

  data () {
    return {
      experiment: {
        title: null,
        type: null,
        short_description: null,
        is_public: null,
        inviteCode: null
      }
    }
  },

  created () {
    this.$axios.get('/experiment/' + this.$route.params.id)
      .then(response => { this.experiment = response.data })
      .catch(err => console.log(err))
  }
}
</script>
