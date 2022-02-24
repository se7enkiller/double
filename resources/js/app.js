import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import App from '~/components/App'
// import VueNativeSock from 'vue-native-websocket-vue3'

import '~/plugins'
import '~/components'

Vue.config.productionTip = false
// App.use(VueNativeSock, '')

/* eslint-disable no-new */
new Vue({
  i18n,
  store,
  router,
  ...App
})
