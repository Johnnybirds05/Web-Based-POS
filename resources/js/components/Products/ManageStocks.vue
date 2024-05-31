<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="transactionsList"
      :search="search"
      :loading="loading"
      :header-props="{ color: 'primary' }"
      class="rounded-lg elevation-4 border-b-thin border-secondary"
      striped
    >
      <template v-slot:top>
        <v-toolbar color="cyan-lighten-2" class="rounded-t-lg pa-3">
          <v-btn
            prepend-icon="mdi-plus-circle"
            @click="transactionForm = true"
            elevation="3"
            color="cyan-darken-2"
            variant="flat"
            class="ml-2"
          >
            <b>Add Stocks</b>
          </v-btn>
          <v-spacer></v-spacer>
          <v-text-field
            v-model="search"
            clearable
            density="comfortable"
            hide-details
            placeholder="Search"
            prepend-inner-icon="mdi-magnify"
            style="max-width: 400px"
            variant="solo"
            class="mr-2"
          ></v-text-field>
        </v-toolbar>
      </template>
      <template v-slot:item.created_at="{ item }">
        <span>{{ convertDate(item.created_at) }}</span>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-btn icon color="red" @click="popDelete(item.change_quantity_id)" class="ma-1">
          <v-tooltip activator="parent" location="top"> Delete Product </v-tooltip>
          <v-icon>mdi-trash-can</v-icon>
        </v-btn>
        <v-btn icon color="success" @click="editQuantity(item.change_quantity_id)" class="ma-1">
          <v-tooltip activator="parent" location="top"> Update Product </v-tooltip>
          <v-icon>mdi-pen</v-icon>
        </v-btn>
      </template>
    </v-data-table>
    <v-dialog v-model="transactionForm" max-width="800" persistent>
      <v-card prepend-icon="mdi-playlist-edit" title="Stocks Information" class="rounded-xl">
        <v-sheet color="teal-lighten-2" class="pa-8">
          <div v-for="(transaction, ix) in transactions" :key="ix">
            <v-row dense>
              <v-col cols="12" md="5" sm="5">
                <v-select
                  v-model="transaction.product_id"
                  variant="solo"
                  :items="products"
                  item-value="product_id"
                  item-title="product_name"
                  density="comfortable"
                  label="Product Name"
                ></v-select>
              </v-col>
              <v-col cols="12" md="2" sm="2">
                <v-text-field
                  label="Total"
                  variant="solo"
                  v-model="transaction.quantity"
                  :rules="[rules.required]"
                  density="comfortable"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="2" sm="2">
                <v-text-field
                  label="wasted"
                  variant="solo"
                  v-model="transaction.error"
                  :rules="[rules.required]"
                  density="comfortable"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="2" sm="2">
                <v-text-field
                  variant="solo"
                  density="comfortable"
                  disabled
                  v-model="transaction.fine"
                >
                  Fine: {{ transaction.quantity - transaction.error }}
                </v-text-field>
              </v-col>
              <v-col cols="12" md="1">
                <v-btn icon color="red" @click="removeStock(ix)" class="ma-1">
                  <v-tooltip activator="parent" location="top"> Remove Row </v-tooltip>
                  <v-icon>mdi-basket-remove</v-icon>
                </v-btn>
              </v-col>
            </v-row>
          </div>
          <v-btn
            text="Add Fields"
            variant="elevated"
            @click="addStocks()"
            color="orange-darken-2"
            class="px-5 mb-4"
          ></v-btn>
        </v-sheet>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            text="Close"
            variant="elevated"
            @click="closeForm()"
            color="green-darken-2"
            class="px-5"
          ></v-btn>
          <v-btn
            color="blue"
            text="Save"
            variant="elevated"
            @click="saveStocks()"
            class="px-5"
            v-if="id = 0"
          ></v-btn>
          <v-btn
            color="orange"
            text="Update"
            variant="elevated"
            @click="updateQuantity()"
            class="px-5"
            v-if="id= 1"
          ></v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-snackbar v-model="successDialog" :timeout="5000" color="blue">
      {{ response.status }}
      <template v-slot:actions>
        <v-btn color="light-blue" variant="flat" @click="successDialog = false"> Close </v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>
<script>
export default {
  data() {
    return {
      id:0,
      transactions: [],
      transactionsList: [],
      transaction_id: '',
      err: [],
      data: [],
      headers: [
        { title: "ID", align: "center", key: "change_quantity_id" },
        { title: "Time", align: "center", key: "created_at" },
        { title: "Actions", align: "center", key: "actions" },
      ],
      successDialog: false,
      products: [],
      fields: {},
      loading: false,
      search: "",
      response: '',
      remarks: '',
      transactionForm: false,
      selectedProduct: null,
      rules: {
        required: value => !!value || "Required.",
        min: v => v.length >= 4 || "Minimum 4 characters",
      },
    };
  },
  methods: {
    loadQuantity() {
      this.loading = true;
      axios.get("/quantity").then(res => {
        this.transactionsList = res.data;
        this.loading = false;
      });
    },
    editQuantity(id){
      axios.get('/quantity/'+id+'/edit').then(
       res=>{
        this.id = 1
        this.transaction_id = id;
        this.transactions = res.data
        this.transactionForm = true
       }
      )
    },
    getAllProduct() {
      axios.get('/fetch-products')
        .then(res => {
          this.products = res.data;
        })
        .catch(error => {
          console.error('Error fetching products:', error);
        });
    },
    addStocks() {
      this.transactions.push({
        product_id: '',
        quantity: '',
        error: '',
      });
    },
    removeStock(index) {
      this.transactions.splice(index, 1);
    },
    saveStocks(){
      axios.post('/quantity',this.transactions).then(
        res=>{
          this.loadQuantity();
          this.response = res.data
          this.successDialog = true;
          this.transactionForm = false;
          this.transactions = []
        }
      )
    },
    updateQuantity(){
      axios.put('/quantity/'+this.transaction_id, this.transactions).then(
        res => {

        }
      )
    },
    closeForm() {
      this.transactionForm = false;
      this.remarks = '';
      this.transactions = [];
      this.id = 0;
    },
    initData() {
      this.loadQuantity();
      this.getAllProduct();
    },
    convertDate(time) {
      if (!time) return '';
      const date = new Date(time);
      const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
      };
      const humanReadableDate = date.toLocaleString('en-US', options);
      return humanReadableDate;
    }
  },
  mounted() {
    this.initData();
  },
};
</script>

