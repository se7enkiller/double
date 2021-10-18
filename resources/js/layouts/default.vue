<template>
  <div class="main-layout">
    <navbar />

    <div class="container-fluid mt-4">
      <child />
    </div>
  </div>
</template>

<script>
import Navbar from '~/components/Navbar'

export default {
  name: 'MainLayout',

  components: {
    Navbar
  }
}
</script>
<style>
/*@import './public/style/all.min.css';*/
  .main-layout {
    /*background-color: #2c2c54;*/
  }
</style>
