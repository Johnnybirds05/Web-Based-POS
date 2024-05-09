<template>
  <v-app class="content">
    <v-main class="items ma-auto d-flex justify-center align-center">
      <v-row class="d-flex justify-center align-center">
        <v-col cols="12" sm="10" md="5">
          <v-form @submit.prevent="submitForm">
          <v-card class="ma-8 pa-13" elevation="5" width="450" height="600" rounded="lg" color="rgba(255,255,255,.85)">
            <v-card-item class="mt-5">
              <v-row>
                <v-col class="d-flex justify-center align-center my-7">
                  <v-avatar color="light-blue" size="80" icon="mdi-account-circle">
                  </v-avatar>
                </v-col>
              </v-row>

              <v-text-field v-model="fields.username" class="mb-2" label="Username" :rules="[rules.required, rules.min]"
                hint="Enter your password to access this website" prepend-inner-icon="mdi-email-outline"
                variant="outlined" :error-messages="err.username ? err.username[0] : ''" required></v-text-field>

              <v-text-field v-model="fields.password" label="Password" :rules="[rules.required]"
                :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'" :type="visible ? 'text' : 'password'"
                placeholder="Enter your password" prepend-inner-icon="mdi-lock-outline" variant="outlined"
                @click:append-inner="visible = !visible" :error-messages="err.password ? err.password[0] : ''"
                required></v-text-field>
              <v-row class="px-10 ma-2">
                  <v-btn block :loading="loading" class="ma-3" color="blue" size="large" type="submit">
                  Log In
                </v-btn>
              </v-row>
            </v-card-item>
          </v-card>
          </v-form>
        </v-col>
        <v-col cols="12" sm="10" md="5">
          <v-card class="ma-8 pa-10 d-flex justify-center align-center gradient-card" elevation="5" width="450"
            height="600" rounded="lg">
            <img :src="`/images/companylogo.png`" width="400  " class="mr-2" cover />
          </v-card>
        </v-col>
      </v-row>

      <v-dialog v-model="errorDialog" width="auto">
        <v-card color="secondary">
          <v-card-text>
            {{ errors }}
          </v-card-text>
          <v-card-actions>
            <v-btn color="red" class="text-white" block variant="flat" elevation="4"
              @click="errorDialog = false">Okay!</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-main>
    <ul class="circles">
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
    </ul>
  </v-app>
</template>
<script>
export default {
  data() {
    return {
      visible: false,
      rules: {
        required: value => !!value || 'Required.',
        min: v => v.length >= 4 || 'Minimum 4 characters',
      },
      loading: false,
      fields: {},
      errorDialog: false,
      err: {}

    };
  },
  methods: {
    submitForm() {
      this.loading =true
      axios.post('/login', this.fields).then(res => {
        this.loading = false
          window.location = '/dashboard';
      }).catch(error => {
        this.loading = false
        this.errors = error.response.data.errors.logs;
        this.err = error.response.data;
        if (error.response.data.errors.logs) {
          this.errorDialog = true;
        }

      })

    },
  },
  mounted() {

  },
}
</script>
<style scoped>

.items {
  z-index: 2;
}

.gradient-card {
  background: linear-gradient(to right, #0a7bf5, #74d6fc);
  /* You can adjust the gradient colors and direction as per your design */
  /* Additional styling for the card */
}
</style>
