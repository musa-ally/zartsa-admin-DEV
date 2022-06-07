<template>
  <div class="container">
    <div class="col-lg-12">
      <v-toolbar
          flat
          class="title-1"
          style="margin-bottom: 25px; background-color: #FFF;"
      >
        Customer Application
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
      </v-toolbar>
      <v-tabs
          v-model="cab"
          color="#20778b"
      >
        <v-tab class="title-tabs">Application</v-tab>
        <v-tab class="title-tabs">Bill</v-tab>
        <v-tabs-items v-model="cab" style="background-color: transparent;">
          <v-tab-item style="padding-bottom: 50px;margin-top: 4px;">
            <table class="table table-striped box_sh">
              <tr>
                <th colspan="2"
                    style="background-color: #20778b !important;color: white !important;font-family: 'Trebuchet MS';font-size: 18px;">
                  Customer Information
                </th>
              <tr>
                <th class="title-23">Full Name</th>
                <td class="title-23">{{ form.first_name || 'N/A' }} {{ form.last_name || 'N/A' }}</td>
              </tr>
              <tr>
                <th class="title-23">Phone Number</th>
                <td class="title-23">{{ form.phone_number || 'N/A' }}</td>
              </tr>
              <tr>
                <th class="title-23">Application Status</th>
                <td class="title-23">
              <span v-if="form.application_status == 'Cleared'" class="title-5">
            <v-chip
                class="ma-2"
                color="#32CD32"
                label
                text-color="white"
                style="color: white"
            >
              <v-icon left>mdi-check-circle</v-icon>
              {{ form.application_status }}</v-chip
            >
          </span>
                  <span v-else-if="form.application_status == 'Not Cleared'" class="title-5">
            <v-chip
                class="ma-2"
                color="#FF0000"
                label
                text-color="white"
                style="color: white"
            >
            {{ form.application_status }}</v-chip
            >
          </span>
                  <span v-else-if="form.application_status == 'Pending'">
            <v-chip
                class="ma-2"
                color="Primary"
                label
                text-color="white"
                style="color: white"
            >
             {{ form.application_status }}</v-chip
            >
          </span>
                  <span v-else-if="form.application_status == 'Discharged'">
            <v-chip
                class="ma-2"
                color="#32CD32"
                label
                text-color="white"
                style="color: white"
            >
              <v-icon left>mdi-check-circle</v-icon>
      {{ form.application_status }}</v-chip
            >
          </span>
                  <span v-else>N/A</span><br/>
                </td>
              </tr>
              <tr>
                <th class="title-23">Application Date</th>
                <td class="title-23">{{ form.app_date || 'N/A' }}</td>
              </tr>
            </table>

            <br/><br/>
            <v-data-table
                :items="customercargos"
                :headers="headers"
                :search="search"
                :loading="dloading"
                class="elevation-3 table-content-2"
                item-key="id"
            >
              <template v-slot:[`item.weight_kg`]="{ item }">
                <span>{{ item.weight_kg | currency("", 2) }}</span>
              </template>
              <template v-slot:[`item.created_at`]="{ item }">
                <span>{{ item.created_at | moment("from", true) }}</span>
              </template>
              <template v-slot:[`item.bill_status`]="{ item }">
                    <span v-if="item.bill_status == 'Paid'">
                      <v-chip
                          class="ma-2"
                          color="#4CBB17"
                          label
                          text-color="white"
                          style="color: white"
                      >
                        <v-icon left>mdi-check-circle</v-icon>
                        {{ item.bill_status }}</v-chip
                      >
                    </span>
                <span v-else-if="item.bill_status == 'Pending'">
                      <v-chip
                          class="ma-2"
                          color="#00AEEF"
                          label
                          text-color="white"
                          style="color: white"
                      >
                        {{ item.bill_status }}</v-chip
                      >
                    </span>
                <span v-else-if="item.bill_status == 'Expired'">
                      <v-chip
                          class="ma-2"
                          color="#FF0000"
                          label
                          text-color="white"
                          style="color: white"
                      >
                        {{ item.bill_status }}</v-chip
                      >
                    </span>
                <span v-else>N/A</span>
              </template>
              <template v-slot:[`item.amount_usd`]="{ item }">
                <span>{{ item.amount_usd | currency("USD ", 0) }}</span>
              </template>
              <template v-slot:[`item.amount_tzs`]="{ item }">
                <span>{{ item.amount_tzs | currency("TZS ", 0) }}</span>
              </template>
            </v-data-table>

            <br/>
            <v-row>
              <v-col cols="12" sm="6">
                <v-btn
                    large
                    link
                    class="float-left"
                    color="#4CBB17"
                    style="color: white"
                    elevation="3"
                    @click="clearCargo"
                    v-if="
              form.application_status == 'Not Cleared' &&
              filterBillStatus.includes('Paid')
            "
                >
                  <span><v-icon>mdi-check</v-icon> Clear</span>
                </v-btn>
              </v-col>
              <v-col cols="12" sm="6">
                <v-btn
                    large
                    link
                    class="float-right"
                    color="#00AEEF"
                    style="color: white"
                    elevation="3"
                    @click="dischargeCargo"
                    v-if="
              form.application_status == 'Cleared' &&
              filterBillStatus.includes('Paid')
            "
                >
                  <span><v-icon>mdi-check</v-icon> Discharge</span>
                </v-btn>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item style="padding-bottom: 50px;margin-top: 4px;">
            <ViewBill/>
          </v-tab-item>
        </v-tabs-items>
      </v-tabs>
    </div>
  </div>
