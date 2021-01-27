<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-container>
      <v-row>
        <v-col cols="12" md="2"></v-col>
        <v-col md="8">
          <h1>{{ msg }}</h1>
          <v-timeline :dense="$vuetify.breakpoint.smAndDown">
            <v-sheet v-for="(slot, key) in eventSlots"  @click="selectTime(slot)" :key="key">
              <time-slot 
                @book="validate('addEvent')"
                @cancel="validate('deleteEvent')"
                :booked="!!checkBooked(slot)" 
                :is-owner="(checkBooked(slot) || {}).email === email"
                :selected="activeEvent.toString() === slot.toString()" :time="slot" />
            </v-sheet>
          </v-timeline>
        </v-col>
        <v-col cols="12" md="2"></v-col>
      </v-row>
    </v-container>

    <v-overlay
      :z-index="10"
      :value="alert"
    >
      <v-card color="red lighten-1" class="mx-auto">
        <v-card-title class="title">
          <h2 class="white--text font-weight-light">
            Ett fel inträffade
          </h2>
        </v-card-title>
        <v-card-text class="white text--primary">
          <br/>
          <h2 class="font-weight-light">
              <p>{{alertMsg}}</p>
          </h2>
        </v-card-text>
        <v-card-text class="white text--primary">
              <v-btn
                class="white--text"
                color="teal"
                @click="alert = false"
              >
                Sträng ruta
              </v-btn>
        </v-card-text>
      </v-card>
    </v-overlay>
  </v-form>
</template>

<script>
import axios from 'axios'
import TimeSlot from './TimeSlot.vue'

export default {
  components: {
    TimeSlot
  },
  data () {
    return {
      email: null,
      msg: 'Välj en tid att boka samtal',
      alert: false,
      alertMsg: '',
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
              this.alert = true
              this.alertMsg = 'Du kunde inte utföra din bokning på denna tiden, anledning: ' + e.data.error
            }
          })
          .catch((e) => {
            console.error(e)
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
        this.eventsLoaded = true
        if (r.data.loggedin === false) {
          this.$router.push('/login')
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
