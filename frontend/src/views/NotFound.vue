<template>
  <v-container>
    <h1 class="title mt-5">404 - Page could not be found.</h1>

    <div class="mt-5">
      <v-btn outline @click="$router.back()" class="ma-0">
        <v-icon left>arrow_back</v-icon> Go back
      </v-btn>
    </div>

    <!-- <div class="mt-5">
      <router-link :to="'/'">
        Home
      </router-link>
    </div> -->
  </v-container>
</template>
