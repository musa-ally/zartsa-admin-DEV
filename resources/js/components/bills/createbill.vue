<template>
  <div class="container">
    <div class="col-lg-12">
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
        <table class="table table-striped box_sh">
          <tr>
            <th colspan="2"
                style="background-color: #20778b !important;color: white !important;font-family: 'Trebuchet MS';font-size: 18px;">
              Customer Information
            </th>
          <tr>
            <th class="title-23">Consignee</th>
            <td class="title-23">{{ form.consignee || 'N/A' }}</td>
          </tr>
          <tr>
            <th class="title-23">BoL</th>
            <td class="title-23">{{ form.bol || 'N/A' }}</td>
          </tr>
          <tr>
            <th class="title-23">Payer Details</th>
            <td class="title-23">
              <v-form
                  v-on:submit.prevent="generateBill"
                  @keydown="form.onKeydown($event)"
                  ref="form"
              >
              <v-row>
                <v-col cols="12" sm="4">
                  <v-text-field
                      label="Full Name"
                      regular
                      name="full_name"
                      v-model="form.payer_full_name"
                      class="nt v-input__control2"
                      color="rgb(32, 119, 139)"
                      autocomplete="off"
                      :rules="[GRules.required]"
                      hint="e.g. Abdallah"
                      ref="h"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="4">
                  <v-text-field
                      label="Phone Number"
                      regular
                      name="full_name"
                      v-model="form.payer_phone_number"
                      class="nt v-input__control2"
                      color="rgb(32, 119, 139)"
                      autocomplete="off"
                      v-mask="'##########'"
                      :rules="[GRules.required, GRules.minPhoneno]"
                      hint="e.g. 0773000000"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="4">
                  <v-text-field
                      label="Email"
                      regular
                      name="email"
                      v-model="form.payer_email_address"
                      class="nt v-input__control2"
                      color="rgb(32, 119, 139)"
                      autocomplete="off"
                      :rules="[GRules.required, GRules.email]"
                      hint="e.g. abdallah@gmail.com"
                  ></v-text-field>
                </v-col>
              </v-row>
              <br/>
              </v-form>
            </td>
          </tr>
        </table>

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

          <template v-slot:footer>
            <v-row>
              <v-col cols="12" sm="6"></v-col>
              <v-col cols="12" sm="6">
                <br/><br/>
                <table class="table table-striped">
                  <tr class="title-4">
                    <th style="width: 30%">USD Exchange rate</th>
                    <td>{{ form.xchange_rate | currency("USD ", 0) }}</td>
                  </tr>
                  <tr class="title-4">
                    <th style="width: 30%">VAT (15%)</th>
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
  name: "CreateBill",
  data() {
    let bol = this.$route.query.bol;
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
        bol: bol,
        bid: "",
        services_id: 1,
        application_status_id: 2,
        consignee: "",
        xchange_rate: "",
        services_code: "CL001",
        external_users_id: 1,
        bill_status_id: 2,
        payer_full_name: "",
        payer_phone_number: "",
        payer_email_address: ""
      }),
      cargoselection: [],
      statusparams: {
        appid: "",
        appstatus: 2,
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
        email: (value) => {
          const pattern =
              /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return pattern.test(value) || "Invalid e-mail";
        },
        minPhoneno: (value) =>
            (value && value.length > 9) || "Invalid Phone Number",
      },
    };
  },
  computed: {
    allcustomercargos() {
      return this.customercargos.map(x => ({...x, isSelectable: x.bill_status === 'NOT_INITIATED'}));
    },

    getTotal: function () {
      let sum = this.cargo_total.reduce((a, b) => a + b, 0);
      return sum;
    },

    getsubTotalUSD: function () {
      let sum = this.cargo_total.reduce((a, b) => a + b, 0);
      return sum / this.form.xchange_rate;
    },

    CalVat: function () {
      return (this.getTotal * 15) / 100;
    },

    grandTotal: function () {
      return this.getTotal + this.CalVat;
    },

    filterBillStatus() {
      return this.customercargos.map(function (cc) {
        return cc.bill_status;
      });
    },

    filterDays() {
      return this.customercargos.map(function (cc) {
        let formatteddate = moment(cc.bol_date)
            .add(7, "d")
            .format("YYYY-MM-DD");
        return formatteddate;
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
              app.form.bid = response.data[0]?.bid;
              app.form.xchange_rate = response.data[0]?.xchange_rate;
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
      app.GRules;
      //wait for DOM update
      this.$nextTick(() => {
        //NOW trigger validation
        if (this.$refs.form.validate()) {
        app.$loading(true);
        axios
            .post(BASE_URL + "/api/auth/save_customer_bills", {
              cargoselection: app.cargoselection,
              bid: app.form.bid,
              xchange_rate: app.form.xchange_rate,
              payer_full_name: app.form.payer_full_name,
              payer_email_address: app.form.payer_email_address,
              payer_phone_number: app.form.payer_phone_number,
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
                    window.location.href = WEB_URL + "/check_clearance";
                  }
                });
              }, 500);
            })
            .catch((e) => {
              console.log(e);
              app.$loading(false);
            });
      }
      });
    },

    selectAll(e) {
      this.cargo_total = [];
      this.cargoselection = [];
      this.selected = [];
      if (e.length > 0) {
        this.cargo_total = e.map(
            (val) =>
                (this.$options.filters.number(val.weight_kg) * this.countDays));
        this.cargoselection = e.map((val) => ({
          id: val.id,
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
    this.getBolInfo();
  },
};
</script>
