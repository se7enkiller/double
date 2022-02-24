<template>
  <div class="game-component">
    <div class="nav d-flex justify-content-between">
      <div class="balanceDiv">
        {{ form.balance }}$
      </div>
    </div>
    <div class="row mb-4">
      <div class="col-3">
        <div class="bet-container bg-color-container">
          <div class="bet-title d-flex justify-content-between flex-nowrap">
            BASIC BETS
          </div>
          <input ref="bet"
                 :disabled="!user"
                 class="bet"
                 type="text"
                 maxlength="6"
                 value="0"
                 @keypress="isNumber($event)"
          >
          <div class="bets-holder">
            <div ref="redBox" class="boxes">
              0
            </div>
            <div ref="greenBox" class="boxes">
              0
            </div>
            <div ref="blackBox" class="boxes">
              0
            </div>
          </div>
          <div class="button-holder mb-3">
            <button id="red" class="btn-x" :disabled="form.canBet === false" @click="updateBet('red')">
              X2
            </button>
            <button id="green" class="btn-x" :disabled="form.canBet === false" @click="updateBet('green')">
              X14
            </button>
            <button id="black" class="btn-x" :disabled="form.canBet === false" @click="updateBet('black')">
              X2
            </button>
          </div>
        </div>
      </div>
      <div class="holder col-9">
        <div class="roulette bg-color-container">
          <div id="timer" ref="timerDiv" class="timer" />
          <div id="out" />
          <div class="ticker" />
          <div class="static">
            <div id="scroll" class="scroll">
              <div v-for="(n, key) in 8"
                   :key="key"
                   class="box-container"
              >
                <div v-for="item in groceryList"
                     :key="item.id"
                     :todo="item"
                     class="box"
                     :class="item.color"
                >
                  {{ item.id }}
                </div>
              </div>
            </div>
          </div>
          <div class="game-footer d-flex align-items-center">
            <div class="game-footer__container d-flex flex-nowrap">
              <div ref="historyChild" class="history-child d-flex" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="game-container_history">
      <div class="row game_history bg-color-container">
        <div class="col-4">
          <div class="double-history__col-header red">
            <span class="double-history__col-bets">
              <fa icon="coins" fixed-width />
              <span class="double-history__col-bets__value">{{ redBets }}</span>
            </span>
          </div>
          <div v-for="(player, key) in getRedPlayers()" :key="key" class="double-history__col-body">
            <div class="double-history__col-header-bet__user">
              <div class="double-history__col-header-bet__photo">
                <img class="w-100 h-100 avatar" :src="player.photo_url">
              </div>
              <div class="double-history__col-header-bet__row">
                <div class="double-history__col-header-bet__username">
                  {{ player.name }}
                </div>
                <div class="double-history__col-header-bet__amount">
                  {{ player.bet }}
                </div>
              </div>
            </div>
          </div>
          <div v-if="getRedPlayers().length === 0" class="double-history__col-body">
            <div class="double-history__col-header-bet__user double-history__col-header-bet__no-rates">
              <span>{{ $t('no_rates') }}</span>
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="double-history__col-header green">
            <span class="double-history__col-bets">
              <fa icon="coins" fixed-width />
              <span class="double-history__col-bets__value">{{ greenBets }}</span>
            </span>
          </div>
          <div v-for="(player, key) in getGreenPlayers()" :key="key" class="double-history__col-body">
            <div class="double-history__col-header-bet__user">
              <div class="double-history__col-header-bet__photo">
                <img class="w-100 h-100 avatar" :src="player.photo_url">
              </div>
              <div class="double-history__col-header-bet__row">
                <div class="double-history__col-header-bet__username">
                  {{ player.name }}
                </div>
                <div class="double-history__col-header-bet__amount">
                  {{ player.bet }}
                </div>
              </div>
            </div>
          </div>
          <div v-if="getGreenPlayers().length === 0" class="double-history__col-body">
            <div class="double-history__col-header-bet__user double-history__col-header-bet__no-rates">
              <span>{{ $t('no_rates') }}</span>
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="double-history__col-header black">
            <span class="double-history__col-bets">
              <fa icon="coins" fixed-width />
              <span class="double-history__col-bets__value">{{ blackBets }}</span>
            </span>
          </div>
          <div v-for="(player, key) in getBlackPlayers()" :key="key" class="double-history__col-body">
            <div class="double-history__col-header-bet__user">
              <div class="double-history__col-header-bet__photo">
                <img class="w-100 h-100 avatar" :src="player.photo_url">
              </div>
              <div class="double-history__col-header-bet__row">
                <div class="double-history__col-header-bet__username">
                  {{ player.name }}
                </div>
                <div class="double-history__col-header-bet__amount">
                  {{ player.bet }}
                </div>
              </div>
            </div>
          </div>
          <div v-if="getBlackPlayers().length === 0" class="double-history__col-body">
            <div class="double-history__col-header-bet__user double-history__col-header-bet__no-rates">
              <span>{{ $t('no_rates') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>

import Vue from 'vue'
import Form from 'vform'
import { mapGetters } from 'vuex'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import WS from '../../services/ws'

Vue.component('Icon', FontAwesomeIcon)

export default {
  name: 'Double',
  data () {
    return {
      form: new Form({
        canBet: true,
        balance: 0,
        photo_url: '',
        name: '',
        bet: 0,
        color: ''
      }),
      groceryList: [
        { id: 0, color: 'green' },
        { id: 1, color: 'red' },
        { id: 2, color: 'black' },
        { id: 3, color: 'red' },
        { id: 4, color: 'black' },
        { id: 5, color: 'red' },
        { id: 6, color: 'black' },
        { id: 7, color: 'red' },
        { id: 8, color: 'black' },
        { id: 9, color: 'red' },
        { id: 10, color: 'black' },
        { id: 11, color: 'red' },
        { id: 12, color: 'black' },
        { id: 13, color: 'red' },
        { id: 14, color: 'black' },
        { id: 15, color: 'red' },
        { id: 16, color: 'black' },
        { id: 17, color: 'red' },
        { id: 18, color: 'black' },
        { id: 19, color: 'red' },
        { id: 20, color: 'black' },
        { id: 21, color: 'red' },
        { id: 22, color: 'black' },
        { id: 23, color: 'red' },
        { id: 24, color: 'black' },
        { id: 25, color: 'red' },
        { id: 26, color: 'black' },
        { id: 27, color: 'red' },
        { id: 28, color: 'black' }
      ],
      historys: [],
      red: 0,
      green: 0,
      black: 0,
      spinTime: 5,
      rolling: false,
      timer: 0,
      loopsFunc: '',
      allPlayers: [],
      redBets: 0,
      greenBets: 0,
      blackBets: 0,
      connection: null
    }
  },

  computed: mapGetters({
    user: 'auth/user'
  }),

  created () {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key]
    })
    this.connection = WS

    this.connection.onmessage = function (event) {
      console.log(event)
    }

    console.log('created')

    this.connection.onopen = function (event) {
      console.log(event)
      console.log('Successfully connected to the echo websocket server...')
    }
  },

  mounted: function () {
    this.printHistory()
    this.loopsFunc = setInterval(() => {
      this.loops()
    }, 15000)
  },

  beforeDestroy () {
    clearInterval(this.loopsFunc)
  },

  methods: {
    roll () {
      if (this.rolling === false) {
        this.rolling = true
        const scroll = document.getElementById('scroll')
        const out = document.getElementById('out')

        const x = Math.floor(Math.random() * (28 + 1))

        out.innerHTML = "IT'S " + x
        this.historys.splice(0, 0, x)

        scroll.style.transition = 'margin ' + this.spinTime + 's ease'
        scroll.style.marginLeft = 'calc(250px - 25px - (1450px * 6) - (50px * ' + x + '))'

        setTimeout(() => {
          scroll.style.transition = 'margin 0s ease'
          scroll.style.marginLeft = 'calc(250px - 25px - (1450px * 1) - (50px * ' + x + '))'
          this.rolling = false
        }, this.spinTime * 1000)

        this.$refs.timerDiv.style.width = '100%'
        this.$refs.timerDiv.style.opacity = '0'

        setTimeout(() => {
          this.$refs.timerDiv.style.width = '0%'
        }, 1000)
        setTimeout(() => {
          if (this.isUserCanBet()) {
            if (this.historyColours(x) === 'red') {
              this.user.balance += this.red * 2
            }
            if (this.historyColours(x) === 'black') {
              this.user.balance += this.black * 2
            }
            if (this.historyColours(x) === 'green') {
              this.user.balance += this.green * 14
            }
          }
          this.updateBet('clear')
        }, 4000)
        setTimeout(() => {
          out.style.opacity = '1'
          out.style.animation = 'out .5s ease-out forwards'
        }, 5000)
        setTimeout(() => {
          out.style.opacity = '0'
        }, 7000)
        setTimeout(() => {
          out.style.animation = ''
          this.printHistory()
          this.clearBet()
        }, 7400)
      }
    },

    loops () {
      if (this.$refs.timerDiv.style) {
        this.$refs.timerDiv.style.width = '0'
        this.$refs.timerDiv.style.opacity = '1'
        setTimeout(() => {
          this.$refs.timerDiv.style.width = '100%'
        }, 1)
      }
      setTimeout(() => {
        console.log('start')
        this.roll()
      }, 5000)
    },

    printHistory () {
      if (this.historys.length >= 20) {
        this.historys.pop()
      }
      this.$refs.historyChild.innerHTML = ''
      for (let i = 0; i < this.historys.length; i++) {
        this.$refs.historyChild.innerHTML += "<div class='history-box " + this.historyColours(this.historys[i]) + "'>" + this.historys[i] + '</div>'
      }
    },

    historyColours (c) {
      c = parseInt(c)
      if (c === 0) {
        return 'green'
      }
      if (c % 2 === 0) {
        return 'black'
      } else {
        return 'red'
      }
    },

    updateBet (button) {
      this.form.bet = this.$refs.bet.value
      if (!Number.isInteger(+this.form.bet)) {
        console.log('Not Integer')
        return
      }
      if (!this.isUserCanBet() || this.form.canBet === false) {
        console.log('Permission Field')
        return
      }
      this.form.canBet = false
      if (this.rolling === false) {
        if (button === 'red') {
          this.red = parseInt(this.form.bet)
        }
        if (button === 'black') {
          this.black = parseInt(this.form.bet)
        }
        if (button === 'green') {
          this.green = parseInt(this.form.bet)
        }
        console.log(this.connection)
        this.connection.send(button)
        this.changeBoxes()
        this.user.balance = this.user.balance - this.form.bet
        this.updateBalance(button)
      } else {
        if (button === 'clear') {
          this.red = 0
          this.black = 0
          this.green = 0
        }
        this.changeBoxes()
      }
    },

    clearBet () {
      this.form.canBet = true
      this.allPlayers = []
      this.changeBoxes(true)
    },

    isNumber: function (evt) {
      console.log(evt)
      evt = (evt) || window.event
      const charCode = (evt.which) ? evt.which : evt.keyCode
      if (((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) || +this.user.balance <= +this.$refs.bet.value) {
        this.normalizeBet()
        evt.preventDefault()
      } else {
        return true
      }
    },

    updateBalance (color) {
      this.form.balance = this.user.balance
      this.form.color = color
      this.form.post('/api/panel/double/updateBalance').then(() => {
        this.allPlayers.push(this.form)
      }).catch(() => {
        this.user.balance = +this.user.balance + +this.form.bet
      })
    },

    isUserCanBet () {
      const bet = this.$refs.bet.value
      return this.user && this.user.balance >= 0 && bet && bet > 0
    },

    normalizeBet () {
      if (+this.user.balance < +this.$refs.bet.value) {
        this.$refs.bet.value = this.user.balance
      }
    },

    changeBoxes (clearButton = false) {
      if (clearButton) {
        this.red = 0
        this.green = 0
        this.black = 0
      }
      this.$refs.redBox.innerHTML = this.red
      this.$refs.greenBox.innerHTML = this.green
      this.$refs.blackBox.innerHTML = this.black
    },

    getRedPlayers () {
      const players = this.allPlayers.filter((item) => item.color === 'red')
      this.redBets = this.sumBets(players)
      return players
    },

    getGreenPlayers () {
      const players = this.allPlayers.filter((item) => item.color === 'green')
      this.greenBets = this.sumBets(players)
      return players
    },

    getBlackPlayers () {
      const players = this.allPlayers.filter((item) => item.color === 'black')
      this.blackBets = this.sumBets(players)
      return players
    },

    sumBets (players) {
      let sum = 0
      for (const player of Object.values(players)) {
        sum += +player.bet
      }
      return sum
    }
  }
}
</script>

