<template>
  <v-card>
    <v-container>
      <v-layout>
        <v-btn outline fab small @click="$router.back()">
          <v-icon>arrow_back</v-icon>
        </v-btn>

        <h4 class="display-1">
          {{ experiment.name }}
        </h4>

        <div class="ml-4">
          <div class="body-1">
            {{ experiment.public === true ? 'Public' : 'Hidden' }}
          </div>

          <v-switch
            class="mt-0"
            v-model="experiment.public"
            color="success"
            @change="visibility"
          ></v-switch>
        </div>
      </v-layout>

      <v-layout column class="mt-4">
        <p class="subheading">
          Type: {{ experiment.type }}
        </p>

        <p>{{ experiment.description }}</p>

        <InviteLink :code="experiment.inviteCode"/>
      </v-layout>

      <v-layout mt-5 justify-space-between>
        <ExportMenu/>
        <DeleteMenu/>
      </v-layout>

      <!-- <v-layout>
        <h2>Results</h2>
      </v-layout> -->

    </v-container>
  </v-card>
</template>

<script>
import DeleteMenu from '@/components/scientist/DeleteMenu'
import ExportMenu from '@/components/scientist/ExportMenu'
import InviteLink from '@/components/scientist/InviteLink'

export default {
  components: {
    DeleteMenu,
    ExportMenu,
    InviteLink
  },

  data () {
    return {
      experiment: {
        name: 'Red chroma evaluation expriment thing',
        type: 'Rank order',
        description: `
          A test under controlled conditions that is made to demonstrate a known truth, to examine the
          validity of a hypothesis, or to determine the efficacy of something previously untried.
        `,
        public: true,
        inviteCode: '87164dd492'
      }
    }
  },

  methods: {
    visibility () {
      this.$axios.post('/upload').then((response) => {
        console.log('success')
      })
    }
  }
}
</script>
