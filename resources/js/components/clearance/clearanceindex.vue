<template>
  <div class="container">
    <div class="col-lg-12">
      <v-toolbar
          flat
          class="title-1"
          style="margin-bottom: 25px; background-color: #FFF;"
      >
        Check Clearance
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
      </v-toolbar>
      <v-form v-on:submit.prevent="getBolInfo">
        <v-row>
          <v-col cols="12" sm="3">
            <v-text-field
                label="BL No"
                outlined
                name="bol"
                v-model="form.bol"
                class="nt v-input__control2"
                color="#000"
                autocomplete="off"
                :rules="[GRules.required]"
                :error-messages="msg"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="3" style="margin-top: 3px">
            <v-btn
                type="submit"
                x-large
                elevation="0"
                class="login-btn"
                color="#20778b"
                style="color: white"
                :disabled="form.bol.length < 4"
            >
              <v-icon color="white">mdi-magnify</v-icon>
              Search
            </v-btn
            >
          </v-col>
        </v-row>
      </v-form>
      <br/>

      <div v-if="customercargos.length > 0">
        <table class="table table-striped box_sh">
          <tr>
            <th colspan="2"
                style="background-color: #20778b !important;color: white !important;font-family: 'Trebuchet MS';font-size: 18px;">
              Customer Information
            </th>
          <tr>
            <th class="title-23">Full Name</th>
            <td class="title-23">{{ form.consignee || 'N/A' }}</td>
          </tr>
          <tr>
            <th class="title-23">BoL No</th>
            <td class="title-23">{{ form.bol || 'N/A' }}</td>
          </tr>
          <tr>
            <th class="title-23">Shipping Line</th>
            <td class="title-23">{{ form.sl_name || 'N/A' }}</td>
          </tr>
          <tr>
            <th class="title-23">Vessel</th>
            <td class="title-23">{{ form.ves_name || 'N/A' }}</td>
          </tr>
          <tr>
            <th class="title-23">Voyage No</th>
            <td class="title-23">{{ form.voy_no || 'N/A' }}</td>
          </tr>
          <tr>
            <th class="title-23">ETA</th>
            <td class="title-23">{{ form.eat || 'N/A' }}</td>
          </tr>
        </table>

        <br/><br/>
        <v-data-table
            :headers="headers"
            :items="customercargos"
            :search="search"
            :loading="dloading"
            class="elevation-3 table-content-2"
        >
          <template v-slot:[`item.weight_kg`]="{ item }">
            <span>{{ item.weight_kg | currency("", 2) }}</span>
          </template>
          <template v-slot:[`item.amount_tzs`]="{ item }">
            <span>{{
                (item.weight_kg * countDays) | currency("TZS ", 0)
              }}</span>
          </template>
          <template v-slot:[`item.amount_usd`]="{ item }">
            <span>{{
                ((item.weight_kg * countDays) / item.xchange_rate)
                    | currency("USD ", 0)
              }}</span>
          </template>
          <template v-slot:[`item.bol_date`]="{ item }">
            <span v-if="$moment(item.bol_date).add(7, 'd') > new Date()"
            >0</span
            >
            <span v-if="$moment(item.bol_date).add(7, 'd') < new Date()">{{
                item.bol_date | moment("add", "7 days", "from", true)
              }}</span>
          </template>
          <template v-slot:[`item.bill_status`]="{ item }">
            <span v-if="item.bill_status == 'Paid'">
              <v-chip class="ma-2" color="success" label text-color="white">
                <v-icon left>mdi-check-circle</v-icon>
                {{ item.bill_status }}</v-chip
              >
            </span>
            <span v-else-if="item.bill_status == 'Pending'">
              <v-chip class="ma-2" color="primary" label text-color="white">
                <v-icon left>mdi-close</v-icon>
                {{ item.bill_status }}</v-chip
              >
            </span>
            <span v-else-if="item.bill_status == 'Expired'">
              <v-chip class="ma-2" color="red" label text-color="white">
                <v-icon left>mdi-close</v-icon>
                {{ item.bill_status }}</v-chip
              >
            </span>
            <span v-else>N/A</span>
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
                comparison_results == false &&
                form.application_status !== 'Cleared' &&
                form.application_status !== 'Discharged' &&
                filterBillStatus.includes('NO_PAYMENT')
              "
            >
              <span><v-icon>mdi-check</v-icon> Clear</span>
            </v-btn>
          </v-col>
          <v-col cols="12" sm="6">
            <v-btn
                link
                class="float-right"
                color="#00AEEF"
                elevation="3"
                :href="web_url + '/check_clearance/create_bill?bol=' + form.bol"
                style="text-decoration: none; color: white"
                v-if="
                comparison_results == true &&
                form.application_status !== '' &&
                form.application_status !== 'Cleared' &&
                form.application_status !== 'Not Cleared' &&
                form.application_status !== 'Discharged'  ||
                filterBillStatus.includes('NOT_INITIATED') ||
                oppofilterBillStatus.includes('NO_PAYMENT') ||
                oppofilterBillStatus.includes('Pending') ||
                oppofilterBillStatus.includes('Paid')
              "
            >
              <span><v-icon>mdi-receipt</v-icon>Create Bill</span>
            </v-btn>
          </v-col>
        </v-row>
      </div>
    </div>
  </div>
