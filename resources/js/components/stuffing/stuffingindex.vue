<template>
  <div class="container">
    <div class="col-lg-12">
      <v-toolbar
        flat
        class="title-1"
        style="margin-bottom: 25px; background-color: rgba(0, 0, 0, 0.042)"
      >
        Stuffing
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
              color="#008b8b"
              autocomplete="off"
              :rules="[GRules.required]"
              v-mask="'##################'"
              :error-messages="msg"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="3" style="margin-top: 3px">
            <v-btn
              type="submit"
              x-large
              elevation="0"
              class="login-btn"
              color="#008b8b"
              style="color: white"
              :disabled="form.bol.length < 4"
              ><v-icon color="white">mdi-magnify</v-icon> Search</v-btn
            >
          </v-col>
        </v-row>
      </v-form>

      <br />

      <div v-if="customercargos.length > 0">
        <table class="table table-bordered table-striped">
          <thead class="title-16">
            <tr>
              <th colspan="2" class="title-3-bg">Customer Information</th>
              <div style="display: none">
                {{ comparison_results }} {{ hideCCButtons }}
                {{ countDays }}
              </div>
            </tr>
          </thead>
        </table>
        <v-row>
          <v-col cols="12" sm="6">
            <span class="title-5">Full Name: {{ form.consignee }} </span>
          </v-col>
          <v-col cols="12" sm="6">
            <span class="title-5">BL No: {{ form.bol }}</span
            ><br />
            <span class="title-5">Status: {{ form.application_status }}</span>
          </v-col>
        </v-row>
        <br /><br />
        <v-data-table
          :headers="headers"
          :items="customercargos"
          :search="search"
          :loading="dloading"
          class="elevation-0 table-content-2"
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
              ((item.weight_kg * countDays) / form.exchange_rate)
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
              <v-chip
                class="ma-2"
                color="#32CD32"
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
                <v-icon left>mdi-close</v-icon>
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
                <v-icon left>mdi-close</v-icon>
                {{ item.bill_status }}</v-chip
              >
            </span>
            <span v-else>N/A</span>
          </template>
        </v-data-table>
        <br />
        <v-row>
          <v-col cols="12" sm="6"> </v-col>
          <v-col cols="12" sm="6">
            <v-btn
              link
              class="float-right"
              color="#00AEEF"
              elevation="0"
              :href="'/stuffing/create_stuffing_bill?bol=' + form.bol"
              style="text-decoration: none; color: white"
              v-if="
                comparison_results == true &&
                form.application_status == 'Discharged'
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
import { BASE_URL } from "../../config/MainURL";
export default {
  name: "StuffingIndex",
  data() {
    return {
      dloading: false,
      todaydate: new Date().toISOString().substr(0, 10),
      search: "",
      msg: "",
      error_status: "",
      show: false,
      comparison_results: "",
      form: new Form({
        bol: "",
        bid: "",
        first_name: "",
        last_name: "",
        phone_number: "",
        consignee: "",
        application_status: null,
        services_id: 15,
        external_users_id: 1,
        bill_status_id: 2,
        control_number: 88990024676790,
        exchange_rate: 2320,
      }),
      statusparams: {
        appid: "",
        appstatus: "Cleared",
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
      // const token = localStorage.getItem("jwt");
      // axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
      let app = this;
      app.msg = "";
      app.dloading = true;
      app.$loading(true);
      axios
        .get(BASE_URL + "/api/auth/check_bol_stuffing", {
          params: {
            bol: app.form.bol,
            services_id: app.form.services_id,
          },
        })
        .then(function (response) {
          setTimeout(() => {
            app.customercargos = response.data;
            app.form.consignee = response.data[0]?.consignee;
            app.form.application_status = response.data[0]?.application_status;
            app.form.bid = response.data[0]?.bid;
            app.statusparams.appid = response.data[0]?.application_id;
            app.dloading = false;
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
  },
  mounted() {},
  created() {},
};
</script>
