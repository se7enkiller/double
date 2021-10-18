<template xmlns="http://www.w3.org/1999/html">
  <div>
    <nav class="navbar navbar-expand-lg navbar-light">
      <div class="container-fluid">
        <router-link :to="{ name: user ? 'home' : 'welcome' }" class="navbar-brand">
          {{ appName }}
        </router-link>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">
          <span class="navbar-toggler-icon" />
        </button>

        <div id="navbar" class="collapse navbar-collapse">
          <ul class="navbar-nav">
            <locale-dropdown />
          </ul>

          <ul class="navbar-nav m-auto">
            <li class="nav-item">
              <router-link :to="{ name: 'panel.double' }" class="nav-link" active-class="active">
                {{ $t('double') }}
              </router-link>
            </li>
          </ul>

          <ul class="navbar-nav ms-auto">
            <!-- Authenticated -->
            <li v-if="user" class="nav-item dropdown d-flex">
              <a class="nav-link dropdown-toggle text-dark mr-4"
                 href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
              >
                <img :src="user.photo_url" class="rounded-circle profile-photo me-1">
                {{ user.name }}
              </a>
              <div class="dropdown-menu">
                <router-link :to="{ name: 'settings.promo-code' }" class="dropdown-item ps-3">
                  <fa icon="percent" fixed-width />
                  {{ $t('promo-code') }}
                </router-link>
                <router-link :to="{ name: 'settings.profile' }" class="dropdown-item ps-3">
                  <fa icon="cog" fixed-width />
                  {{ $t('settings') }}
                </router-link>

                <div class="dropdown-divider" />
                <a href="#" class="dropdown-item ps-3" @click.prevent="logout">
                  <fa icon="sign-out-alt" fixed-width />
                  {{ $t('logout') }}
                </a>
              </div>
              <div class="d-flex align-items-center mr-4">
                {{ $t('balance') }}: {{ user.balance }}
              </div>
              <div class="d-flex align-items-center">
                <button class="btn btn-info btn-sm" @click="showDeposit()">
                  {{ $t('deposit') }}
                </button>
              </div>
            </li>
            <!-- Guest -->
            <template v-else>
              <li class="nav-item">
                <router-link :to="{ name: 'login' }" class="nav-link" active-class="active">
                  {{ $t('login') }}
                </router-link>
              </li>
              <li class="nav-item">
                <router-link :to="{ name: 'register' }" class="nav-link" active-class="active">
                  {{ $t('register') }}
                </router-link>
              </li>
            </template>
          </ul>
        </div>
      </div>
    </nav>
    <modal name="deposit"
           :width="656"
           :height="400"
           :adaptive="true"
    >
      <div class="row">
        <div class="col-6">
          <div class="payment-container">
            <div class="payment-title">
              {{ $t('payments_methods') }}
            </div>
            <div class="payment-form">
              <button class="btn large-btn card-btn" :class="{active: activeButton === 'card'}" @click="toggleActiveButton('card')">
                VISA/MASTERCARD
              </button>
              <button class="btn large-btn qiwi-btn" :class="{active: activeButton === 'qiwi'}" @click="toggleActiveButton('qiwi')">
                QIWI
              </button>
              <button class="btn large-btn skins-btn">
                {{ $t('skins') }}
              </button>
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="payment-container">
            <div class="payment-title">
              {{ $t('enter_amount') }}
            </div>
            <div class="balance-form">
              <div class="balance-exchange">
                <input ref="depositMoney" type="text">
              </div>
              <button class="btn large-btn card-btn" @click="updateBalance()">
                {{ $t('deposit_money') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </modal>
  </div>
</template>

<script>
import Vue from 'vue'
import { mapGetters } from 'vuex'
import LocaleDropdown from './LocaleDropdown'
import VModal from 'vue-js-modal'
import Form from 'vform'

Vue.use(VModal)

export default {
  components: {
    LocaleDropdown
  },

  data: () => ({
    form: new Form({
      balance: 0
    }),
    appName: window.config.appName,
    activeButton: ''
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    },

    showDeposit () {
      this.$modal.show('deposit')
    },

    hideDeposit () {
      this.$modal.hide('deposit')
    },

    toggleActiveButton (button) {
      this.activeButton = button
    },

    updateBalance () {
      this.form.balance = +this.$refs.depositMoney.value + +this.user.balance
      this.form.post('/api/panel/double/updateBalance').then(() => {
        this.user.balance = +this.form.balance
      }).catch(() => {
      })
    }
  }
}
</script>

<style scoped>
.profile-photo {
  width: 2rem;
  height: 2rem;
  margin: -.375rem 0;
}

.btn:focus {
  box-shadow: unset;
}

.navbar {
  box-shadow: none !important;
}

.mr-4 {
  margin-right: 1rem;
}

.payment-container {
  padding: 30px 20px;
}

.payment-title {
  box-sizing: border-box;
  padding: 0 10px 30px 10px;
  width: 100%;
  text-align: center;
  letter-spacing: 1px;
  font-size: 20px;
  font-weight: 300;
}

.large-btn {
  width: 100%;
  background: #fff;
  border-radius: 4px;
  box-sizing: border-box;
  padding: 10px;
  letter-spacing: 1px;
  font-family: Open Sans,sans-serif;
  font-weight: 400;
  min-width: 140px;
  margin-top: 8px;
  cursor: pointer;
  border: 1px solid #dddedf;
  text-transform: uppercase;
  transition: all .1s;
  font-size: 10px;
  outline: none;
}

.skins-btn {
  border-color: #6649d0;
  color: #6649d0;
}

.skins-btn:hover {
  color: #fff;
  background-color: #6649d0;
}

.qiwi-btn {
  border-color: #ffc200;
  color: #ffc200;
}

.qiwi-btn:hover, .qiwi-btn.active {
  color: #fff;
  background-color: #ffc200;
}

.card-btn {
  border-color: #3880ff;
  color: #3880ff;
}

.card-btn:hover, .card-btn.active {
  background-color: #3880ff;
  color: #fff;
}

.balance-exchange {
  background-color: #171d39;
  display: grid;
  -webkit-box-align: center;
  align-items: center;
  border-radius: 1rem;
}
</style>
