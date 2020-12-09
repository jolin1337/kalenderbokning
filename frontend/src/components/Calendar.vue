<template>
  <v-container>
    <v-row>
      <v-col>
        <v-alert
          :value="showAlert"
          color="pink"
          dark
          border="top"
          icon="mdi-delete"
          transition="scale-transition"
        >
          Vill du ta bort din bokning?
          <v-row>
            <v-col>
              <v-btn class="mr-4" @click="removeEvent"> Ta bort </v-btn>
            </v-col>
            <v-col>
              <v-btn class="mr-4" @click="hideEvent"> Avbryt </v-btn>
            </v-col>
          </v-row>
        </v-alert>
      </v-col>
    </v-row>
    <v-row class="fill-height">
      <v-col>
        <v-sheet height="600">
          <v-calendar
            ref="calendar"
            v-model="value"
            color="primary"
            type="4day"
            :events="events"
            @contextmenu:event="showEvent"
            :event-color="getEventColor"
            :event-ripple="false"
            @change="getEvents"
            @mousedown:event="startDrag"
            @mousedown:time="startTime"
            @mousemove:time="mouseMove"
            @mouseup:time="endDrag"
            @mouseleave.native="cancelDrag"
          >
            <template v-slot:event="{ event, timed, eventSummary }">
              <div class="v-event-draggable" v-html="eventSummary()"></div>
              <div
                v-if="timed"
                class="v-event-drag-bottom"
                @mousedown.stop="extendBottom(event)"
              ></div>
            </template>
          </v-calendar>
        </v-sheet>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        Dra och släpp för att skapa en boknings tid. Du kan flytta din bokning
        genom att dra den upp eller ner. För att ta bort bokningen högerklicka
        och välj, ta bort. När din bokning ser bra ut klickar du på boka knappen
        så skickar vi en inbjudan till dig på mail/avbokan ifall du tagit bort
        din bokning.
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
export default {
  props: {
    canAddNew: {
      default: () => false
    },
    canModify: {
      default: () => false
    },
    customAttrs: {
      default: () => ({})
    }
  },
  data () {
    return {
      minDuration: 30 * 60 * 1000,
      maxDuration: 60 * 60 * 1000,
      value: '',
      showAlert: false,
      activeEvent: null,
      events: [],
      newEvents: [],
      colors: [
        '#2196F3',
        '#3F51B5',
        '#673AB7',
        '#00BCD4',
        '#4CAF50',
        '#FF9800',
        '#757575'
      ],
      names: [
        'Meeting',
        'Holiday',
        'PTO',
        'Travel',
        'Event',
        'Birthday',
        'Conference',
        'Party'
      ],
      dragEvent: null,
      dragTime: null,
      dragStart: null,
      createEvent: null,
      createStart: null,
      extendOriginal: null
    }
  },
  methods: {
    showEvent ({ event }) {
      if (this.canModify(event)) {
        this.activeEvent = event
        this.showAlert = true
      }
    },
    hideEvent () {
      this.showAlert = false
      this.activeEvent = null
    },
    removeEvent () {
      if (this.canModify(this.activeEvent)) {
        this.hideEvent()
        const idx = this.events.indexOf(this.activeEvent)
        this.events.splice(idx, 1)
      }
    },
    startDrag ({ event, timed }) {
      if (event && timed && this.canModify(event)) {
        this.dragEvent = event
        this.dragTime = null
        this.extendOriginal = null
      }
    },
    startTime (tms) {
      const mouse = this.toTime(tms)

      if (this.dragEvent && this.dragTime === null) {
        const start = this.dragEvent.start

        this.dragTime = mouse - start
      } else {
        if (!this.canAddNew(this.events)) return
        this.createStart = this.roundTime(mouse)
        this.createEvent = {
          id: this.getUuid(),
          name: `Boka Här`,
          color: this.rndElement(this.colors),
          start: this.createStart,
          end: this.createStart + this.minDuration,
          timed: true,
          ...this.customAttrs
        }

        this.events.push(this.createEvent)
        this.newEvents.push(this.createEvent)
      }
    },
    extendBottom (event) {
      this.createEvent = event
      this.createStart = event.start
      this.extendOriginal = event.end
    },
    mouseMove (tms) {
      const mouse = this.toTime(tms)
      if (this.dragEvent && this.dragTime !== null) {
        const start = this.dragEvent.start
        const end = this.dragEvent.end
        const duration = Math.max(
          this.minDuration,
          Math.min(this.maxDuration, end - start)
        )
        const newStartTime = mouse - this.dragTime
        const newStart = this.roundTime(newStartTime)
        const newEnd = newStart + duration

        this.dragEvent.start = newStart
        this.dragEvent.end = newEnd
      } else if (this.createEvent && this.createStart !== null) {
        const mouseRounded = this.roundTime(mouse, false)
        let min = Math.min(mouseRounded, this.createStart)
        let max = Math.max(mouseRounded, this.createStart)
        const duration = Math.max(
          this.minDuration,
          Math.min(this.maxDuration, max - min)
        )
        if (mouseRounded === min && duration < max - min) {
          min = max - duration
        } else if (mouseRounded === min && duration > max - min) {
          min = max - duration
        }
        if (mouseRounded === max && duration < max - min) {
          max = min + duration
        } else if (mouseRounded === max && duration > max - min) {
          max = min + duration
        }

        this.createEvent.start = min
        this.createEvent.end = max
      }
    },
    endDrag () {
      this.dragTime = null
      this.dragEvent = null
      this.createEvent = null
      this.createStart = null
      this.extendOriginal = null
    },
    cancelDrag () {
      if (this.createEvent) {
        if (this.extendOriginal) {
          this.createEvent.end = this.extendOriginal
        } else {
          const i = this.events.indexOf(this.createEvent)
          if (i !== -1) {
            this.events.splice(i, 1)
          }
        }
      }

      this.createEvent = null
      this.createStart = null
      this.dragTime = null
      this.dragEvent = null
    },
    roundTime (time, down = true) {
      const roundTo = 15 // minutes
      const roundDownTime = roundTo * 60 * 1000

      return down
        ? time - (time % roundDownTime)
        : time + (roundDownTime - (time % roundDownTime))
    },
    toTime (tms) {
      return new Date(
        tms.year,
        tms.month - 1,
        tms.day,
        tms.hour,
        tms.minute
      ).getTime()
    },
    getEventColor (event) {
      const rgb = parseInt(event.color.substring(1), 16)
      const r = (rgb >> 16) & 0xff
      const g = (rgb >> 8) & 0xff
      const b = (rgb >> 0) & 0xff

      return event === this.dragEvent
        ? `rgba(${r}, ${g}, ${b}, 0.7)`
        : event === this.createEvent
          ? `rgba(${r}, ${g}, ${b}, 0.7)`
          : event.color
    },
    getEvents ({ start, end }) {
      /** {
         name: this.rndElement(this.names),
        color: this.rndElement(this.colors),
        start,
        end,
        timed,
      } */
      let url = window.location.href.split('#')[0]
      axios
        .get(`${url}?start=${start.date}&end=${end.date}&action=events`)
        .then((e) => {
          this.events = e.data
        })
    },
    rnd (a, b) {
      return Math.floor((b - a + 1) * Math.random()) + a
    },
    rndElement (arr) {
      return arr[this.rnd(0, arr.length - 1)]
    },
    getUuid () {
      const format = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'
      return format.replace(/[xy]/g, function (c) {
        let r = (Math.random() * 16) | 0
        let v = c === 'x' ? r : (r & 0x3) | 0x8
        return v.toString(16)
      })
    }
  }
}
</script>
