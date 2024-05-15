<template>
  <v-data-table
    :headers="headers"
    item-value="change_quantity_id"
    :items="transactions"
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
      </v-toolbar>
    </template>
    <template v-slot:item.total_quantity="{ item }">
      <v-chip
        :color="item.total_quantity ? 'green' : 'red'"
        :text="item.total_quantity ? item.total_quantity : '0'"
        class="text-uppercase"
        size="small"
        label
      ></v-chip>
    </template>
    <template v-slot:item.current_quantity="{ item }">
      <v-chip
        :color="item.current_quantity ? 'blue' : 'orange'"
        :text="item.current_quantity ? item.current_quantity : '0'"
        class="text-uppercase"
        size="small"
        label
      ></v-chip>
    </template>
    <template v-slot:item.error="{ item }">
      <v-chip
        :color="item.current_quantity ? 'red' : 'green'"
        :text="item.error ? item.error : '0'"
        class="text-uppercase"
        size="small"
        label
      ></v-chip>
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
</template>
<script>
export default {
  data() {
    return {
      transactions: [],
      headers: [
        {
          title: "ID",
          align: "center",
          key: "change_quantity_id",
        },
        {
          title: "Transaction Time",
          align: "center",
          key: "original_price",
        },
        {
          title: "Actions",
          align: "center",
          key: "actions",
        },
      ],
      products: [
        {
          product_id: 0,
          quantity: 0,
          error: 0,
        },
      ],
      fields: {},
      loading: false,
      search: "",
      transactionForm: false,
    };
  },
};
</script>
