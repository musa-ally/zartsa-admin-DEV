<template>
  <div class="container">
    <div class="col-lg-12">
      <v-toolbar
          flat
          class="title-1"
          style="margin-bottom: 25px; background-color: #FFF;"
      >
        Destuffing
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
      </v-toolbar>
      <v-form v-on:submit.prevent="getBolInfo">
        <v-row>
          <v-col cols="12" sm="3">
            <v-text-field
                label="BL No"
                outlined
                name="blno"
                v-model="form.bol"
                class="nt v-input__control2"
                color="#008b8b"
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
        <div class="card">
          <div class="card-header title-17" style="background-color: #20778b;">
            Customer Information
          </div>
          <div class="card-body">
            <v-row style="background-color: #FFF;">
              <v-col cols="12" sm="6">
                <span class="title-5">Full Name: {{ form.consignee || 'N/A' }} </span><br/>
              </v-col>
              <v-col cols="12" sm="6">
                <span class="title-5">BoL No: {{ form.bol || 'N/A' }}</span>
              </v-col>
            </v-row>
          </div>
        </div>
        <br/>
        <v-data-table
            :headers="headers"
            :items="allcustomercargos"
            :search="search"
            show-select
            class="elevation-3 table-content-2"
            @input="selectAll"
        >
          <template v-slot:[`item.weight_kg`]="{ item }">
            <span>{{ item.weight_kg | currency("", 2) }}</span>
          </template>
          <template v-slot:[`item.amount_tzs`]="{ item }">
            <span>{{
                (item.de_bill) | currency("TZS ", 0)
              }}</span>
          </template>
          <template v-slot:[`item.amount_usd`]="{ item }">
            <span>{{
                ((item.de_bill) / form.exchange_rate)
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
          <template v-slot:footer>
            <v-row>
              <v-col cols="12" sm="6"></v-col>
              <v-col cols="12" sm="6">
                <br/><br/>
                <table class="table table-striped">
                  <tr class="title-4">
                    <th style="width: 30%">USD Exchange rate</th>
                    <td>{{ form.exchange_rate | currency("USD ", 0) }}</td>
                  </tr>
                  <tr class="title-4">
                    <th style="width: 30%">VAT (18%)</th>
                    <td>{{ CalVat | currency("TZS ", 0) }}</td>
                  </tr>
                  <tr class="title-4">
                    <th style="width: 30%">Sub Total (USD)</th>
                    <td>{{ getsubTotalUSD | currency("USD ", 0) }}</td>
                  </tr>
                  <tr class="title-4">
                    <th style="width: 30%">Sub Total (TZS)</th>
                    <td>{{ getTotal | currency("TZS ", 0) }}</td>
                  </tr>
                  <tr class="title-4">
                    <th style="width: 30%">Grand Total</th>
                    <td>{{ grandTotal | currency("TZS ", 0) }}</td>
                  </tr>
                  <tr class="title-4">
                    <th style="width: 30%"></th>
                    <td>
                      <br/>
                      <v-btn
                          large
                          link
                          class="float-right"
                          color="#00AEEF"
                          style="color: white"
                          elevation="2"
                          @click="generateBill"
                          :disabled="cargo_total.length < 1"
                      >
                        <span><v-icon>mdi-file</v-icon> Generate Bill</span>
                      </v-btn>
                    </td>
                  </tr>
                </table>
              </v-col>
            </v-row>
          </template>
        </v-data-table>
      </div>
    </div>
  </div>
</template>

<script>
import {BASE_URL, WEB_URL} from "./../../config/MainURL";

export default {
  name: "DestuffingIndex",
  data() {
    return {
      dloading: false,
      search: "",
      show: false,
      msg: "",
      dialog: false,
      randomCN: "",
      singleSelect: false,
      selected: [],
      form: new Form({
        bol: '',
        bid: "",
        application_status_id: 2,
        consignee: "",
        exchange_rate: 2320,
        services_code: "CL001",
        bill_status_id: 2,
        control_number: 88990024676790,
      }),
      cargoselection: [],
      statusparams: {
        appid: "",
        appstatus: 6,
      },
      headers: [
        {
          text: "Cargo No",
          value: "cargo_no",
          align: "start",
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
          text: "Amount(USD)",
          value: "amount_usd",
          class: "title-11",
          sortable: false,
        },
        {
          text: "Amount(TZS)",
          value: "amount_tzs",
          class: "title-11",
          sortable: false,
        },
      ],
      cargo_total: [],
      customercargos: [],
      GRules: {
        required: (value) => !!value || "Field is required",
      },
    };
  },
  computed: {
    allcustomercargos() {
      return this.customercargos.map(x => ({...x, isSelectable: x.service_stage !== 'Destuffing' && x.bill_status === 'Paid' && x.application_status === 'Discharged'}));
    },

    getTotal: function () {
      let sum = this.cargo_total.reduce((a, b) => a + b, 0);
      return sum;
    },

    getsubTotalUSD: function () {
      let sum = this.cargo_total.reduce((a, b) => a + b, 0);
      return sum / this.form.exchange_rate;
    },

    CalVat: function () {
      return (this.getTotal * 18) / 100;
    },

    grandTotal: function () {
      return this.getTotal + this.CalVat;
    },

    filterBillStatus() {
      return this.customercargos.map(function (cc) {
        return cc.bill_status;
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
          .get(BASE_URL + "/api/auth/check_bol_destuffing", {
            params: {
              bol: app.form.bol,
            },
          })
          .then(function (response) {
            setTimeout(() => {
              app.customercargos = response.data;
              app.form.consignee = response.data[0]?.consignee;
              app.form.bid = response.data[0]?.bid;
              app.dloading = false;
              app.msg = "";
              app.$loading(false);
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

    generateBill() {
      let app = this;
      //wait for DOM update
      this.$nextTick(() => {
        //NOW trigger validation
        this.$Progress.start();
        app.$loading(true);
        axios
            .post(BASE_URL + "/api/auth/create_destuffing_bill", {
              cargoselection: app.cargoselection,
              bid: app.form.bid,
              application_status_id: app.statusparams.appstatus,
              bill_status_id: app.form.bill_status_id,
              amount_tzs: app.grandTotal,
              amount_usd: app.getsubTotalUSD,
            })
            .then(() => {
              setTimeout(() => {
                app.$loading(false);
                Swal.fire({
                  icon: "success",
                  title: "Bill Generated",
                  showDenyButton: false,
                  showCancelButton: false,
                  confirmButtonText: "Ok",
                  allowOutsideClick: false,
                }).then((result) => {
                  if (result.isConfirmed) {
                    window.location.href = WEB_URL + "/destuffing";
                  }
                });
              }, 500);
            })
            .catch((e) => {
              console.log(e);
              app.$loading(false);
            });
      });
    },

    selectAll(e) {
      this.cargo_total = [];
      this.cargoselection = [];
      this.selected = [];
      if (e.length > 0) {
        this.cargo_total = e.map(
            (val) =>
                val.de_bill);
        this.cargoselection = e.map((val) => ({
          id: val.id,
          ccsid: val.ccsid,
          cargo_type_id: val.cargo_type_id,
          voyage_id: val.voyage_id,
          vessel_id: val.vessel_id
        }));
      }
    },
  },
  mounted() {
  },
  created() {
  },
};
</script>
