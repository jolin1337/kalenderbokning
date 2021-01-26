<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-container>
      <v-row>
        <v-col>
          <h1>{{ msg }}</h1>
          <v-text-field
            v-model="email"
            :rules="emailRules"
            :counter="30"
            label="E-post"
            required
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <v-text-field
            v-model="password"
            type="password"
            :rules="passwordRules"
            label="Lösenord"
            required
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <v-btn :disabled="!valid" class="mr-4" @click="validate">
            Logga in
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </v-form>
</template>

<script>
import axios from 'axios'
export default {
  data () {
    return {
      msg: 'Logga in för att boka samtal',
      valid: false,
      email: '',
      password: '',
      emailRules: [
        (v) => !!v || 'E-mail is required',
        (v) => /.+@.+/.test(v) || 'E-mail måste vara korrekt'
      ],
      passwordRules: [(v) => !!v || 'Lösenord krävs för att logga in']
    }
  },
  methods: {
    validate () {
      if (this.$refs.form.validate() && this.valid) {
        const bodyFormData = new FormData()
        bodyFormData.append('email', this.email)
        bodyFormData.append('pwd', this.password)
        bodyFormData.append('action', 'login')
        let url = window.location.href.split('#')[0]
        axios
          .post(url, bodyFormData)
          .then(() => {
            this.$router.push('/')
          })
          .catch((e) => {
            console.error(e)
          })
      }
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
</style>
