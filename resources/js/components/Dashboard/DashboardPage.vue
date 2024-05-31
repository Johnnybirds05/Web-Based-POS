<template>
  <v-app class="content">
    <v-layout>
      <v-navigation-drawer
        v-model="drawer"
        :rail="rail"
        permanent
        @click="rail = false"
        color="white"
        class="navigations"
        elevation="8"
        min-width="60"
      >
        <v-list-item
          :prepend-avatar="`/storage/avatars/${user.img}`"
          :title="`${user.first_name} ${user.last_name}`"
          nav
          class="mt-5"
        >
          <template v-slot:append>
            <v-btn icon @click.stop="rail = !rail" variant="text">
              <v-icon>mdi-chevron-left</v-icon>
            </v-btn>
          </template>
        </v-list-item>
        <v-divider></v-divider>

        <v-list density="comfortable" nav>
          <v-list-item
            prepend-icon="mdi-home-city"
            title="Home"
            @click="tab = 'ONE'"
          ></v-list-item>
          <v-list-item
            prepend-icon="mdi-account-group"
            title="Manage Accounts"
            @click="tab = 'TWO'"
          ></v-list-item>
          <v-list-item
            prepend-icon="mdi-food-drumstick"
            title="Manage Products"
            @click="tab = 'THREE'"
          ></v-list-item>
          <v-list-item
            prepend-icon="mdi-truck-delivery"
            title="Manage Stocks"
            @click="tab = 'FOUR'"
          ></v-list-item>
          <v-list-item
            prepend-icon="mdi-logout"
            title="Logout"
            @click="logout = true"
          ></v-list-item>
        </v-list>
      </v-navigation-drawer>

      <v-app-bar :order="order" color="cyan-lighten-2" height="90">
        <v-container class="mx-auto d-flex align-center">
          <template v-slot:image>
            <v-img gradient="to top right, rgba(1,1,1,.9), rgba(128,208,199,.9)"></v-img>
          </template>
          <v-avatar class="me-4" color="cyan-lighten-2" size="80">
            <v-img :src="`/images/companylogo.png`" width="80" contain></v-img>
          </v-avatar>
          <h2 class="text-white">JLU Enterprise Inc.</h2>
          <v-spacer></v-spacer>
        </v-container>
      </v-app-bar>
      <v-main>
        <v-window v-model="tab">
          <v-window-item value="ONE">
            <v-card class="pa-15" color="transparent" min-height="90vh">
              {{ user }}
            </v-card>
          </v-window-item>

          <v-window-item value="TWO">
            <v-card color="transparent" class="pa-5" min-height="90vh">
              <manage-user ref="ManageUser"></manage-user>
            </v-card>
          </v-window-item>
          <v-window-item value="THREE">
            <v-card color="transparent" class="pa-5" min-height="90vh">
              <manage-products ref="ManageProducts"></manage-products>
            </v-card>
          </v-window-item>
          <v-window-item value="FOUR">
            <v-card color="transparent" class="pa-5" min-height="90vh">
              <manage-stocks ref="ManageStocks"></manage-stocks>
            </v-card>
          </v-window-item>
        </v-window>
      </v-main>
    </v-layout>

    <v-dialog v-model="logout" width="auto">
      <v-card max-width="400" append-icon="mdi-update" title="Confirm Logout">
        <template v-slot:actions>
          <v-spacer></v-spacer>
          <v-btn
            class="ms-auto"
            text="Close"
            @click="logout = false"
            variant="flat"
            color="orange"
          ></v-btn>
          <v-btn
            class="ma-4"
            text="Confirm"
            href="/logout"
            variant="flat"
            color="light-blue-darken-2"
          ></v-btn>
          <v-spacer></v-spacer>
        </template>
      </v-card>
    </v-dialog>
  </v-app>
</template>
<script>
export default {
  data() {
    return {
      drawer: true,
      rail: false,
      order: -1,
      tab: null,
      user: {},
      logout: false,
    };
  },
  watch: {
        tab(newTab, oldTab) {
            this.initializeComponent(newTab);
        }
    },
  methods: {
    fetchCurrentUser() {
      axios.get("/user").then((res) => {
        this.user = res.data;
      });
    },
    initializeComponent(tab) {
            switch (tab) {
                case 'ONE':
                this.$nextTick(() => {
                
                }); 
                break;
                case 'TWO':
                this.$nextTick(() => {
                this.$refs.ManageUsers.initData();
                }); 
                break;
                case 'THREE':
                this.$nextTick(() => {
                this.$refs.ManageProducts.initData();
                });
                break;
                case 'FOUR':
                this.$nextTick(() => {
                this.$refs.ManageStocks.initData();
                }); 
                break;
                default:
                break;
            }
            },
    initData() {
      this.fetchCurrentUser();
    },
  },
  mounted() {
    this.initData();
  },
};
</script>
<style scoped>
.gradient-card {
  background: linear-gradient(to right, #5fdfff, #0d92ff);
  /* You can adjust the gradient colors and direction as per your design */
  /* Additional styling for the card */
}

.content {
  background-color: rgb(223, 247, 255);
}

.navigations {
  opacity: 0.8;
}
</style>
