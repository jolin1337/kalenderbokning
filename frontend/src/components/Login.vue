<template>
  <v-form v-model="valid" ref="form" lazy-validation class="login-form">
    <v-container>
      <v-row>
        <v-col>
          <h1>{{ $locale.login_title }}</h1>
          <v-alert
            border="top"
            color="red lighten-2"
            type="error"
            v-if="error"
          >
            {{error}}
          </v-alert>
          <v-text-field
            v-model="email"
            :rules="emailRules"
            :counter="30"
            :label="$locale.login_email"
            required
            @keyup.enter="validate()"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <v-text-field
            v-model="password"
            type="password"
            :rules="passwordRules"
            :label="$locale.login_password"
            required
            @keyup.enter="validate()"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <v-btn :disabled="!valid" class="mr-4" @click="validate()">
            {{$locale.login_loginButton}}
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
      valid: false,
      error: false,
      email: '',
      password: '',
      emailRules: [
        (v) => !!v || this.$locale.login_emailRules_emailRequired1,
        (v) => /.+@.+/.test(v) || this.$locale.login_emailRules_emailRequired2
      ],
      passwordRules: [(v) => !!v || this.$locale.login_emailRules_passwordRequired]
    }
  },
  computed: {
    url () {
      let url = window.location.href.split('#')[0].split('?')[0]
      if (url.endsWith('index.php')) url = url.replace('index.php', '')
      if (url.endsWith('index.html')) url = url.replace('index.html', '')
      return url + 'api.php'
    }
  },
  methods: {
    validate () {
      if (this.$refs.form.validate() && this.valid) {
        const bodyFormData = new FormData()
        bodyFormData.append('email', this.email)
        bodyFormData.append('pwd', this.password)
        bodyFormData.append('action', 'login')
        axios
          .post(this.url, bodyFormData)
          .then(resp => {
            if (!resp.data.error) this.$router.push('/')
            else {
              this.error = resp.data.error
            }
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
.login-form {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: white;
}
</style>
