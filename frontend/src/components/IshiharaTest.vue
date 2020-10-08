<template>
  <div style="position: fixed; z-index: 20; top: 0; bottom: 0; left: 0; right: 0; background: #fff; overflow-x: hidden;">
    <v-toolbar flat>
      <v-spacer></v-spacer>

      <v-toolbar-items>
        <v-dialog v-model="abortDialog" max-width="500">
          <template v-slot:activator="{ on }">
            <v-btn text v-on="on">
              Quit
            </v-btn>
          </template>
          <v-card>
            <v-card-title class="headline">Do you want to quit the experiment?</v-card-title>
            <v-card-text></v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="default darken-1" text @click="abortDialog = false">Continue</v-btn>
              <v-btn color="red darken-1" text @click="abort">Quit</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar-items>
    </v-toolbar>

    <div>
      <v-row class="mt-6 justify-center">
        <v-col cols="auto">
          <v-img :src="require(`@/assets/Ishihara/${ishihara[index].img}.png`)" style="width: 500px;"></v-img>
        </v-col>
      </v-row>

      <v-row class="mt-6 justify-center">
        <v-col cols="12" class="text-center">
          <h3>Type any number you see in the image above</h3>
          <p>Leave the field empty if you cannot see any number</p>
        </v-col>
        <v-col cols="auto">
          <v-text-field
            :label="'Answer'"
            v-model.number="answer"
            outlined
            dense
            autocomplete="off"
            style="width: 150px;"
          ></v-text-field>
        </v-col>

        <v-col cols="auto" class="pl-0 ml-0">
          <v-btn @click="next" :disabled="diableBtn" color="primary" style="margin-top: 2px;">
            next
            <v-icon small class="ml-1">
              mdi-arrow-right
            </v-icon>
          </v-btn>
        </v-col>
      </v-row>

      <!-- Color vision: {{ vision }}<br>
      Post-eval for vision: {{ postEvaluation }}<br>
      Degree: {{ degree }} ({{ perc }}%)<br> -->
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      ishihara: [
        { id: 1,  img: '1',  normal: 12,   defic: 12,   cBlind: 12 },
        { id: 2,  img: '2',  normal: 8,    defic: 3,    cBlind: null },
        { id: 3,  img: '3',  normal: 29,   defic: 70,   cBlind: null },
        { id: 4,  img: '4',  normal: 5,    defic: 2,    cBlind: null },
        { id: 5,  img: '5',  normal: 3,    defic: 5,    cBlind: null },
        { id: 6,  img: '6',  normal: 15,   defic: 17,   cBlind: null },
        { id: 7,  img: '7',  normal: 74,   defic: 21,   cBlind: null },
        { id: 8,  img: '8',  normal: 6,    defic: null, cBlind: null },
        { id: 9,  img: '9',  normal: 45,   defic: null, cBlind: null },
        { id: 10, img: '10', normal: 5,    defic: null, cBlind: null },
        { id: 11, img: '11', normal: 7,    defic: null, cBlind: null },
        { id: 12, img: '12', normal: 16,   defic: null, cBlind: null },
        { id: 13, img: '13', normal: 73,   defic: null, cBlind: null },
        { id: 14, img: '14', normal: null, defic: 5,    cBlind: null },
        { id: 15, img: '15', normal: null, defic: 45,   cBlind: null },
        { id: 16, img: '16', normal: 26,   defic: { protan: 6, deutan: 2 }, cBlind: null },
        { id: 17, img: '17', normal: 42,   defic: { protan: 2, deutan: 4 }, cBlind: null }
      ],
      // normal: [12, 8, 29, 5, 3, 15, 74, 6, 45, 5, 7, 16, 73, null, null, 26, 42],
      // protan: [12, 3, 29, 5, 3, 15, 74, 6, 45, 5, 7, null, null, 5, 45, 6, 2],
      // deutan: [12, 3, 70, 2, 5, 17, 21, null, null, null, null, null, null, 5, 45, 2, 4],

      index: 0,
      answer: null,
      tasks: [],

      vision: '',
      postEvaluation: 'Not applicable',
      degree: '',
      perc: 0,

      loading: false,
      abortDialog: false,
      diableBtn: false
    }
  },
  methods: {
    async next () {
      if (this.index === this.ishihara.length - 1) {
        this.diableBtn = true
        this.tasks.push({ 'answer': this.answer, 'extra': null })
        this.validateVision()
        this.answer = null

        // save result
        await this.save()

        this.$emit('finished')
        return
      }

      this.tasks.push({ 'answer': this.answer, 'extra': null })
      this.answer = null
      this.index++
    },

    /* eslint-disable */
    validateVision () {
      let normal = 0
      let protan = 0
      let deutan = 0
      let fail = 0

      // Set score:
      for (let i = 0; i < this.tasks.length; i++) {
        // First 15:
        if (i < this.tasks.length-2) {
          if (i != 0) {
            switch (this.tasks[i].answer) {
              case this.ishihara[i].normal:
                normal++
                break
              case this.ishihara[i].defic:
                protan++
                deutan++
                break
              default:
                fail++
                break
            }
          }
        }
        // Two last expections:
        else {
          switch (this.tasks[i].answer) {
            case this.ishihara[i].normal:        normal++; break;
            case this.ishihara[i].defic.protan:  protan++; break;
            case this.ishihara[i].defic.deutan:  deutan++; break;
            default:                             fail++;   break;
          }
        } 
      }

      // Calc percentage:
      let normalP = parseFloat(((normal / (this.tasks.length-1)) * 100).toFixed(2))
      let protanP = parseFloat(((protan / (this.tasks.length-1)) * 100).toFixed(2))
      let deutanP = parseFloat(((deutan / (this.tasks.length-1)) * 100).toFixed(2))
      let failP   = parseFloat(((fail / (this.tasks.length-1)) * 100).toFixed(2))

      if (normal >= protan && normal >= deutan && normal >= fail) {
        // Good vision:
        if (normal >= 10) {
          this.vision = "Normal"
          this.degree = this.calcDegree(normalP)
          this.perc = normalP
        }  
        // 9 and less is regarded as deficient.
        else {
          this.vision = "Weak normal"
          this.postEvaluation = this.postEval()
          this.degree = this.calcDegree(normalP)
          this.perc = normalP
        }
      }
      // Protan:
      else if (protan > deutan && protan > fail) {
        this.vision = "Protanopia"
        this.degree = this.calcDegree(protanP)
        this.perc = protanP
      }
      // Deutan:
      else if (deutan > protan && deutan > fail) {
        this.vision = "Deuteranopia"
        this.degree = this.calcDegree(deutanP)
        this.perc = deutanP
      }
      // Protan equals Deutan
      else if (protan >= fail || deutan >= fail && protan == deutan) {
        this.vision = "Protanopia or Deuteranopia"
        this.postEvaluation = this.postEval()
        this.degree = this.calcDegree(protanP)
        this.perc = protanP
      }
      // Fail:
      else if (fail > deutan && fail > protan && fail > normal) {
        this.vision = "Color blind"
        this.postEvaluation = this.postEval()
        this.degree = this.calcDegree(failP)
        this.perc = failP
      }

      // console.log('Normal\n  score: ' + normal + '\n  perc:  ' + normalP + '%')
      // console.log('Protan\n  score: ' + protan + '\n  perc:  ' + protanP + '%')
      // console.log('Deutan\n  score: ' + deutan + '\n  perc:  ' + deutanP + '%')
      // console.log('Fail\n    score: ' + fail   + '\n  perc:  ' + failP + '%')
    },

    calcDegree (perc) {
      if      (perc <= 25)                return "None"
      else if (perc >= 25 && perc <= 50)  return "Weak"
      else if (perc >= 50 && perc <= 75)  return "Moderate"
      else if (perc >= 75 && perc <= 100) return "Strong"
    },

    postEval () {
      if (this.tasks[15].answer == this.ishihara[15].defic.protan || this.tasks[16].answer == this.ishihara[16].defic.protan) {
        return "Protanopia"
      } 
      else if (this.tasks[15].answer == this.ishihara[15].defic.deutan || this.tasks[16].answer == this.ishihara[16].defic.deutan) {
        return "Deuteranopia"
      }
      else if (this.tasks[15].answer == this.ishihara[15].normal) {
        if (this.tasks[15].extra == this.ishihara[15].defic.protan) {
          return "Protanomalia"
        }
        else if (this.tasks[15].extra == this.ishihara[15].defic.deutan) {
          return "Deuteranomalia"
        }
      }
      else {
        return "Not Applicable"
      }
    },

    save () {
      const expID = localStorage.getItem('experimentResult')

      return this.$axios.patch(`/experiment-result/${expID}/update`, {
        vision: this.vision,
        post_eval: this.postEvaluation,
        degree: this.degree
      })
    },

    abort () {
      localStorage.removeItem('index')
      localStorage.removeItem('experimentResult')
      this.abortDialog = true
      this.$emit('aborted')
    }
  }
}
</script>
