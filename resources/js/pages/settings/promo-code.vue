<template>
  <card>
    <div class="alert alert-info d-flex">
      <fa class="align-self-center" icon="info-circle" size="2x" />
      <span class="mx-2">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non, sunt, vitae. Cumque.</span>
    </div>
    <div class="d-flex align-items-center flex-column mb-4">
      <div v-if="user" class="d-flex align-items-center flex-column mb-3">
        <div class="promo-link__title mb-3">
          {{ $t('press_promo-code') }}:
        </div>
        <div class="d-flex">
          <input type="text">
          <button class="btn btn-sm btn-success">
            <fa icon="check" fixed-width />
          </button>
        </div>
      </div>
      <div class="promo-link__title mb-3">
        {{ $t('your_promo-code') }}:
      </div>
      <div v-tooltip="{ content: $t('click_to_copy')}"
           class="promo-code d-flex justify-content-around align-items-center"
           @click="copy()"
      >
        <span ref="promo" class="promo-link__promo">
          F4FMGJDVE
        </span>
      </div>
    </div>
    <div class="d-flex align-items-center flex-column mb-3">
      {{ $t('entered_promo-code') }}:
    </div>
    <div class="row">
      <div class="col-9">
        {{ user.name }}
      </div>
      <div class="col-3">
        <div class="success">
          {{ $t('paid_out') }}
        </div>
      </div>
      <div class="col-9">
        {{ user.name }}
      </div>
      <div class="col-3">
        <div class="warning">
          {{ $t('pending') }}
        </div>
      </div>
    </div>
  </card>
</template>

<script>
import Vue from 'vue'
import Form from 'vform'
import VTooltip from 'v-tooltip'
import { mapGetters } from 'vuex'

Vue.use(VTooltip)

export default {
  name: 'PromoCode',

  data: () => ({
    form: new Form({
      balance: 0
    })
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  methods: {
    copy () {
      const tmpTextField = document.createElement('textarea')
      tmpTextField.textContent = this.$refs.promo.innerHTML
      tmpTextField.setAttribute('style', 'position:absolute; right:200%;')
      document.body.appendChild(tmpTextField)
      tmpTextField.select()
      tmpTextField.setSelectionRange(0, 99999) /* For mobile devices */
      document.execCommand('copy')
      tmpTextField.remove()
    }
  }
}

</script>

<style scoped>
  .promo-code {
    background-color: #262e58;
    height: 50px;
    width: 300px;
    border-radius: 5px;
    color: #fff;
    cursor: pointer;
  }

  .success {
    color: #01c236;
  }

  .warning {
    color: #ffc720;
  }
</style>
