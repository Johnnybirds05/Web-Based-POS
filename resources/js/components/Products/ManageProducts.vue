<template>
  <v-data-table v-model="selected" show-select :headers="headers" item-value="product_id" :items="products"
    :search="search" :loading="loading" :header-props="{ 'color': 'primary' }"
    class="rounded-lg elevation-4 border-b-thin border-secondary" striped>
    <template v-slot:top>
      <v-toolbar color="cyan-lighten-2" class="rounded-t-lg pa-3">
        <v-btn color="red" prepend-icon="mdi-trash-can" v-if="selected.length > 0" @click="deleteDialog = true"
          variant="elevated">
          Multi-Delete</v-btn>
        <v-btn prepend-icon="mdi-plus-circle" @click="productForm = true" elevation="3" color="teal-accent-4"
          variant="flat" class="ml-2">
          <b>Add Products</b>
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
  <v-dialog v-model="productForm" max-width="800" persistent>
    <v-card prepend-icon="mdi-clipboard-list" title="Product Information" class="rounded-xl">
      <v-sheet color="light-blue-lighten-2" class="pa-8">
        <v-row dense>
          <v-col cols="12" md="4" sm="6">
            <v-text-field label="Product name" variant="solo" v-model="fields.product_name" :rules="[rules.required]"
              :error-messages="err.product_name ? err.product_name[0] : ''" density="comfortable"></v-text-field>
          </v-col>
          <v-col cols="12" md="4" sm="6">
            <v-select variant="solo" :items="['Chicken Parts', 'Dry Goods']" label="Product Category"
              v-model="fields.category" :rules="[rules.required]" :error-messages="err.category ? err.category[0] : ''"
              density="comfortable"></v-select>
          </v-col>
          <v-col cols="12" md="4" sm="6">
            <v-text-field label="Original Price" variant="solo" v-model="fields.original_price"
              :rules="[rules.required]" :error-messages="err.original_price ? err.original_price[0] : ''"
              density="comfortable" suffix="Php"></v-text-field>
          </v-col>
          <v-col cols="12" md="4" sm="6">
            <v-text-field label="Retail Price" variant="solo" v-model="fields.retail_price" :rules="[rules.required]"
              :error-messages="err.retail_price ? err.retail_price[0] : ''" density="comfortable"
              suffix="Php"></v-text-field>
          </v-col>
          <v-col cols="12" md="4" sm="6">
            <v-select variant="solo" :items="['kg', 'pc']" label="Value Type" v-model="fields.quantity_value"
              :rules="[rules.required]" :error-messages="err.quantity_value ? err.quantity_value[0] : ''"
              density="comfortable"></v-select>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="12" sm="12">
            <v-textarea label="Description (Optional)" v-model="fields.description" name="input-7-1" variant="solo"
              auto-grow></v-textarea>
          </v-col>
        </v-row>
      </v-sheet>

      <v-divider></v-divider>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text="Close" variant="elevated" @click="closeForm()" color="green-darken-2" class="px-5"></v-btn>
        <v-btn color="blue" text="Save" variant="elevated" @click="saveProduct()" class="px-5"
          v-if="!fields.product_id"></v-btn>
        <v-btn color="orange" text="update" variant="elevated" @click="updateProduct()" class="px-5"
          v-if="fields.product_id"></v-btn>
        <v-spacer></v-spacer>
      </v-card-actions>
    </v-card>
  </v-dialog>
  <v-snackbar v-model="failedDialog" :timeout="2000">
    Action successfully saved!

    <template v-slot:actions>
      <v-btn color="red" variant="text" @click="failedDialog = false"> Close </v-btn>
    </template>
  </v-snackbar>
  <v-snackbar v-model="successDialog" :timeout="5000" color="blue">
    {{ response.status }}
    <template v-slot:actions>
      <v-btn color="light-blue" variant="flat" @click="successDialog = false">
        Close
      </v-btn>
    </template>
  </v-snackbar>
  <v-dialog v-model="confirmDelete" width="auto">
      <v-card max-width="400" append-icon="mdi-update" title="Confirm Delete">
        <template v-slot:text>
        Are You sure you want to delete this product?
        </template>
        <template v-slot:actions>
          <v-spacer></v-spacer>
          <v-btn
            class="ms-auto"
            text="Close"
            @click="confirmDelete = false"
            variant="elevated"
            color="green-darken-1"
          ></v-btn>
          <v-btn
            class="ma-4"
            text="Confirm"
            variant="elevated"
            color="light-blue-darken-2"
            @click="deleteProduct()"
          ></v-btn>
          <v-spacer></v-spacer>
        </template>
      </v-card>
    </v-dialog>
    <v-dialog v-model="deleteDialog" width="auto">
      <v-card max-width="400" append-icon="mdi-update" title="Multiple Delete">
        <template v-slot:text>
        Are You sure you want to delete all selected products?
        </template>
        <template v-slot:actions>
          <v-spacer></v-spacer>
          <v-btn
            class="ms-auto"
            text="Close"
            @click="deleteDialog = false"
            variant="elevated"
            color="green-darken-1"
          ></v-btn>
          <v-btn
            class="ma-4"
            text="Confirm"
            variant="elevated"
            color="light-blue-darken-2"
            @click="deleteMultiple()"
          ></v-btn>
          <v-spacer></v-spacer>
        </template>
      </v-card>
    </v-dialog>
</template>
<script>
export default {
  data() {
    return {
      rules: {
        required: (value) => !!value || "Required.",
        min: (v) => v.length >= 4 || "Minimum 4 characters",
      },
      search: "",
      id: '',
      fields: {},
      err: [],
      loading: false,
      selected: [],
      products: [],
      productForm: false,
      failedDialog: false,
      successDialog: false,
      deleteDialog: false,
      confirmDelete: false,
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
          title: "Total Quantity",
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
        {
          title: "Actions",
          align: "center",
          key: "actions",
        },
      ],
      products: [],
    };
  },
  methods: {
    loadProducts() {
      this.loading = true
      axios.get("/products").then((res) => {
        this.products = res.data;
        this.loading = false
      });
    },
    initData() {
      this.loadProducts();
    },
    closeForm() {
      this.fields = {};
      this.productForm = false;
      this.err = []
    },
    saveProduct() {
      axios.post("/products", this.fields).then((res) => {
        this.productForm = false;
        this.loadProducts();
        this.response = res.data
        this.successDialog = true
        this.err = []
      }).catch(
        err=>{
          this.err = err.response.data.errors
        }
      );
    },
    popDelete(id){
      this.id = id
      this.confirmDelete = true
    },
    deleteProduct(){
      axios.delete('/products/'+this.id).then(
        res=>{
          this.response = res.data
          this.confirmDelete = false
          this.loadProducts();
          this.successDialog = true
        }
      )
    },
    editProduct(id){
      axios.get('/products/'+id+'/edit').then(
       res=>{
        this.fields = res.data
        this.productForm = true
        this.err = []
       } 
      )
    },
    updateProduct(){
      axios.put('/products/'+this.fields.product_id,this.fields).then(
        res=>{
          this.response = res.data
          this.productForm = false
          this.successDialog = true
          this.fields = {}
          this.loadProducts()
        }
      ).catch(
        err=>{
          this.err = err.response.data.errors
        }
      );
    },
    deleteMultiple(){
      axios.post('/delete-products',this.selected).then(
        res=>{
          this.response = res.data.status
          this.successDialog = true
          this.deleteDialog = false
          this.loadProducts()
        }
      )
    }
  },
  mounted() {
    this.initData();
  },
};
</script>
<style>
.text-bold {
  font-weight: bold;
}
</style>