<style scoped>
.game-component {
  min-height: 500px;
}

#out {
  width: 100%;
  height: 120px;
  line-height: 120px;
  color: #fff;
  font-size: 60px;
  font-weight: 700;
  text-transform: uppercase;
  text-align: center;
  opacity: 0;
  transition: opacity .4s ease;
}

@keyframes out{
  0%{transform: scale(0) rotate(0deg);}
  70%{transform: scale(1.1) rotate(2deg);}
  100%{transform: scale(1) rotate(0deg);}
}

.bg-color-container {
  background-color: #40407a;
}

.roulette {
  width: 100%;
  height: 100%;
  margin: 0 auto;
  border-radius: 5px;
  overflow: hidden;
}

.balanceDiv {
  color: #fff;
}

.game-footer {
  height: 70px;
  padding: 0 15px;
  background-color: #2c2c54;
  position: relative;
  margin: 25px auto;
}

.game-footer__container {
  overflow-x: auto;
  overflow-y: hidden;
}

::v-deep .history-box {
  width: 45px;
  height: 45px;
  background: red;
  border-radius: 50%;
  margin-right: 7px;
  text-align: center;
  line-height: 45px;
  color: #fff;
}

.timer {
  width: 0;
  height: 5px;
  background: tomato;
  transition: opacity .2s linear, width 5s linear;
}

