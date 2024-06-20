<template>
  <v-row>
    <v-col cols="12" md="8">
      <v-sheet>
        <v-card
          class="mx-auto pa-3 bg-secondary"
          prepend-icon="mdi-view-list"
          width="100%"
        >
          <template v-slot:title>
            <span class="font-weight-black">Sell Products:</span>
          </template>
          <v-card-text>
            <v-sheet class="bg-secondary">
              <div v-for="(transaction, ix) in transactions" :key="ix" >
                <v-row dense>
                  <v-col cols="12" md="8" sm="8">
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
                  <v-col cols="12" md="3" sm="3" v-if="transaction.product_id">
                    <v-text-field
                      label="Total"
                      variant="solo"
                      v-model="transaction.quantity"
                      :rules="[rules.required]"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="1">
                    <v-btn icon color="red" @click="removeStock(ix)" class="ma-1">
                      <v-tooltip activator="parent" location="top">
                        Remove Row
                      </v-tooltip>
                      <v-icon>mdi-delete-variant</v-icon>
                    </v-btn>
                  </v-col>
                </v-row>
              </div>
              <v-btn
                text="Add Fields"
                variant="elevated"
                @click="addStocks()"
                color="orange-darken-2"
                prepend-icon="mdi-plus"
                class="px-5 mb-4"
              ></v-btn>
            </v-sheet>
          </v-card-text> </v-card
      ></v-sheet>
    </v-col>
    <v-col cols="12" md="4">
      <v-sheet>
        <v-card class="mx-auto pa-3 bg-primary" prepend-icon="mdi-cash" width="100%">
          <template v-slot:title>
            <span class="font-weight-black">Total Price:</span>
          </template>

          <v-card-text class="bg-surface-light pt-4 d-flex justify-center">
            <h2>{{ formattedTotalPrice }}</h2>&nbsp; php
          </v-card-text>
          <v-card-action>
            <v-btn block variant="elevated" color="light-blue" @click="saveTransactions()">
              Save Transaction
            </v-btn>
          </v-card-action>
        </v-card>
      </v-sheet>
    </v-col>
    <v-col cols="12" md="12">
      <v-data-table
        :headers="headers"
        :items="products"
        :search="search"
        :loading="loading"
        :header-props="{ color: 'primary' }"
        class="rounded-lg elevation-4 border-b-thin border-secondary"
        striped
      >
        <template v-slot:top>
          <v-toolbar color="cyan-lighten-2" class="rounded-t-lg pa-3">
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
        <template v-slot:item.total_quantity="{ item }">
          <v-chip
            :color="
              item.transactions.length > 0 && item.transactions[0].total_quantity > 0
                ? 'green'
                : 'red'
            "
            class="text-uppercase"
            size="small"
            label
            >{{
              item.transactions.length > 0 && item.transactions[0].total_quantity
                ? item.transactions[0].total_quantity
                : "0"
            }}</v-chip
          >
        </template>
        <template v-slot:item.current_quantity="{ item }">
          <v-chip
            :color="
              item.transactions.length > 0 && item.transactions[0].current_quantity > 0
                ? 'blue'
                : 'orange'
            "
            class="text-uppercase"
            size="small"
            label
            >{{
              item.transactions.length > 0 && item.transactions[0].current_quantity
                ? item.transactions[0].current_quantity
                : "0"
            }}</v-chip
          >
        </template>
        <template v-slot:item.error="{ item }">
          <v-chip
            :color="
              item.transactions.length > 0 && item.transactions[0].total_wasted > 0
                ? 'red'
                : 'green'
            "
            class="text-uppercase"
            size="small"
            label
            >{{
              item.transactions.length > 0 && item.transactions[0].total_wasted
                ? item.transactions[0].total_wasted
                : "0"
            }}</v-chip
          >
        </template>
      </v-data-table>
    </v-col>
  </v-row>
</template>
<script>
import axios from "axios";
import Swal from 'sweetalert2';
export default {
  data() {
    return {
      rules: {
        required: (value) => !!value || "Required.",
        min: (v) => v.length >= 4 || "Minimum 4 characters",
      },
      search: "",
      id: "",
      fields: {},
      err: [],
      loading: false,
      total: 0,
      selected: [],
      remark: 2,
      headers: [
        {
          title: "Name",
          align: "start",
          key: "product_name",
        },
        {
          title: "Original Price",
          align: "center",
          key: "original_price",
        },
        {
          title: "Retail Price",
          align: "center",
          key: "retail_price",
        },
        {
          title: "Overall Quantity",
          align: "center",
          key: "total_quantity",
        },
        {
          title: "Current Quantity",
          align: "center",
          key: "current_quantity",
        },
        {
          title: "Wasted",
          align: "center",
          key: "error",
        },
      ],
      users: [],
      products: [],
      transactions: [],
    };
  },
  methods: {
    loadProducts() {
      this.loading = true;
      axios.get("/products").then((res) => {
        this.products = res.data;
        this.loading = false;
      });
    },
    addStocks() {
      this.transactions.push({
        product_id: '',
        quantity: '',
      });
    },
    removeStock(index) {
      this.transactions.splice(index, 1);
    },
    saveTransactions(){

      if(this.transactions.length >0 && this.transactions[0].product_id !== ''){
        axios.post('/transactions/'+this.remark,this.transactions).then(
        res=>{
          this.loadProducts();
          this.response = res.data.status
          Swal.fire({
          icon: "success",
          title: "Transaction Success",
          text: this.response,
        });
          this.transactionForm = false;
          this.transactions = []
        }
      )
      }
      else{
        Swal.fire({
          icon: "error",
          title: "Transaction Failed",
          text: "Please add some product in the fields!",
        });
      }
    },
    initData() {
      this.loadProducts();
    },
  },
  computed:{
    totalPrice(){
      return this.transactions.reduce((total, transaction) => {
        const product = this.products.find(prod => prod.product_id === transaction.product_id);

        if (product) {
          return total + (transaction.quantity * product.retail_price);
        } else {
          console.warn(`Product with ID ${transaction.product_id} not found.`);
          return total;
        }
      }, 0)
    },
    formattedTotalPrice() {
      return this.totalPrice.toFixed(2);
    }
  },
  mounted() {
    this.initData();
  },
};
</script>
