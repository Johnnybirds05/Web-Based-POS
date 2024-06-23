<template>
  <v-sheet class="my-4 rounded-lg">
    <v-row>
      <v-col cols="12" md="8" sm="12" >
        <div class="w-100 h-100 d-flex justify-center align-items-center">
          <v-sheet class="w-100 h-100 pa-2">
            <v-table
              class="border-black border-lg striped-table w-100"
              density="comfortable"
              :loading="loading"
            >
              <thead>
                <tr>
                  <th class="text-left bg-light-blue-darken-3">
                    <b>CATEGORY</b>
                  </th>
                  <th class="text-center bg-cyan-darken-2">
                    <b>TOTAL KG SOLD</b>
                  </th>
                  <th class="text-center bg-cyan-darken-2">
                    <b>TOTAL DECLARED SALES</b>
                  </th>
                  <th class="text-center bg-cyan-darken-2">
                    <b>DISCREPANCY</b>
                  </th>
                  <th class="text-center bg-yellow-darken-1">
                    <b>TOTAL</b>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in totals" :key="item.name">
                  <td class="bg-light-blue-darken-1">
                    <b>{{ item.category }}</b>
                  </td>
                  <td class="text-center">{{ item.total_sales.toFixed(2)  }}</td>
                  <td class="text-center">{{ item.total_prices.toFixed(2)  }}</td>
                  <td class="text-center">{{ item.descrepancy.toFixed(2) }}</td>
                  <td class="text-center">{{ (item.descrepancy + item.total_prices).toFixed(2)  }}</td>
                </tr>
                <tr>
                  <th colspan="5" class="text-center bg-light-blue">
                    <v-btn
                      variant="elevated"
                      color="green-darken-1"
                      prepend-icon="mdi-content-save-check"
                      @click="downloadExcel"
                    >
                      download excel
                    </v-btn>
                  </th>
                </tr>
              </tbody>
            </v-table>
          </v-sheet>
        </div>
      </v-col>
      <v-col cols="12" md="4" sm="12">
        <v-card class="rounded">
          <v-sheet>
            <v-row dense>
              <v-col cols="12" md="12" sm="12">
                <v-card title="Period" class="bg-light-blue-darken-4">
                  <v-card-text>
                    <v-row>
                      <v-col cols="12" md="12" sm="12">
                        <v-select
                          v-model="filter.range"
                          :items="['All', 'Select']"
                          variant="solo"
                          density="compact"
                          label="Range Type"
                          :rules="[rules.required]"
                          @update:modelValue="filterProducts()"
                        ></v-select>
                      </v-col>
                      <template v-if="filter.range == 'Select'">
                        <v-col cols="12" md="12" sm="12">
                          <v-text-field
                            type="date"
                            v-model="filter.to"
                            variant="solo"
                            color="primary"
                            label="Date"
                            required
                            :rules="[rules.required]"
                            @update:modelValue="filterProducts()"
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
        </v-card>
      </v-col>
    </v-row>
  </v-sheet>
  <v-sheet class="my-5">
    <v-data-table
      :group-by="groupBy"
      :headers="headers"
      :items="products"
      :search="search"
      :loading="loading"
      class="elevation-4 border-b-thin border-black striped-table"
      density="compact"
      fixed-header
    >
      <template v-slot:headers="{ columns }">
        <tr>
          <template v-for="column in columns" :key="column.key">
            <template v-if="column.key == 'product_name'">
              <th class="bg-orange">
                <b>{{ column.title.toUpperCase() }}</b>
              </th>
            </template>
            <template
              v-else-if="
                column.key == 'opening' ||
                column.key == 'delivery' ||
                column.key == 'total' ||
                column.key == 'closing' ||
                column.key == 'damage'
              "
            >
              <th class="bg-blue-grey-darken-1">
                <b>{{ column.title.toUpperCase() }}</b>
              </th>
            </template>
            <template
              v-else-if="
                column.key == 'sbs' || column.key == 'kg_des' || column.key == 'des_peso'
              "
            >
              <th class="bg-red-lighten-2">
                <b>{{ column.title.toUpperCase() }}</b>
              </th>
            </template>
            <template
              v-else-if="
                column.key == 'retail_price' ||
                column.key == 'total_kg' ||
                column.key == 'sales'
              "
            >
              <th class="bg-green-darken-1">
                <b>{{ column.title.toUpperCase() }}</b>
              </th>
            </template>
            <template v-else>
              <th class="bg-black">
                <b>{{ column.title.toUpperCase() }}</b>
              </th>
            </template>
          </template>
        </tr>
      </template>
      <template v-slot:group-header="{ item, columns, toggleGroup, isGroupOpen }">
        <tr>
          <td :colspan="columns.length" class="bg-light-blue">
            <VBtn
              :icon="isGroupOpen(item) ? '$expand' : '$next'"
              size="small"
              variant="text"
              @click="toggleGroup(item)"
            ></VBtn>
            {{ item.value }}
          </td>
        </tr>
      </template>
      <template v-slot:top>
        <v-toolbar color="light-blue-darken-2" class="pa-1">
          <v-spacer></v-spacer>
          <h2 class="text-white mx-10">List of Products</h2>
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
      <template v-slot:item.product_name="{ item }">
        <b>{{ item.product_name }}</b>
      </template>
      <template v-slot:item.retail_price="{ item }">
        {{
          item.retail_price.toFixed(2)
        }}
      </template>
      <template v-slot:item.sales="{ item }">
        {{
          item.transactions.length > 0 &&
          item.transactions[0].total_sales * item.retail_price
            ? (item.transactions[0].total_sales * item.retail_price).toFixed(2)
            : "0.00"
        }}
      </template>
      <template v-slot:item.total_kg="{ item }">
        {{
          item.transactions.length > 0 && item.transactions[0].total_sales
            ? item.transactions[0].total_sales.toFixed(2)
            : "0.00"
        }}
      </template>
      <template v-slot:item.opening="{ item }">
        {{
          item.transactions.length > 0 && item.transactions[0].opening
            ? item.transactions[0].opening.toFixed(2)
            : "0.00"
        }}
      </template>
      <template v-slot:item.delivery="{ item }">
        {{
          item.transactions.length > 0 && item.transactions[0].delivery
            ? item.transactions[0].delivery.toFixed(2)
            : "0.00"
        }}
      </template>
      <template v-slot:item.total="{ item }">
        {{
          item.transactions.length > 0 && item.transactions[0].total_quantity
            ? item.transactions[0].total_quantity.toFixed(2)
            : "0.00"
        }}
      </template>
      <template v-slot:item.closing="{ item }">
        {{
          item.transactions.length > 0 && item.transactions[0].closing
            ? item.transactions[0].closing.toFixed(2)
            : "0.00"
        }}
      </template>
      <template v-slot:item.damage="{ item }">
        {{
          item.transactions.length > 0 && item.transactions[0].total_wasted
            ? item.transactions[0].total_wasted.toFixed(2)
            : "0.00"
        }}
      </template>
      <template v-slot:item.sbs="{ item }">
        {{
          item.transactions.length > 0 &&
          item.transactions[0].total_quantity - item.transactions[0].total_sales
            ? (item.transactions[0].total_quantity - item.transactions[0].total_sales).toFixed(2)
            : "0.00"
        }}
      </template>
      <template v-slot:item.kg_des="{ item }">
        {{
          item.transactions.length > 0 && item.transactions[0].closing !== 0
            ? (item.transactions[0].total_quantity -
              (item.transactions[0].closing + item.transactions[0].total_sales)).toFixed(2)
            : "0.00"
        }}
      </template>
      <template v-slot:item.des_peso="{ item }">
        {{
          item.transactions.length > 0 && item.transactions[0].closing !== 0
            ? ((item.transactions[0].total_quantity -
                (item.transactions[0].closing + item.transactions[0].total_sales)) *
              item.retail_price).toFixed(2)
            : "0.00"
        }}
      </template>
      <template v-slot:item.actions="{ item }">
        <v-btn
          icon
          color="cyan-darken-4"
          @click="editProduct(item.product_id)"
          class="ma-1"
          size="x-small"
        >
          <v-tooltip activator="parent" location="top">
            View Product transactions
          </v-tooltip>
          <v-icon>mdi-eye</v-icon>
        </v-btn>
      </template>
    </v-data-table>
  </v-sheet>