</template>

<script>
import {BASE_URL, WEB_URL} from "./../../config/MainURL";

let web_url = WEB_URL;
export default {
  name: "CheckClearance",
  data() {
    return {
      web_url,
      userid: document
          .querySelector("meta[name='user-id']")
          .getAttribute("content"),
      dloading: false,
      todaydate: new Date().toISOString().substr(0, 10),
      search: "",
      msg: "",
      proerror: "",
      error_status: "",
      show: false,
      dialog: false,
      comparison_results: "",
      form: new Form({
        bol: "",
        bid: "",
        first_name: "",
        last_name: "",
        phone_number: "",
        consignee: "",
        application_status: null,
        services_code: "CL001",
        eat: "",
        ad: "",
        ves_name: "",
        sl_name: "",
        voy_no: "",
        xchange_rate: ""
      }),
      statusparams: {
        appid: "",
        appcode: 'CL001',
      },
      customercargos: [],
      headers: [
        {
          text: "Cargo No",
          value: "cargo_no",
          class: "title-11",
          sortable: false,
        },
        {
          text: "Cargo Type",
          value: "NAME",
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
          text: "Days",
          value: "bol_date",
          class: "title-11",
          sortable: false,
        },
        {
          text: "Amount(TZS)",
          value: "amount_tzs",
          class: "title-11",
          sortable: false,
        },
        {
          text: "Amount(USD)",
          value: "amount_usd",
          class: "title-11",
          sortable: false,
        },
        {
          text: "Payment Status",
          value: "bill_status",
          class: "title-11",
          sortable: false,
        },
        {
          text: "",
          value: "action",
          class: "title-11",
          sortable: false,
        },
      ],
      GRules: {
        required: (value) => !!value || "Field is required",
      },
    };
  },
  computed: {
    filterDays() {
      return this.customercargos.map(function (cc) {
        let formatteddate = moment(cc.bol_date)
            .add(7, "d")
            .format("YYYY-MM-DD");
        return formatteddate;
      });
    },

    filterBillStatus() {
      return this.customercargos.map(function (cc) {
        return cc.bill_status;
      });
    },

    oppofilterBillStatus() {
      return this.customercargos.map(function (cc) {
        return !cc.bill_status;
      });
    },

    countDays() {
      let todaydate = new Date().toISOString().substr(0, 10);
      let MODate = moment(this.filterDays, "YYYY-MM-DD");
      let OGDate = moment(todaydate, "YYYY-MM-DD");
      if (moment.duration(OGDate.diff(MODate)).asDays() < 0) {
        return 0;
      } else {
        return moment.duration(OGDate.diff(MODate)).asDays();
      }
    },

    hideCCButtons() {
      let todaydate = new Date().toISOString().substr(0, 10);
      this.comparison_results = this.filterDays.every(function (e) {
        return e < todaydate;
      });
      console.log(this.comparison_results);
    },

    filterCargoIDS() {
      return this.customercargos.map(function (cc) {
        return cc.cargo_id;
      });
    },
  },
  methods: {
    getBolInfo() {
      let app = this;
      app.msg = "";
      app.dloading = true;
      app.$loading(true);
      axios
          .get(BASE_URL + "/api/auth/check_bol", {
            params: {
              bol: app.form.bol,
            },
          })
          .then(function (response) {
            setTimeout(() => {
              app.customercargos = response.data;
              app.form.consignee = response.data[0]?.consignee;
              app.form.application_status = response.data[0]?.application_status;
              app.statusparams.appid = response.data[0]?.application_id;
              app.form.bid = response.data[0]?.bid;
              app.form.eat = response.data[0]?.eat;
              app.form.ad = response.data[0]?.ad;
              app.form.dd = response.data[0]?.depd;
              app.form.ves_name = response.data[0]?.ves_name;
              app.form.sl_name = response.data[0]?.sl_name;
              app.form.voy_no = response.data[0]?.voy_no;
              app.form.xchange_rate = response.data[0]?.xchange_rate;
              app.dloading = false;
              // loader.hide();
              app.$loading(false);
              app.msg = "";
            }, 500);
          })
          .catch((e) => {
            app.msg = e.response.data.message;
            app.customercargos = "";
            app.form.consignee = "";
            app.dloading = false;
            app.$loading(false);
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
          axios
              .post(BASE_URL + "/api/auth/clear_cargo", {
                cargoselection: app.customercargos,
                bid: app.form.bid,
                xchange_rate: app.form.xchange_rate
              })
              .then(() => {
                // app.$loading(false);
                Swal.fire("Cargo cleared!", "", "success");
                app.getBolInfo();
              })
              .catch((error) => {
                console.log(error);
                this.proerror = error.response.data.status_code;
              });
        } else if (result.isDenied) {
          Swal.fire("No changes made", "", "info");
        }
      });
    },
  },
  mounted() {
  },
  created() {
  },
};
</script>
