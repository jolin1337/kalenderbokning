<template>
  <v-timeline-item :color="color" fill-dot :small="!selected"> <!--:right="!booked || isOwner" :left="booked && !isOwner">-->
    <v-card :color="color" class="mx-auto">
        <v-card-title class="title" v-if="booked && !isOwner">
            <v-container class="time-card">
                <v-row>
                    <v-col cols="12" md="12">
                        <h2 class="white--text font-weight-light">
                            <v-icon dark size="42" class="mr-4"> mdi-calendar-clock </v-icon>
                            {{date}}
                        </h2>
                        <h3 class="white--text font-weight-light">
                            {{timeStart}} - {{timeEnd}}
                        </h3>
                    </v-col>
                </v-row>
            </v-container>
        </v-card-title>
        <v-card-title class="title" v-else>
            <v-container class="time-card">
                <v-row>
                    <v-col cols="12" md="12">
                        <h2 class="white--text font-weight-light">
                            <v-icon dark size="42" class="mr-4"> mdi-calendar-clock </v-icon>
                            {{date}}
                        </h2>
                        <h3 class="white--text font-weight-light">
                            {{timeStart}} - {{timeEnd}}
                        </h3>
                        <v-container v-show="selected">
                            <v-row>
                                <v-col>
                                    <p>
                                    <v-btn v-if="!booked && isAdmin" class="mr-4" @click.stop.prevent="$emit('remove')">
                                        {{$locale.timeSlot_removeButton}}
                                    </v-btn>
                                    </p>
                                    <v-btn v-if="!isOwner" class="mr-4" @click.stop.prevent="$emit('book')">
                                        {{$locale.timeSlot_bookButton}}
                                    </v-btn>
                                    <v-btn v-else class="mr-4" @click.stop.prevent="$emit('cancel')">
                                        {{$locale.timeSlot_cancelButton}}
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-col>
                </v-row>
            </v-container>
        </v-card-title>
    </v-card>
  </v-timeline-item>
</template>

<script>
export default {
    props: {
        time: Date,
        booked: Boolean,
        selected: Boolean,
        isOwner: Boolean,
        isAdmin: Boolean,
        emailColor: {
            default () {
                return 'blue'
            },
            type: String
        }
    },
    computed: {
        date () {
            return this.time.toISOString().split('T')[0]
        },
        timeStart () {
            const ds = this.time.toISOString().split('.')[0].split('T')[1]
            return ds.substring(0, ds.length - 3)
        },
        timeEnd () {
            const end = new Date(this.time.getTime() + 30 * 60 * 1000)
            const ds = end.toISOString().split('.')[0].split('T')[1]
            return ds.substring(0, ds.length - 3)
        },
        color () {
            if (this.selected) {
                return 'purple lighten-2'
            }
            if (this.isOwner) {
                return 'green lighten-2'
            }
            if (this.booked) {
                return 'grey lighten-2'
            }
            return this.emailColor
        }
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
.time-card {
    text-align: left;
    padding: 0;
}
</style>