.timer:after {
  display: block;
  content: '';
  clear: both;
}

.ticker {
  width: 0;
  border-top: 20px solid #2c2c54;
  border-left: 20px solid transparent;
  border-right: 20px solid transparent;
  margin: 0 auto;
  transform: translateY(4px);
}

.static {
  width: 500px;
  height: 50px;
  margin: 0 auto;
  overflow: hidden;
}

.static:before {
  position: absolute;
  display: block;
  content: '';
  width: 500px;
  height: 50px;
  margin-bottom: -50px;
  box-shadow: inset 100px 0px 70px -70px #40407a, inset -100px 0px 70px -70px #40407a;
}

.scroll {
  width: calc(1450px * 8);
  height: 50px;
  background: red;
  overflow: hidden;
  margin-left: calc(250px - 25px - (1450px * 1) - (50px * 0));
}

.box-container {
  width: 1450px;
  height: 50px;
  background: blue;
  float: left;
}

.box {
  width: 50px;
  height: 50px;
  line-height: 50px;
  text-align: center;
  color: #fff;
  float: left;
}

#green,
::v-deep .green,
.box:nth-child(1) {
  background: #01c236 !important;
}

#red,
::v-deep .red,
.box:nth-child(even) {
  background: red;
}

#black,
::v-deep .black,
.box:nth-child(odd) {
  background: #333537;
}