</template>
<script>
import axios, { all } from "axios";
import ExcelJS from 'exceljs';
export default {
  data() {
    return {
      rules: {
        required: (value) => !!value || "Required.",
        min: (v) => v.length >= 8 || "Minimum 8 characters",
      },
      filter: {},
      search: "",
      loading: false,
      groupBy: [
        {
          key: "category",
          order: "asc",
        },
      ],
      headers: [
        {
          title: "PRODUCT",
          align: "start",
          key: "product_name",
        },
        {
          title: "SRP",
          align: "center",
          key: "retail_price",
        },
        {
          title: "SALES",
          align: "center",
          key: "sales",
        },
        {
          title: "TOTAL KG",
          align: "center",
          key: "total_kg",
        },
        {
          title: "OPENING",
          align: "center",
          key: "opening",
        },
        {
          title: "DELIVERY",
          align: "center",
          key: "delivery",
        },
        {
          title: "TOTAL",
          align: "center",
          key: "total",
        },
        {
          title: "CLOSING",
          align: "center",
          key: "closing",
        },
        {
          title: "DAMAGE",
          align: "center",
          key: "damage",
        },
        {
          title: "SHOULD BE SOLD(KG)",
          align: "center",
          key: "sbs",
        },
        {
          title: "KG(DISCREPANCY)",
          align: "center",
          key: "kg_des",
        },
        {
          title: "DISCREPANCY IN PESO",
          align: "center",
          key: "des_peso",
        },
        {
          title: "Actions",
          align: "center",
          key: "actions",
        },
      ],
      users: [],
      products: [],
      totals: [],
    };
  },
  methods: {
    loadProducts() {
      this.loading = true;
      axios.get("/fetch-reports-user").then((res) => {
        this.products = res.data;
      });
      axios.get("/fetch-reports-total-user").then((res) => {
        this.totals = res.data;
      });
      this.loading = false;
    },
    loadUsers() {
      axios.get("/fetch-users").then((res) => {
        this.users = [
          {
            user_id: 0,
            username: "All",
          },
          ...res.data,
        ];
        this.filter.range = "All";
      });
    },

    filterProducts() {
      this.loading = true;
      if (this.filter.range == "All") {
        this.loadProducts();
      } else if (
        this.filter.range == "Select" &&
        this.filter.to
      ) {
        axios.get("/fetch-reports-users/" + this.filter.to).then((res) => {
          this.products = res.data;
        });
        axios.get("/fetch-reports-total-users/" + this.filter.to).then((res) => {
          this.totals = res.data;
        });
      }
      this.loading = false;
    },
    async downloadExcel() {
      const workbook = new ExcelJS.Workbook();
      const totalsSheet = workbook.addWorksheet('Totals');
      const productsSheet = workbook.addWorksheet('Products');

      // Add column headers to the "Totals" sheet
      totalsSheet.columns = [
        { header: 'CATEGORY', key: 'category' },
        { header: 'TOTAL KG SOLD', key: 'total_sales' },
        { header: 'TOTAL DECLARED SALES', key: 'total_prices' },
        { header: 'DISCREPANCY', key: 'descrepancy' },
        { header: 'TOTAL', key: 'total' }
      ];

      // Add rows to the "Totals" sheet
      this.totals.forEach(item => {
        totalsSheet.addRow({
          category: item.category,
          total_sales: item.total_sales.toFixed(2),
          total_prices: item.total_prices.toFixed(2),
          descrepancy: item.descrepancy.toFixed(2),
          total: (item.descrepancy + item.total_prices).toFixed(2)
        });
      });

      // Add column headers to the "Products" sheet
      productsSheet.columns = [
        { header: 'PRODUCT NAME', key: 'product_name' },
        { header: 'RETAIL PRICE', key: 'retail_price' },
        { header: 'TOTAL KG', key: 'total_sales' },
        { header: 'SALES', key: 'sales' },
        { header: 'OPENING', key: 'opening' },
        { header: 'DELIVERY', key: 'delivery' },
        { header: 'TOTAL', key: 'total_quantity' },
        { header: 'CLOSING', key: 'closing' },
        { header: 'DAMAGE', key: 'total_wasted' },
        { header: 'SHOULD BE SOLD', key: 'sbs' },
        { header: 'KG(DISCREPANCY)', key: 'kg_des' },
        { header: 'DISCREPANCY IN PESO', key: 'des_peso' }

      ];

      // Add rows to the "Products" sheet
      this.products.forEach(item => {
        productsSheet.addRow({
          product_name: item.product_name,
          retail_price: item.retail_price.toFixed(2),
          total_sales: item.transactions[0]?.total_sales.toFixed(2) || 0.00,
          sales: item.transactions[0]?.total_sales * item.retail_price.toFixed(2) || 0.00,
          opening: item.transactions[0]?.opening.toFixed(2) || 0.00,
          delivery: item.transactions[0]?.delivery.toFixed(2) || 0.00,
          total_quantity: item.transactions[0]?.total_quantity.toFixed(2) || 0.00,
          closing: item.transactions[0]?.closing.toFixed(2) || 0.00,
          total_wasted: item.transactions[0]?.total_wasted.toFixed(2) || 0.00,
          sbs: (item.transactions[0]?.total_quantity - item.transactions[0]?.total_sales).toFixed(2) || 0.00,
          kg_des: (item.transactions[0]?.total_quantity - (item.transactions[0]?.closing + item.transactions[0]?.total_sales)).toFixed(2) || 0.00,
          des_peso: ((item.transactions[0]?.total_quantity - (item.transactions[0]?.closing + item.transactions[0]?.total_sales)) * item.retail_price).toFixed(2) || 0.00

        });
      });

      // Write the Excel file and trigger the download
      const buffer = await workbook.xlsx.writeBuffer();
      const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

      // Create a link element, use it to download the blob, then remove it
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = 'TablesData.xlsx';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
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
.striped-table {
  font-size: 11px;
}
</style>