</template>

<script>
import {BASE_URL, WEB_URL} from "../../config/MainURL";
import ViewBill from "./../bills/viewbill";

let web_url = WEB_URL;
export default {
  name: "BillClearance",
  data() {
    let appid = this.$route.query.appid;
    let servname = this.$route.query.servname;
    return {
      web_url,
      dloading: false,
      search: "",
      cab: "",
      show: false,
      dialog: false,
      form: new Form({
        first_name: "",
        last_name: "",
        phone_number: "",
        application_status: "",
        appid: appid,
        bol: "",
        cn: "",
        service_name: servname,
        app_date: ""
      }),
      statusparams: {
        appid: appid,
        app_code: 'CL001',
      },
      disstatusparams: {
        appid: appid,
        app_code: 'DS001',
      },
      customercargos: [],
      headers: [
        {
          text: "Cargo No",
          value: "number",
          class: "title-11",
          sortable: false,
        },
        {
          text: "Cargo Type",
          value: "name",
          class: "title-11",
          sortable: false,
        },
        {
          text: "Weight(Kg)",
          value: "weight_kg",
          class: "title-11",
          sortable: false,
        },
        {
          text: "BoL",
          value: "bl",
          class: "title-11",
          sortable: false,
        },
        {
          text: "Payment Status",
          value: "bill_status",
          class: "title-11",
          sortable: false,
        },
      ],
    };
  },
  components: {
    ViewBill
  },
  computed: {
    filterBillStatus() {
      return this.customercargos.map(function (cc) {
        return cc.bill_status;
      });
    },

    filterAmount() {
      return this.customercargos.map(function (cc) {
        return cc.amount_tzs;
      });
    },

    getTotal: function () {
      let sum = this.filterAmount.reduce((a, b) => a + b, 0);
      return sum;
    },

    CalVat: function () {
      return (this.getTotal * 18) / 100;
    },

    grandTotal: function () {
      return this.getTotal + this.CalVat;
    },
  },
  methods: {
    getCustomerPD() {
      let app = this;
      app.$loading(true);
      axios
          .get(BASE_URL + "/api/auth/customer_personal_data", {
            params: {
              appid: app.form.appid,
              usertype: 'E'
            },
          })
          .then(function (response) {
            setTimeout(() => {
              app.form.first_name = response.data[0].first_name;
              app.form.last_name = response.data[0].last_name;
              app.form.phone_number = response.data[0].phone_number;
              app.form.application_status = response.data[0].application_status;
              app.form.app_date = response.data[0].app_date;
              app.$loading(false);
            }, 500);
          })
          .catch((e) => {
            app.dloading = false;
            app.$loading(false);
            console.log(e);
          });
    },

    getCustomerCargo() {
      let app = this;
      app.dloading = true;
      axios
          .get(BASE_URL + "/api/auth/customer_bills", {
            params: {
              appid: app.form.appid,
              service_name: app.form.service_name
            },
          })
          .then(function (response) {
            setTimeout(() => {
              app.customercargos = response.data;
              app.form.bol = response.data[0].bl;
              app.form.cn = response.data[0].control_number;
              app.dloading = false;
            }, 500);
          })
          .catch((e) => {
            app.dloading = false;
            console.log(e);
          });
    },

    clearCargo() {
      Swal.fire({
        title: "Are you sure?",
        icon: "info",
        text: "You won't be able to revert this!",
        type: "warning",
        showDenyButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, clear cargo!",
        denyButtonText: `Don't clear cargo`,
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          let app = this;
          app.$Progress.start();
          app.$loading(true);
          axios
              .post(
                  BASE_URL + "/api/auth/update_application_status",
                  app.statusparams
              )
              .then(() => {
                app.$loading(false);
                Swal.fire("Cargo cleared!", "", "success");
                app.getCustomerPD();
              })
              .catch((error) => {
                app.$Progress.fail();
                app.$loading(false);
                console.log(error);
              });
        } else if (result.isDenied) {
          Swal.fire("No changes made", "", "info");
        }
      });
    },

    dischargeCargo() {
      const token = localStorage.getItem("jwt");
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
      Swal.fire({
        title: "Are you sure?",
        icon: "info",
        text: "You won't be able to revert this!",
        type: "warning",
        showDenyButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, discharge cargo!",
        denyButtonText: `Don't discharge cargo`,
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          let app = this;
          app.$Progress.start();
          app.$loading(true);
          axios
              .post(BASE_URL + "/api/auth/discharge_cargo", app.disstatusparams)
              .then(() => {
                app.$loading(false);
                Swal.fire("Cargo discharged!", "", "success");
                app.getCustomerPD();
              })
              .catch((error) => {
                app.$Progress.fail();
                app.$loading(false);
                console.log(error);
              });
        } else if (result.isDenied) {
          app.$loading(false);
          Swal.fire("No changes made", "", "info");
        }
      });
    },

    printInfo() {
      this.$htmlToPaper("printCustomerBill");
    },
  },
  mounted() {
  },
  created() {
    this.getCustomerPD();
    this.getCustomerCargo();
  },
};
</script>

<style>
</style>
