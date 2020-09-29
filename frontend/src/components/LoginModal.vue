<template>
  <v-dialog v-model="open" persistent max-width="600">
    <v-card>
      <v-card-text class="pa-12">
        <div>
          <LoginAnonymous/>
        </div>

        <div class="pt-6 pb-6 pl-2">
          <h3 class="text-h6">OR</h3>
        </div>

        <div>
          <Login/>
          <Register/>
        </div>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import LoginAnonymous from '@/components/LoginAnonymous'
import Register from '@/components/Register'
import Login from '@/components/Login'

export default {
  components: {
    LoginAnonymous,
    Register,
    Login
  },

  props: {
    open: Boolean
  }
}
</script>
