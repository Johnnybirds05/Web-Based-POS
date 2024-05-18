<template>
  <v-container>
    <v-data-table :headers="headers" item-value="change_quantity_id" :items="data" :search="search"
      :loading="loading" :header-props="{ color: 'primary' }"
      class="rounded-lg elevation-4 border-b-thin border-secondary" striped>
      <template v-slot:top>
        <v-toolbar color="cyan-lighten-2" class="rounded-t-lg pa-3">
          <v-btn prepend-icon="mdi-plus-circle" @click="transactionForm = true" elevation="3" color="cyan-darken-2"
            variant="flat" class="ml-2">
            <b>Add Stocks</b>
          </v-btn>
          <v-spacer></v-spacer>
          <v-text-field v-model="search" clearable density="comfortable" hide-details placeholder="Search"
            prepend-inner-icon="mdi-magnify" style="max-width: 400px" variant="solo" class="mr-2"></v-text-field>
        </v-toolbar>
      </template>
      <template v-slot:item.total_quantity="{ item }">
      <v-chip :color="item.total_quantity ? 'green' : 'red'" :text="item.total_quantity ? item.total_quantity : '0'"
        class="text-uppercase" size="small" label></v-chip>
    </template>
    <template v-slot:item.current_quantity="{ item }">
      <v-chip :color="item.current_quantity ? 'blue' : 'orange'"
        :text="item.current_quantity ? item.current_quantity : '0'" class="text-uppercase" size="small" label></v-chip>
    </template>
    <template v-slot:item.error="{ item }">
      <v-chip :color="item.current_quantity ? 'red' : 'green'" :text="item.error ? item.error : '0'"
        class="text-uppercase" size="small" label></v-chip>
    </template>
    <template v-slot:item.actions="{ item }">
      <v-btn icon color="red" @click="popDelete(item.product_id)" class="ma-1">
        <v-tooltip activator="parent" location="top"> Delete Product </v-tooltip>
        <v-icon>mdi-trash-can</v-icon>
      </v-btn>
      <v-btn icon color="success" @click="editProduct(item.product_id)" class="ma-1">
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
              <v-col cols="12" md="6" sm="6">
              <v-select
                variant="solo"
                :items="products"
                item-title="product_name"
                item-value="product_id"
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
                >Fine:{{ transaction.quantity - transaction.error }}</v-text-field>
              </v-col>
            </v-row>
          </div>
          <v-btn text="Add Fields" variant="elevated" @click="addStocks()" color="orange-darken-2" class="px-5"></v-btn>
        </v-sheet>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text="Close" variant="elevated" @click="closeForm()" color="green-darken-2" class="px-5"></v-btn>
          <v-btn color="blue" text="Save" variant="elevated" @click="saveProduct()" class="px-5" v-if="!fields.product_id"></v-btn>
          <v-btn color="orange" text="Update" variant="elevated" @click="updateProduct()" class="px-5" v-if="fields.product_id"></v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      transactions: [],
      err: [],
      data: [],
      headers: [
        { title: "ID", align: "center", key: "change_quantity_id" },
        { title: "Transaction Time", align: "center", key: "created_at" },
        { title: "Actions", align: "center", key: "actions" },
      ],
      products: [],
      fields: {},
      loading: false,
      search: "",
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
      axios.get("/quantity").then(res => {
        this.transactions = res.data;
      });
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
    closeForm() {
      this.transactionForm = false;
      this.transactions = [];
    },
    initData() {
      this.loadQuantity();
      this.getAllProduct();
    },
  },
  mounted() {
    this.initData();
  },
};
</script>

