<template>
  <v-data-iterator :items="users" items-per-page="6" :search="search">
    <template v-slot:header>
      <v-toolbar class="pa-3 rounded-xl elevation-1" color="light-blue-lighten-3">
        <v-btn prepend-icon="mdi-plus-circle" @click="userForm = true" elevation="3" color="light-blue" variant="flat">
          <b>Add User</b>
        </v-btn>
        <v-spacer></v-spacer>
        <v-text-field v-model="search" clearable density="comfortable" hide-details placeholder="Search"
          prepend-inner-icon="mdi-magnify" style="max-width: 400px;" variant="solo" class="rounded-xl"></v-text-field>
      </v-toolbar>
    </template>

    <template v-slot:default="{ items }">
      <v-row class="pa-4">
        <v-col v-for="(item, i) in items" :key="i" cols="12" sm="6" md="4" lg="4">
          <v-sheet border class=" rounded-xl border-sm border-surface pa-10 ma-3" elevation="4">
          <v-row class="d-flex justify-center my-4">
            <v-avatar :image="`/storage/avatars/`+item.raw.img" size="200" class="border-lg" color="light-blue"><v-icon
                icon="mdi-account-circle"></v-icon>
            </v-avatar>
          </v-row>
            <v-list-item :title="item.raw.lname" density="comfortable">
              <template v-slot:title>
                <strong>
                  <h4>{{ item.raw.last_name }}, {{ item.raw.first_name }} {{ item.raw.middle_initial }}</h4>
                </strong>
              </template>
            </v-list-item>

            <v-table density="compact" class="text-caption">
              <tbody>

                <tr align="right">
                  <th>Contact No:</th>

                  <td>{{ item.raw.contact}}</td>
                </tr>

                <tr align="right">
                  <th>Role:</th>
                  <td>{{ item.raw.role }}</td>
                </tr>

                <tr align="right">
                  <th>Username:</th>

                  <td>{{ item.raw.username }}</td>
                </tr>
                <tr align="right">
                  <th>Actions:</th>

                  <v-btn icon="mdi-trash-can" variant="text" color="red" @click="popDelete(item.raw.user_id)">
                  </v-btn> <v-btn icon="mdi-pen" variant="text" color="success"
                    @click="editDriver(item.raw.user_id)"></v-btn>
                </tr>
              </tbody>
            </v-table>
          </v-sheet>
        </v-col>
      </v-row>
    </template>
    <template v-slot:footer="{ page, pageCount, prevPage, nextPage }" v-if="users.length > 0">

      <div class="d-flex align-center justify-center pa-4">
        <v-btn :disabled="page === 1" icon="mdi-arrow-left" density="comfortable" variant="tonal" rounded
          @click="prevPage"></v-btn>

        <div class="text-caption">
          Page {{ page }} of {{ pageCount }}
        </div>

        <v-btn :disabled="page >= pageCount" icon="mdi-arrow-right" density="comfortable" variant="tonal" rounded
          @click="nextPage"></v-btn>
      </div>
    </template>
  </v-data-iterator>
       <v-sheet v-if="users.length < 0" >
          No Data Found!
      </v-sheet>
  <v-dialog v-model="userForm" max-width="800" persistent>
    <v-card prepend-icon="mdi-account" title="User Profile" theme="light">
      <v-sheet color="light-blue-lighten-4" class="pa-5">
        <v-row class="d-flex justify-center">
          <v-col cols="12" md="4" class="ma-4">
            <v-avatar :image="userImagePath" size="200" class="border-xl" color="blue"><v-icon
                icon="mdi-account-circle" v-if="!userImage"></v-icon>
            </v-avatar>
          </v-col>
        </v-row>
        <v-row dense>
          <v-col cols="12" md="6" sm="6">
            <v-file-input label="Image" variant="solo" v-model="fields.img" append-icon="mdi-camera" accept="image/*"
              v-on:change="onChange" theme="dark"  :error-messages="err.image ? err.image[0] : ''"></v-file-input>
          </v-col>
          <v-col cols="12" md="3">
            <v-select
            variant="solo" 
            :items="['ADMIN', 'USER']"
              label="Role"
              v-model="fields.role"
              :rules="[rules.required]"  :error-messages="err.role ? err.role[0] : ''"
            ></v-select>
          </v-col>
        </v-row>
        <v-row dense>
          <v-col cols="12" md="4" sm="6">
            <v-text-field label="First name" variant="solo" v-model="fields.fname" :rules="[rules.required]"  :error-messages="err.fname ? err.fname[0] : ''"></v-text-field>
          </v-col>

          <v-col cols="12" md="4" sm="6">
            <v-text-field label="Middle name" variant="solo" v-model="fields.mname"></v-text-field>
          </v-col>

          <v-col cols="12" md="4" sm="6">
            <v-text-field label="Last name" variant="solo" v-model="fields.lname" :rules="[rules.required]"  :error-messages="err.lname ? err.lname[0] : ''"></v-text-field>
          </v-col>

          <v-col cols="12" md="4" sm="6">
            <v-text-field label="Contact No." variant="solo" v-model="fields.contact" :rules="[rules.required]"  :error-messages="err.contact ? err.contact[0] : ''"></v-text-field>
          </v-col>
          <v-col cols="12" md="4" sm="6">
            <v-text-field label="Username" prepend-inner-icon="mdi-account" variant="solo" v-model="fields.username" :rules="[rules.required]"  :error-messages="err.username ? err.username[0] : ''"></v-text-field>
          </v-col>
          <v-col cols="12" md="4" sm="6">
            <v-text-field label="Password" variant="solo" v-model="fields.password" :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'" :rules="[rules.required,rules.min]"
              :type="visible ? 'text' : 'password'" prepend-inner-icon="mdi-lock-outline"
              @click:append-inner="visible = !visible"
              :error-messages="err.password ? err.password[0] : ''"></v-text-field>
          </v-col>
        </v-row>
      </v-sheet>

      <v-divider></v-divider>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text="Close" variant="elevated" @click="userForm = false" color="green-darken-2" class="px-5"></v-btn>
        <v-btn color="blue" text="Save" variant="elevated" @click="saveProfile()"  class="px-5"></v-btn>
        <v-spacer></v-spacer>
      </v-card-actions>
    </v-card>
  </v-dialog>
  <v-snackbar
      v-model="successDialog"
      :timeout="2000"
    >
      {{ response }}

      <template v-slot:actions>
        <v-btn
          color="light-blue"
          variant="text"
          @click="successDialog = false"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>

