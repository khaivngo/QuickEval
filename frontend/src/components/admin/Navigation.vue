<template>
  <div class="qe-nav-drawer">
    <v-list
      dense
      style="margin-top: 10px;"
      class="qe-drawer-1"
    >
      <v-list-item-group v-model="active" color="primary">
        <v-list-item
          v-for="(item, i) in items"
          :key="i"
          link
          @click="$router.push(item.url)"
        >
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
    </v-list>
  </div>
</template>

<script>
export default {
  data () {
    return {
      items: [
        { title: 'Dashboard', url: '/admin/dashboard', icon: 'mdi-view-dashboard-outline' },
        { title: 'Scientist Requests', url: '/admin/scientist-role-requests', icon: 'mdi-account-multiple-plus-outline' },
        { title: 'Users', url: '/admin/all-users', icon: 'mdi-account-multiple-outline' }
        // account-supervisor-outline
      ],
      active: null,
      mini: true
    }
  },

  created () {
    if (this.$route.path.split('/')[2] === 'scientist-role-requests') {
      this.active = 1
    } else if (this.$route.path.split('/')[2] === 'dashboard') {
      this.active = 0
    } else if (this.$route.path.split('/')[2] === 'all-users') {
      this.active = 2
    } else {
      this.active = 1
    }
  }
}
</script>

<style scoped lang="css">
  .qe-nav-drawer {
    z-index: 1;
    padding-top: 64px;
    position: fixed;
    width: 240px;
    overflow-y: auto;
    top: 0;
    bottom: 0;
    left: 0;
    border-right: 1px solid #ddd;
    background: #fff;
  }
  @media (max-width: 1150px) {
    .qe-nav-drawer {
      width: 58px;
    }
  }
</style>
