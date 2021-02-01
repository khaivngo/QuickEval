<template>
  <transition
    :name="transitionName"
    mode="out-in"
    @beforeLeave="beforeLeave"
    @enter="enter"
    @afterEnter="afterEnter"
  >
    <slot/>
  </transition>
</template>

<script>
const DEFAULT_TRANSITION = 'fade'
const DEFAULT_TRANSITION_MODE = 'out-in'

export default {
  data () {
    return {
      prevHeight: 0,
      transitionName: DEFAULT_TRANSITION,
      transitionMode: DEFAULT_TRANSITION_MODE
    }
  },

  created () {
    this.$router.beforeEach((to, from, next) => {
      let transitionName = to.meta.transitionName || from.meta.transitionName

      // if (transitionName === 'slide') {
      //   const toDepth = to.path.split('/').length
      //   const fromDepth = from.path.split('/').length
      //   transitionName = toDepth < fromDepth ? 'slide-left' : 'slide-right'
      // }

      this.transitionName = transitionName || DEFAULT_TRANSITION

      next()
    })
  },

  methods: {
    beforeLeave (element) {
      this.prevHeight = getComputedStyle(element).height
    },

    enter (element) {
      const { height } = getComputedStyle(element)

      element.style.height = this.prevHeight

      setTimeout(() => {
        element.style.height = height
      })
    },

    afterEnter (element) {
      element.style.height = 'auto'
    }
  }
}
</script>

<style lang="scss">
  .fade-enter-active,
  .fade-leave-active {
    transition-duration: 0.3s;
    // transition-property: opacity;
    transition-timing-function: ease;
    transition-property: height, opacity;
    overflow: hidden;
  }

  .fade-enter,
  .fade-leave-active {
    opacity: 0
  }

  .slide-left-enter-active,
  .slide-left-leave-active,
  .slide-right-enter-active,
  .slide-right-leave-active {
    transition-duration: 0.3s;
    transition-property: height, opacity, transform;
    transition-timing-function: cubic-bezier(0.55, 0, 0.1, 1);
    overflow: hidden;
  }

  .slide-left-enter,
  .slide-right-leave-active {
    opacity: 0;
    transform: translate(2em, 0);
  }

  .slide-left-leave-active,
  .slide-right-enter {
    opacity: 0;
    transform: translate(-2em, 0);
  }
</style>
