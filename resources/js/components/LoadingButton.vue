<template>
  <button :disabled="loading" :class="`btn btn-${type} mb-3 text-white`">
    <slot />
  </button>
</template>

<script>
export default {
  name: "LoadingButton",
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
    type: {
      type: String,
      default: 'primary'
    },
  },
}
</script>