@keyframes out {
  0% {
    transform: scale(0) rotate(0deg);
  }
  70% {
    transform: scale(1.1) rotate(2deg);
  }
  100% {
    transform: scale(1) rotate(0deg);
  }
}

.bet-container {
  height: 200px;
  background-color: #40407a;
  border-radius: 5px;
}

.bet-title {
  height: 40px;
  line-height: 40px;
  padding: 0 20px;
  border-bottom: 1px solid RGBA(0, 0, 0, 0.3);
  color: #999;
  box-sizing: border-box;
}

.button-holder {
  display: flex;
  justify-content: space-around;
  margin: 0 10px;
}

.btn-x {
  cursor: pointer;
  width: 80px;
  height: 40px;
  display: block;
  background: RGBA(0, 0, 0, 0.3);
  outline: 0;
  border: 1px solid RGBA(0, 0, 0, 0.3);
  border-radius: 5px;
  color: #fff;
}

.bet {
  display: block;
  height: 40px;
  line-height: 40px;
  margin: 20px auto 0;
  border-radius: 5px;
  background: RGBA(0, 0, 0, 0.3);
  border: 1px solid RGBA(0, 0, 0, 0.3);
  padding-left: 20px;
  box-sizing: border-box;
  color: #ddd;
  outline: 0;
  max-width: 100%;
}

.bet::placeholder {
  color: #fff;
}

.bets-holder {
  display: flex;
  justify-content: space-around;
  margin: 15px 10px 5px 10px;
}

.bets-holder .boxes {
  width: 80px;
  text-align: center;
  color: #fff;
  font-weight: 700;
}

.game-container_history {
  margin-top: 10px;
  padding: 0 15px;
}

.game_history {
  padding: 15px 0;
  border-radius: 7px;
}

.double-history__col-header {
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
  display: flex;
  color: #fff;
  padding: 10px;
}

.double-history__col-bets {
  font-size: 16px;
  font-weight: 800;
}

.double-history__col-body {
  color: #fff;
  font-size: 14px;
  padding: 3px 10px 0 10px;
  background: #1d1d1d;
  border-bottom-left-radius: 5px;
  border-bottom-right-radius: 5px;
}

.double-history__col-header-bet__user {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 8px 0 8px 0;
  border-bottom: 1px solid #333;
}

.double-history__col-header-bet__no-rates {
  justify-content: center;
  height: 62px;
}

.double-history__col-header-bet__photo {
  width: 45px;
  min-width: 45px;
  height: 45px;
  margin-right: 7px;
  overflow: hidden;
  border-radius: 50%;
}

.double-history__col-header-bet__row{
  max-width: calc(100% - 55px);
  display: flex;
  width: 100%;
  align-items: center;
}

.double-history__col-header-bet__amount{
  padding: 4px 8px;
  border: 1px solid;
  border-radius: 5px;
  margin-left: auto;
  font-size: 13px;
}
</style>
