<template>
  <v-container class="ma-0 pa-2" style="border-bottom: 1px solid #eee;">
    <v-layout align-center>
      <v-flex>
        <v-btn text @click="$router.back()" class="ma-0 text-none">
          <v-icon left>mdi-arrow-left</v-icon>
          <span>
            <slot></slot>
          </span>
        </v-btn>
      </v-flex>
    </v-layout>
  </v-container>
</template>
