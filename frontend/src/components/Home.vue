<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-container>
      <v-row>
        <v-col cols="12" md="2"></v-col>
        <v-col md="8">
          <h1>{{ $locale.home_title }}</h1>
          <v-timeline :dense="$vuetify.breakpoint.smAndDown">
            <v-sheet v-if="isAdmin" @click="newSlot = {}">
              <v-timeline-item icon="mdi-plus" color="green lighten-0" class="text-left">
                <h3 class="font-weight-light" style="margin-top: 10px;color: grey">
                  {{$locale.home_addNewSlotButton}}
                </h3>
              </v-timeline-item>
            </v-sheet>
            <v-sheet v-for="(slot, key) in eventSlots"  @click="selectTime(slot)" :key="key">
              <time-slot 
                @book="validate('addEvent')"
                @cancel="validate('deleteEvent')"
                @remove="removeSlot(slot)"
                :booked="!!checkBooked(slot)"
                :is-admin="isAdmin"
                :is-owner="(checkBooked(slot) || {}).email === email"
                :selected="activeEvent.toString() === slot.toString()" :time="slot" />
            </v-sheet>
            <v-sheet v-if="isAdmin" @click="newSlot = {}">
              <v-timeline-item icon="mdi-plus" color="green lighten-0" class="text-left">
                <h3 class="font-weight-light" style="margin-top: 10px;color: grey">
                  {{$locale.home_addNewSlotButton}}
                </h3>
              </v-timeline-item>
            </v-sheet>
          </v-timeline>
        </v-col>
        <v-col cols="12" md="2"></v-col>
      </v-row>
    </v-container>

    <alert :show="!!errorMsg" :title="$locale.home_errorTitle" color="red" @close="errorMsg = ''" :alert-msg="errorMsg"></alert>
    <alert
      color="blue"
      v-if="!!newSlot"
      :title="$locale.home_addSlotTitle" 
      @close="newSlot = false" 
      :alert-msg="$locale.home_addSlotSubTitle">
      <template v-slot:content  >
          <v-container>
            <v-row>
              <v-col>
                <v-date-picker class="theme--light"
                  color="green lighten-1"
                  locale="swe"
                  v-model="newSlot.date"></v-date-picker>
              </v-col>
              <v-col>
                <v-time-picker
                  color="green lighten-1"
                  v-model="newSlot.time"
                  :allowed-minutes="(m) => m % 5 === 0"
                  format="24hr"
                ></v-time-picker>
              </v-col>
            </v-row>
          </v-container>
      </template>
      <template v-slot:buttons>
        <v-btn
            class="white--text"
            color="teal"
            @click="addSlot()"
        >
        {{$locale.home_addSlotButton}}
        </v-btn>
      </template>
    </alert>
  </v-form>
</template>

<script>
import axios from 'axios'
import TimeSlot from './TimeSlot.vue'
import Alert from './Alert.vue'

export default {
  components: {
    TimeSlot,
    Alert
  },
  data () {
    return {
      email: null,
      isAdmin: false,
      errorMsg: '',
      newSlot: false,
      valid: false,
      eventsLoaded: false,
      eventSlots: [],
      events: [],
      activeEvent: ''
    }
  },
  computed: {
    customAttrs () {
      return { email: this.email }
    },
    url () {
      let url = window.location.href.split('#')[0].split('?')[0]
      if (url.endsWith('index.php')) url = url.replace('index.php', '')
      if (url.endsWith('index.html')) url = url.replace('index.html', '')
      return url + 'api.php'
    }
  },
  methods: {
    validate (action) {
      this.$refs.form.validate()
      const booked = this.checkBooked(this.activeEvent)
      if (this.valid && (!booked || this.email === booked.email)) {
        let data = new FormData()
        let event = this.events.find((e) => e.email === this.email)
        if (!event) {
          event = { email: this.email }
        }
        event.name = 'Event for ' + this.email
        event.start = this.activeEvent.getTime()
        event.end = this.activeEvent.getTime() + 30 * 60 * 1000
        data.append('event', JSON.stringify(event))
        data.append('action', action)
        axios
          .post(this.url, data)
          .then((e) => {
            if (!e.data.error) {
              this.$router.push('/confirm')
            } else {
              this.showAlert = true
              this.errorMsg = this.$locale.home_unableToAddEventError + e.data.error
            }
          })
          .catch((e) => {
            console.error(e)
          })
      }
    },
    removeSlot (slot) {
      if (this.isAdmin && !this.checkBooked(slot)) {
        let data = new FormData()
        data.append('timeslots', JSON.stringify([slot]))
        data.append('action', 'removeTimeslots')
        axios
          .post(this.url, data)
          .then(e => {
            if (!e.data.error) {
              this.$router.push('/confirm')
            } else {
              this.showAlert = true
              this.errorMsg = this.$locale.home_unableToRemoveSlotError + e.data.error
            }
          })
      }
    },
    addSlot () {
      if (this.newSlot.date && this.newSlot.time && this.isAdmin) {
        let data = new FormData()
        const slot = this.newSlot.date + 'T' + this.newSlot.time + ':00.000Z'
        data.append('timeslots', JSON.stringify([slot]))
        data.append('action', 'addTimeslots')
        axios
          .post(this.url, data)
          .then(e => {
            this.newSlot = false
            if (!e.data.error) {
              this.$router.push('/confirm')
            } else {
              this.showAlert = true
              this.errorMsg = this.$locale.home_unableToAddSlotError + e.data.error
            }
          })
      }
    },
    getEvents ({ start, end }) {
      /** {
         name: this.rndElement(this.names),
        color: this.rndElement(this.colors),
        start,
        end,
        timed,
      } */
      axios
        .get(`${this.url}?start=${start}&end=${end}&action=events`)
        .then((e) => {
          if (e.data) {
            this.events = Object.values(e.data)
            this.events.sort((a, b) => new Date(a.start) - new Date(b.start))
          }
        })
    },
    getEventSlots ({ start, end }) {
      return axios
        .get(`${this.url}/?action=eventslots&start=${start}&end=${end}`)
        .then((e) => {
          if (e.data) {
            this.eventSlots = Object.values(e.data).map(e => new Date(e))
            this.eventSlots.sort((a, b) => a-b)
          }
        })
    },
    selectTime (date) {
      const booked = this.checkBooked(date)
      if (!booked || booked.email === this.email) {
        this.activeEvent = date;
      }
    },
    checkBooked (start) {
      const end = new Date(start.getTime() + 30 * 60 * 1000)
      return this.events.find(e => {
        const d = new Date(e.start)
        return start < new Date(d.getTime() + 30 * 60 * 1000) && end > d
      })
    }
  },
  mounted () {
    const start = new Date()
    start.setHours(1,0,0,0)
    const end = new Date(start)
    end.setDate(end.getDate() + 7)
    axios
      .get(`${this.url}/?action=loggedin`)
      .then((r) => {
        this.email = r.data.email
        this.isAdmin = r.data.admin
        this.eventsLoaded = true
        if (r.data.loggedin === false) {
          this.$router.push('/login')
          throw Error("Not admin in")
        }
      })
      .then(() => this.getEventSlots({
        start: start.toISOString(),
        end: end.toISOString()
      }))
      .then(() => this.getEvents({
        start: start.toISOString(),
        end: end.toISOString()
      }))
      .catch((e) => {
        console.error(e)
        this.$router.push('/login')
      })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h1,
h2 {
  font-weight: normal;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>
