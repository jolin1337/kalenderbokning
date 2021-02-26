<template>
  <div>
    <login-info :info="userInfo"></login-info>
    <v-form v-model="valid" ref="form" lazy-validation>
      <v-container>
        <v-row>
          <!-- <v-col cols="12" md="2"></v-col> -->
          <v-col cols="12" md="12">
            <div style="text-align: left;padding: 0 50px;">
              <v-container>
                <v-row>
                  <v-col cols="12" md="12">
                    <v-card color="green lighten-2" class="mx-auto" v-if="myBooking">
                      <v-card-title class="title">
                        <v-container class="my-booking-card">
                          <v-row>
                            <v-col cols="12" md="12">
                              <div class="white--text">Min bokning:</div>
                              <h2 class="white--text font-weight-light">
                                <v-icon dark size="42" class="mr-4"> mdi-calendar-clock </v-icon>
                                {{myBooking.date}}
                              </h2>
                              <h3 class="white--text font-weight-light">
                                  {{myBooking.timeStart}} - {{myBooking.timeEnd}}
                              </h3>
                              <div v-if="link" class="white--text">
                                Mötes länk: <a :href="link" class="white--text" target="_blank">{{link}}</a></div>
                              <v-btn class="mr-4" @click.stop.prevent="validate('deleteEvent')">Avboka</v-btn>
                            </v-col>
                          </v-row>
                        </v-container>
                      </v-card-title>
                    </v-card>
                  </v-col>
                </v-row>
              </v-container>
            </div>
            <h3>{{ $locale.home_title }}</h3>
            <v-timeline dense>
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
                  :is-admin="isAdmin && slot.email === email"
                  :is-owner="(checkBooked(slot) || {}).email === email"
                  :guidance-email="slot.email"
                  :selected="activeEvent.time.toString() === slot.time.toString() && activeEvent.email === slot.email"
                  :time="slot.time"
                  :emailColor="emailColor(slot.email)" />
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
          <!-- <v-col cols="12" md="2"></v-col> -->
        </v-row>
      </v-container>

      <alert :show="!!errorMsg" :title="$locale.home_errorTitle" color="red" @close="errorMsg = ''" :alert-msg="errorMsg"></alert>
      <alert
        color="blue"
        scrollable
        v-if="!!newSlot"
        :title="$locale.home_addSlotTitle"
        @close="newSlot = false"
        :alert-msg="$locale.home_addSlotSubTitle">
        <template v-slot:content>
            <v-container>
              <v-row>
                <v-col>
                  <v-text-field
                    v-model="newSlot.link"
                    :label="'Länk till remote möte'"
                  ></v-text-field>
                </v-col>
              </v-row>
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
  </div>
</template>

<script>
import axios from 'axios'
import TimeSlot from './TimeSlot.vue'
import Alert from './Alert.vue'
import LoginInfo from './LoginInfo'

export default {
  components: {
    TimeSlot,
    Alert,
    LoginInfo
  },
  data () {
    return {
      email: null,
      link: null,
      isAdmin: false,
      errorMsg: '',
      newSlot: false,
      valid: false,
      eventsLoaded: false,
      eventSlots: [],
      events: [],
      activeEvent: {
        time: '',
        email: ''
      },
      myBooking: false
    }
  },
  computed: {
    userInfo () {
      return {
        email: this.email,
        link: this.link,
        isAdmin: this.isAdmin
      }
    },
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
      if (this.activeEvent.email === '') {
        this.activeEvent = {
          email: this.myBooking.guidance_email,
          time: new Date(this.myBooking.date + 'T' + this.myBooking.timeStart)
        }
      }
      const booked = this.checkBooked(this.activeEvent)
      if (this.valid && (!booked || this.email === booked.email)) {
        let data = new FormData()
        let event = this.events.find((e) => e.email === this.email)
        if (!event) {
          event = { email: this.email }
        }
        event.name = 'Event for ' + this.email
        event.guidance_email = this.activeEvent.email
        event.start = this.activeEvent.time.getTime()
        event.end = this.activeEvent.time.getTime() + 30 * 60 * 1000
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
        data.append('timeslots', JSON.stringify([slot.time]))
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
        const time = this.newSlot.date + 'T' + this.newSlot.time + ':00.000Z'
        const link = this.newSlot.link
        data.append('timeslots', JSON.stringify([{time, link}]))
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
            this.eventSlots = Object.values(e.data).map(e => {
              return {
                ...e,
                time: new Date(e.time)
              }
            })
            this.eventSlots.sort((a, b) => a.time - b.time)
          }
        })
    },
    selectTime (slot) {
      const booked = this.checkBooked(slot)
      if (!booked || booked.email === this.email) {
        this.activeEvent = slot
      }
    },
    checkBooked (slot) {
      const end = new Date(slot.time.getTime() + 30 * 60 * 1000)
      const bookedEvent = this.events.find(e => {
        const d = new Date(e.start)
        const isBookedInterval = slot.time < new Date(d.getTime() + 30 * 60 * 1000) && end > d
        const isGuidanceEmail = e.guidance_email === slot.email
        return isBookedInterval && isGuidanceEmail
      })
      return bookedEvent
    },
    emailColor (email) {
      return '#' + Array.from(email.substring(0, 6)).map(ch => {
        return (Math.abs(ch.charCodeAt(0) - 'a'.charCodeAt(0)) % 16).toString(16)
      }).join('')
    }
  },
  mounted () {
    const start = new Date()
    start.setHours(1, 0, 0, 0)
    const end = new Date(start)
    end.setDate(end.getDate() + (this.$locale.bookableDaysForward || 7))
    axios
      .get(`${this.url}/?action=loggedin`)
      .then((r) => {
        this.email = r.data.email
        this.link = r.data.link
        this.isAdmin = r.data.admin
        this.eventsLoaded = true
        if (r.data.bookedEvent && r.data.bookedEvent.length > 0) {
          const bookTime = new Date(r.data.bookedEvent[0].start).toISOString().split('T')
          this.myBooking = {
            date: bookTime[0],
            guidance_email: r.data.bookedEvent[0].guidance_email,
            timeStart: bookTime[1].substring(0, '00:00'.length),
            timeEnd: new Date(r.data.bookedEvent[0].end).toISOString().split('T')[1].substring(0, '00:00'.length)
          }
        }
        if (r.data.loggedin === false) {
          this.$router.push('/login')
          throw Error('Not admin in')
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
.my-booking-card {
  padding: 0;
}
</style>
