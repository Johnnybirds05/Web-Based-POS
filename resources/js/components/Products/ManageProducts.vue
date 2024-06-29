<template>
  <v-data-table
    v-model="selected"
    show-select
    :headers="headers"
    item-value="product_id"
    :items="products"
    :search="search"
    :loading="loading"
    class="rounded-lg elevation-4 border-b-thin border-secondary striped-table"
  >
    <template v-slot:top>
      <v-toolbar color="light-blue" class="rounded-t-lg pa-3">
        <v-btn
          color="red"
          prepend-icon="mdi-trash-can"
          v-if="selected.length > 0"
          @click="deleteMultiple()"
          variant="elevated"
        >
          Multi-Delete
        </v-btn>
        <v-btn
          prepend-icon="mdi-plus-circle"
          @click="productForm = true"
          elevation="3"
          color="cyan-darken-2"
          variant="flat"
          class="ml-2"
        >
          <b>Add Products</b>
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
        <v-btn
          icon
          color="orange-darken-2"
          @click="filterForm = true"
          class="ma-1"
          size="large"
          variant="flat"
          elevation="4"
        >
          <v-tooltip activator="parent" location="top"> Filter </v-tooltip>
          <v-icon>mdi-form-dropdown</v-icon>
        </v-btn>
      </v-toolbar>
    </template>
    <template v-slot:item.current_quantity="{ item }">
      <v-chip
        :color="
          item.transactions.length > 0 && item.transactions[0].total_quantity > 0
            ? 'blue'
            : 'orange'
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
    <template v-slot:item.actions="{ item }">
      <v-btn
        icon
        color="red"
        @click="popDelete(item.product_id)"
        class="ma-1"
        size="x-small"
      >
        <v-tooltip activator="parent" location="top"> Delete Product </v-tooltip>
        <v-icon>mdi-trash-can</v-icon>
      </v-btn>
      <v-btn
        icon
        color="success"
        @click="editProduct(item.product_id)"
        class="ma-1"
        size="x-small"
      >
        <v-tooltip activator="parent" location="top"> Update Product </v-tooltip>
        <v-icon>mdi-pen</v-icon>
      </v-btn>
    </template>
  </v-data-table>

  <v-dialog v-model="productForm" max-width="800" persistent>
    <v-card
      prepend-icon="mdi-clipboard-list"
      title="Product Information"
      class="rounded-xl"
    >
      <v-sheet color="light-blue-lighten-2" class="pa-8">
        <v-row dense>
          <v-col cols="12" md="8" sm="12">
            <v-text-field
              label="Product name"
              variant="solo"
              v-model="fields.product_name"
              :rules="[rules.required]"
              :error-messages="err.product_name ? err.product_name[0] : ''"
              density="comfortable"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="4" sm="6">
            <v-select
              variant="solo"
              :items="['CHICKEN', 'BY PRODUCT', 'FROZEN', 'DRY GOODS']"
              label="Product Category"
              v-model="fields.category"
              :rules="[rules.required]"
              :error-messages="err.category ? err.category[0] : ''"
              density="comfortable"
            ></v-select>
          </v-col>
          <v-col cols="12" md="4" sm="6">
            <v-text-field
              label="Original Price"
              variant="solo"
              v-model="fields.original_price"
              :rules="[rules.required]"
              :error-messages="err.original_price ? err.original_price[0] : ''"
              density="comfortable"
              suffix="Php"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="4" sm="6">
            <v-text-field
              label="Retail Price"
              variant="solo"
              v-model="fields.retail_price"
              :rules="[rules.required]"
              :error-messages="err.retail_price ? err.retail_price[0] : ''"
              density="comfortable"
              suffix="Php"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="4" sm="6">
            <v-text-field
              label="Substitute Retail Price"
              variant="solo"
              v-model="fields.sub_retail_price"
              :rules="[rules.required]"
              :error-messages="err.sub_retail_price ? err.sub_retail_price[0] : ''"
              density="comfortable"
              suffix="Php"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="4" sm="6">
            <v-select
              variant="solo"
              :items="['kg', 'pc']"
              label="Value Type"
              v-model="fields.quantity_value"
              :rules="[rules.required]"
              :error-messages="err.quantity_value ? err.quantity_value[0] : ''"
              density="comfortable"
            ></v-select>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="12" sm="12">
            <v-textarea
              label="Description (Optional)"
              v-model="fields.description"
              name="input-7-1"
              variant="solo"
              auto-grow
            ></v-textarea>
          </v-col>
        </v-row>
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
          @click="saveProduct()"
          class="px-5"
          v-if="!fields.product_id"
        ></v-btn>
        <v-btn
          color="orange"
          text="update"
          variant="elevated"
          @click="updateProduct()"
          class="px-5"
          v-if="fields.product_id"
        ></v-btn>
        <v-spacer></v-spacer>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <v-dialog v-model="filterForm" max-width="900" persistent>
    <v-card prepend-icon="mdi-clipboard-list" title="Filter Product" class="rounded">
      <v-sheet color="secondary" class="pa-1">
        <v-row dense>
          <v-col cols="12" md="6" sm="12">
            <v-card title="Scope" class="bg-light-blue-darken-1">
              <v-card-text class="pa-2">
                <v-select
                  v-model="filter.scope"
                  :items="users"
                  variant="solo"
                  item-value="user_id"
                  item-title="username"
                  density="comfortable"
                  label="Scope Value"
                  :rules="[rules.required]"
                ></v-select>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="12" md="6" sm="12">
            <v-card title="Period" class="bg-light-blue-darken-1">
              <v-card-text class="pa-2">
                <v-row>
                  <v-col cols="12" md="12" sm="12">
                    <v-select
                      v-model="filter.range"
                      :items="['All', 'Range']"
                      variant="solo"
                      density="comfortable"
                      label="Product Name"
                      :rules="[rules.required]"
                    ></v-select>
                  </v-col>
                  <template v-if="filter.range == 'Range'">
                    <v-col cols="12" md="12" sm="12">
                      <v-text-field
                        type="date"
                        v-model="filter.to"
                        variant="solo"
                        color="primary"
                        label="Date"
                        required
                        :rules="[rules.required]"
                      >
                      </v-text-field>
                    </v-col>
                  </template>
                </v-row>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-sheet>

      <v-divider></v-divider>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          text="Close"
          variant="elevated"
          @click="filterForm = false"
          color="green-darken-2"
          class="px-5"
        ></v-btn>
        <v-btn
          color="blue"
          text="Go"
          variant="elevated"
          @click="filterProducts()"
          class="px-5"
        ></v-btn>
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
      <template v-slot:text> Are You sure you want to delete this product? </template>
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
</template>
<script>
import axios from "axios";
import Swal from "sweetalert2";
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
      selected: [],
      products: [],
      productForm: false,
      failedDialog: false,
      successDialog: false,
      confirmDelete: false,
      filterForm: false,
      filter: [],
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
          title: "Sub-retail Price",
          align: "center",
          key: "sub_retail_price",
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
      users: [],
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
    loadUsers() {
      axios.get("/fetch-users").then((res) => {
        // Start with the default 'All' user
        this.users = [
          {
            user_id: "0",
            username: "All",
          },
          ...res.data, // Spread the fetched users
        ];
      });
    },
    closeForm() {
      this.fields = {};
      this.productForm = false;
      this.err = [];
    },
    saveProduct() {
      axios
        .post("/products", this.fields)
        .then((res) => {
          this.closeForm();
          this.loadProducts();
          this.response = res.data;
          Swal.fire("Product Successfully Saved!", "", "success");
          this.err = [];
        })
        .catch((err) => {
          this.err = err.response.data.errors;
        });
    },
    popDelete(id) {
      this.id = id;
      this.confirmDelete = true;
    },
    deleteProduct() {
      axios.delete("/products/" + this.id).then((res) => {
        this.response = res.data.status;
        this.confirmDelete = false;
        this.loadProducts();
        Swal.fire({
          title: "Deleted!",
          text: this.response,
          icon: "success",
        });
      });
    },
    editProduct(id) {
      axios.get("/products/" + id + "/edit").then((res) => {
        this.fields = res.data;
        this.productForm = true;
        this.err = [];
      });
    },
    updateProduct() {
      axios
        .put("/products/" + this.fields.product_id, this.fields)
        .then((res) => {
          this.response = res.data;
          this.closeForm();
          Swal.fire("Product Successfully Updated!", "", "success");
          this.loadProducts();
        })
        .catch((err) => {
          this.err = err.response.data.errors;
        });
    },
    deleteMultiple() {
      Swal.fire({
        title: "Are you sure you want to delete these products?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post("/delete-products", this.selected).then((res) => {
            this.response = res.data.status;
            Swal.fire({
              title: "Deleted!",
              text: this.response,
              icon: "success",
            });
            this.loadProducts();
          });
        }
      });
    },
    filterProducts() {
      this.loading = true;
      this.filterForm = false;
      if (this.filter.scope == 0 && this.filter.range == "All") {
        this.loadProducts();
      } else if (this.filter.scope !== 0 && this.filter.range == "All") {
        axios.get("/product-user/" + this.filter.scope).then((res) => {
          this.products = res.data;
        });
      } else if (this.filter.scope !== 0 && this.filter.range == "Range") {
        axios
          .get("/product-user/" + this.filter.scope + "/" + this.filter.to)
          .then((res) => {
            this.products = res.data;
          });
      } else if (this.filter.scope == 0 && this.filter.range == "Range") {
        axios.get("/product-range/" + this.filter.to).then((res) => {
          this.products = res.data;
        });
      }
      this.loading = false;
    },
    initData() {
      this.loadProducts();
      this.loadUsers();
    },
  },
  mounted() {
    this.initData();
  },
};
</script>
<style scoped>
.text-bold {
  font-weight: bold;
}
.striped-table {
  font-size: 11px;
}
</style>
