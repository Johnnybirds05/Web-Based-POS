<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="transactionsList"
      :search="search"
      :loading="loading"
      :header-props="{ color: 'primary' }"
      class="rounded-lg elevation-4 border-b-thin border-secondary striped-table"
      striped
    >
      <template v-slot:top>
        <v-toolbar color="light-blue" class="rounded-t-lg pa-3">
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
      <template v-slot:item.remarks="{ item }">
        <span>{{ remarks.find(remark => remark.id === item.remarks)?.remark || 'No Remark Found' }}</span>
      </template>
      <template v-slot:item.created_at="{ item }">
        <span>{{ convertDate(item.created_at) }}</span>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-btn icon color="red" @click="popDelete(item.transaction_id)" class="ma-1" size="x-small">
          <v-tooltip activator="parent" location="top"> Delete Product </v-tooltip>
          <v-icon>mdi-trash-can</v-icon>
        </v-btn>
        <v-btn icon color="success" @click="editTransactions(item.transaction_id)" class="ma-1" size="x-small">
          <v-tooltip activator="parent" location="top"> Update Product </v-tooltip>
          <v-icon>mdi-pen</v-icon>
        </v-btn>
      </template>
    </v-data-table>
    <v-dialog v-model="transactionForm" max-width="800" persistent>
      <v-card prepend-icon="mdi-playlist-edit" title="Stocks Information" class="rounded-xl">
        <v-sheet color="teal-lighten-2" class="pa-8">
          <v-row dense>
              <v-col cols="12" md="5" sm="5">
                <v-select
                  v-model="remark"
                  variant="solo"
                  :items="remarks"
                  item-value="id"
                  item-title="remark"
                  density="comfortable"
                  label="Transaction Type"
                ></v-select>
              </v-col>
            </v-row>
          <div v-for="(transaction, ix) in transactions" :key="ix">
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
                  <v-tooltip activator="parent" location="top"> Remove Row </v-tooltip>
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
            @click="saveTransactions()"
            class="px-5"
            v-if="id == 0"
          ></v-btn>
          <v-btn
            color="orange"
            text="Update"
            variant="elevated"
            @click="updateTransactions()"
            class="px-5"
            v-if="id== 1"
          ></v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="deleteForm" width="auto">
      <v-card max-width="500" append-icon="mdi-update" title="Delete">
        <template v-slot:text>
        Are You sure you want to delete this transaction?
        </template>
        <template v-slot:actions>
          <v-spacer></v-spacer>
          <v-btn
            class="ms-auto"
            text="Close"
            @click="deleteForm = false"
            variant="elevated"
            color="green-darken-1"
          ></v-btn>
          <v-btn
            class="ma-4"
            text="Confirm"
            variant="elevated"
            color="light-blue-darken-2"
            @click="deleteTransaction()"
          ></v-btn>
          <v-spacer></v-spacer>
        </template>
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
import axios from 'axios';
import Swal from 'sweetalert2';
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
        { title: "Remarks", align: "center", key: "remarks" },
        { title: "Time", align: "center", key: "created_at" },
        { title: "Actions", align: "center", key: "actions" },
      ],
      successDialog: false,
      deleteForm: false,
      products: [],
      remarks: [],
      loading: false,
      search: "",
      response: '',
      remark: 1,
      transactionForm: false,
      selectedProduct: null,
      rules: {
        required: value => !!value || "Required.",
        min: v => v.length >= 4 || "Minimum 4 characters",
      },
    };
  },
  methods: {
    loadTransactions() {
      this.loading = true;
      axios.get("/user-transactions").then(res => {
        this.transactionsList = res.data;
        this.loading = false;
      });
    },
    getAllProduct() {
      axios.get('/fetch-products')
        .then(res => {
          this.products = res.data;
        })
    },
    getAllRemarks() {
      axios.get('/fetch-remarks')
        .then(res => {
          this.remarks = res.data;
        })
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
      axios.post('/transactions/'+this.remark,this.transactions).then(
        res=>{
          this.loadTransactions();
          this.response = res.data.status        
          Swal.fire("Transaction Successfully Saved!", "", "success");
          this.transactionForm = false;
          this.transactions = []
        }
      )
    },
    editTransactions(id){
      axios.get('/transactions/'+id+'/edit').then(
       res=>{
        this.id = 1
        this.remark = res.data[0].remarks
        console.log(this.remark);
        this.transaction_id = id;
        this.transactions = res.data
        this.transactionForm = true
       }
      )
    },
    popDelete(id){
      this.transaction_id = id;
      this.deleteForm = true;
    },
    deleteTransaction(){
      axios.delete('/transactions/'+this.transaction_id).then(
        res=>{
          this.response = res.data.status
          Swal.fire({
                title: "Deleted!",
                text: this.response,
                icon: "success"
            });
          this.deleteForm = false;
          this.loadTransactions();
        }
      )
    },
    updateTransactions(){
      axios.post('/transactions/'+this.transaction_id+'/'+ this.remark, this.transactions).then(
        res => {
          this.loadTransactions();
          this.response = res.data
          Swal.fire("Transaction Successfully Updated!", "", "success");
          this.transactionForm = false;
          this.transactions = []
        }
      )
    },
    closeForm() {
      this.transactionForm = false;
      this.transactions = [];
      this.id = 0;
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
    },
    initData() {
      this.loadTransactions();
      this.getAllProduct();
      this.getAllRemarks();
    },
  },
  mounted() {
    this.initData();
  },
};
</script>

