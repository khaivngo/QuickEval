<template>
  <div v-if="show" class="bottom-dialog d-flex justify-end flex-wrap">
    <div style="background: #1BA1E2;" class="pt-8 pb-8 pl-8 pr-8 mb-8 elevation-3">
      <!-- #1BA1E2 -->
      <!-- <p class="mr-6 pb-3 pt-6 mb-0">
        We use cookies for necessary site features only. These features are:
      </p> -->

      <h3 class="text-h4">
        This website uses cookies
        <!-- Cookie consent -->
      </h3>

      <div class="d-flex mt-6">
        <div style="padding-top: 2px;">
          <v-checkbox
            dark
            v-model="necessary"
            class="ma-0 pa-0 pl-1"
            color="success"
            hide-details
            disabled
          ></v-checkbox>
        </div>
        <div>
          <h6 class="subtitle-1 font-weight-bold ma-0 pa-0">Necessary</h6>
          <p class="body-2">
            Secure sign in, and page navigation
          </p>
          <!-- <ul>
            <li>
              Secure sign in, and page navigation
            </li>
            <li>
              Keeping track of progress during experiments
            </li>
          </ul> -->
        </div>
      </div>

      <div class="d-flex mt-1">
        <div style="padding-top: 2px;">
          <v-checkbox
            dark
            v-model="preferences"
            class="ma-0 pa-0 pl-1"
            color="default"
            hide-details
          ></v-checkbox>
        </div>
        <div>
          <h6 class="subtitle-1 font-weight-bold ma-0 pa-0">Preferences</h6>
          <p class="body-2">
            <!-- Remember your site preferences over time -->
            Remember information that changes the way the website behaves or looks
          </p>
          <!-- <ul>
            <li>
              Remember your site preferences over time
            </li>
          </ul> -->
        </div>
      </div>

      <div class="mt-6 d-flex justify-center">
        <v-btn color="primary" @click="accepted" class="mr-4">
          Accept selection
        </v-btn>
        <v-btn color="success" @click="accepted('all')">
          Accept all cookies
        </v-btn>
      </div>

      <!-- <ul>
        <li>Remember your sign in</li>
        <li>Keeping track of progress during experiments</li>
        <li>Remember your site preferences over time</li>
      </ul> -->
      <!-- <p class="pt-3"> -->
        <!-- By using QuickEval you agree to our <a href="" target="_blank">cookie policy</a> -->
        <!-- https://support.invisionapp.com/hc/en-us/articles/360003821111 -->
      <!-- </p> -->
    </div>
  </div>
</template>

<script>
import EventBus from '@/eventBus'

export default {
  data () {
    return {
      show: true,

      necessary: true,
      preferences: false
    }
  },
  created () {
    if (localStorage.getItem('cookies-consented') === 'true') {
      this.show = false
    }
  },
  mounted () {
    EventBus.$on('cookie-consent-change', () => {
      localStorage.setItem('cookies-consented', false)
      localStorage.setItem('cookies-preferences', false)
      this.show = true
    })
  },
  methods: {
    accepted (all) {
      localStorage.setItem('cookies-consented', true)

      let preferences = this.preferences
      if (all && all === 'all') {
        preferences = true
      }

      localStorage.setItem('cookies-preferences', preferences)
      this.show = false
    }
  }
}
</script>

<style scoped lang="css">
  .bottom-dialog {
    /*position: fixed;
    bottom: 0;
    right: 0;
    z-index: 0;
    background: transparent;*/
    z-index: 1;
    position: fixed;
    bottom: 0;
    left: 50%;
    width: auto;
    color: #fff;
    -webkit-transform: translateX(-50%);
    -moz-transform: translateX(-50%);
    -ms-transform: translateX(-50%);
    -o-transform: translateX(-50%);
    transform: translateX(-50%);
  }
</style>
