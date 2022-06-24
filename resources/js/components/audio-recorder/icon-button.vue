<template>
  <div v-html="icons[name]"></div>
</template>

<script>
  export default {
    props: {
      name: { type: String }
    },
    data: function () {
      return {
        icons: { 
          mic: '<img src="/img/microphone_icon.png" width="51">',
          pause: '<img src="/img/pause_icon.png" width="51">',
          play: '<img src="/img/play_icon.png" width="51">', 
          stop: '<img src="/img/stop_icon.png" width="51">' 
        }
      }
    }
  }
</script>