</template>
<script>
export default {
  data() {
    return {
      rules: {
        required: value => !!value || 'Required.',
        min: v => v.length >= 4 || 'Minimum 4 characters',
      },
      search: '',
      users: [],
      userForm: false,
      userImage: null,
      userImagePath: null,
      visible: false,
      err: [],
      fields: [],
      response: '',
      successDialog:false,
      failedDialog:false,
    }
  },
  methods: {
    onChange(e) {
      this.userImage = e.target.files[0];
      this.previewImage()
    },
    previewImage() {
      const file = this.userImage;
      if (!file) return;

      if (!file.type.match("image.*")) {
        alert("Please select an image file");
        return;
      }

      const reader = new FileReader();
      reader.onload = e => {
        this.userImagePath = e.target.result;
      };
      reader.readAsDataURL(file);
    },
    saveProfile(){
      let formData = new FormData();
      formData.append('image',this.userImage ? this.userImage: '');
      formData.append('username',this.fields.username? this.fields.username: '');
      formData.append('role',this.fields.role ? this.fields.role : '');
      formData.append('first_name',this.fields.fname ? this.fields.fname : '');
      formData.append('middle_name',this.fields.mname ? this.fields.mname : '');
      formData.append('last_name',this.fields.lname ? this.fields.lname : '');
      formData.append('contact',this.fields.contact ? this.fields.contact : '');
      formData.append('password',this.fields.password ? this.fields.password : '' );

      axios.post('/users',formData).then(
        res=>{
          this.userForm = false;
          this.successDialog = true;
        }
      ).catch(
        err=>{
          this.err = err.response.data.errors
        }
      )

    },
    loadProfiles(){
      axios.get('/users').then(
        res=>{
          this.users = res.data
        }
      )
    },
    initData(){
      this.loadProfiles();
    }
  },
  mounted() {
    this.initData();
  },
}
</script>