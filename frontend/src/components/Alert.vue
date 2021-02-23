<template>
    <v-dialog
        :z-index="10"
        :value="show"
        :dark="false"
        max-width="1000px"
        scrollable
        light
    >
        <v-card :color="color + ' lighten-1'" class="mx-auto">
            <v-card-title class="title">
                <h2 class="white--text font-weight-light">
                {{title}}
                </h2>
            </v-card-title>
            <v-card-text class="white text--primary">
                <v-container>
                    <v-row>
                        <v-col>
                            <br/>
                            <h2 class="font-weight-light">
                                <p>{{alertMsg}}</p>
                            </h2>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                            <slot name="content"></slot>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                            <slot name="buttons"></slot>
                            <v-btn
                                class="white--text"
                                color="grey"
                                @click="$emit('close')"
                            >
                            {{$locale.alert_closeButton}}
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: {
        show: {
            type: Boolean,
            default () {
                return true
            }
        },
        alertMsg: String,
        color: {
            type: String,
            default () {
                return 'blue'
            }
        },
        title: String
    }
}
</script>
