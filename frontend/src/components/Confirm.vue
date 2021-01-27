<template>
  <v-form ref="form" lazy-validation>
    <v-container>
      <v-row>
        <v-col> {{ $locale.confirm_title }} </v-col>
      </v-row>
      <v-row>
        <v-col>
          <v-btn class="mr-4">
            <router-link to="/">{{ $locale.confirm_backButton }}</router-link>
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </v-form>
</template>

<script>
export default {}
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
