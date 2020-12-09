<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-container>
      <v-row>
        <v-col>
          <h1>{{ msg }}</h1>
          <calendar
            :canModify="canModify"
            :canAddNew="canAddNew"
            :custom-attrs="customAttrs"
            ref="cal"
          ></calendar>
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <v-btn :disabled="!valid" class="mr-4" @click="validate">
            Boka!
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </v-form>
</template>

<script>
import axios from 'axios'
import Vue from 'vue'
import calendar from '@/components/Calendar'

export default {
  components: {
    calendar
  },
  data () {
    return {
      email: null,
      msg: 'Välj en tid att boka möte',
      valid: false,
      eventsLoaded: false,
      date: ''
    }
  },
  computed: {
    customAttrs () {
      return { email: this.email }
    },
    url () {
      return window.location.href.split('#')[0]
    }
  },
  methods: {
    validate () {
      this.$refs.form.validate()
      if (this.valid) {
        let data = new FormData()
        let event = this.$refs.cal.events.find((e) => e.email === this.email)
        if (event) {
          event.name = 'Event for ' + this.email
          data.append('event', JSON.stringify(event))
          data.append('action', 'addEvent')
        } else {
          event = { email: this.email }
          data.append('event', JSON.stringify(event))
          data.append('action', 'deleteEvent')
        }
        axios
          .post(this.url, data)
          .then((e) => {
            if (!e.data.error) {
              this.$router.push('/confirm')
            } else {
              alert(
                'Du kunde inte skapa/uppdatera/ta bort din bokning på denna tiden'
              )
            }
          })
          .catch((e) => {
            console.error(e)
          })
      }
    },
    canAddNew (events) {
      return this.eventsLoaded && !events.find((e) => e.email === this.email)
    },
    canModify (event) {
      return this.eventsLoaded && event.email === this.email
    }
  },
  mounted () {
    if (Vue.config.devtools) {
      this.eventsLoaded = true
      return
    }
    axios
      .get(`${this.url}?action=loggedin`)
      .then((r) => {
        this.email = r.data.email
        this.eventsLoaded = true
        if (r.data.loggedin === false) {
          this.$router.push('/login')
        }
      })
      .catch((e) => {
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
