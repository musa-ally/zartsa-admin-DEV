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
            </tr>
          </thead>
        </table>

        <v-row>
          <v-col cols="12" sm="6">
            <span class="title-5">Full Name: {{ form.consignee }} </span><br />
          </v-col>
          <v-col cols="12" sm="6">
            <span class="title-5">BL No: {{ form.bol }}</span>
          </v-col>
        </v-row>
        <br /><br />
        <v-data-table
          :headers="headers"
          :items="customercargos"
          :search="search"
          :single-select="singleSelect"
          show-select
          @input="selectAll"
          class="elevation-0 table-content-2"
        >
          <template v-slot:[`header.data-table-select`]="{ on, props }">
            <v-simple-checkbox
              v-bind="props"
              v-on="on"
              class="checkbx"
            ></v-simple-checkbox>
          </template>

          <template v-slot:[`item.weight_kg`]="{ item }">
            <span>{{ item.weight_kg | currency("", 2) }}</span>
          </template>
          <template v-slot:[`item.amount_tzs`]="{ item }">
            <span>{{
              (item.type * form.de_amount) | currency("TZS ", 0)
            }}</span>
          </template>
          <template v-slot:[`item.amount_usd`]="{ item }">
            <span>{{
              ((item.type * form.de_amount) / form.exchange_rate)
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
        </v-data-table>
        <br />
        <v-row>
          <v-col cols="12" sm="6">
            <v-btn
              large
              link
              class="float-left"
              color="#00AEEF"
              style="color: white"
              elevation="0"
              @click="generateBill"
              :disabled="cargo_total.length < 1"
            >
              <span><v-icon>mdi-file</v-icon> Generate Bill</span>
            </v-btn>
          </v-col>
          <v-col cols="12" sm="6">
            <table class="table table-bordered">
              <tr class="title-4">
                <th style="width: 30%">VAT (18%)</th>
                <td>{{ CalVat | currency("USD ", 0) }}</td>
              </tr>
              <tr class="title-4">
                <th style="width: 30%">Sub Total</th>
                <td>{{ getTotal | currency("USD ", 0) }}</td>
              </tr>
              <tr class="title-4">
                <th style="width: 30%">Grand Total</th>
                <td>{{ grandTotal | currency("USD ", 0) }}</td>
              </tr>
            </table>
          </v-col>
        </v-row>
      </div>
    </div>
  </div>
</template>

<script>
import { BASE_URL } from "../../config/MainURL";
export default {
  name: "CreateStuffingBill",
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
        services_id: 16,
        external_users_id: 2,
        application_status: "Cleared",
        consignee: "",
        bill_status_id: 1,
        control_number: 88990024676790,
        exchange_rate: 2320,
        de_amount: 50000,
      }),
      cargoselection: {
        cargo_id: [],
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
          text: "Size",
          value: "type",
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
    getTotal: function () {
      let sum = this.cargo_total.reduce((a, b) => a + b, 0);
      return sum;
    },

    CalVat: function () {
      return (this.getTotal * 18) / 100;
    },

    grandTotal: function () {
      return this.getTotal + this.CalVat;
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
      // const token = localStorage.getItem("jwt");
      // axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
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

    generateBill() {
      // const token = localStorage.getItem("jwt");
      // axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
      let app = this;
      //wait for DOM update
      this.$nextTick(() => {
        //NOW trigger validation
        app.$Progress.start();
        app.$loading(true);
        axios
          .post(BASE_URL + "/api/auth/save_customer_bills", {
            cargoselection: app.cargoselection.cargo_id,
            bol: app.form.bol,
            bid: app.form.bid,
            services_id: app.form.services_id,
            external_users_id: app.form.external_users_id,
            application_status: app.form.application_status,
            control_number: app.form.control_number,
            bill_status_id: app.form.bill_status_id,
            amount_usd: app.grandTotal,
            exchange_rate: app.form.exchange_rate,
          })
          .then(() => {
            setTimeout(() => {
              app.$loading(false);
              Swal.fire("Bill Generated", "", "success");
              Swal.fire({
                icon: "success",
                title: "Bill Generated",
                showDenyButton: false,
                showCancelButton: false,
                confirmButtonText: "Ok",
                allowOutsideClick: false,
              }).then((result) => {
                if (result.isConfirmed) {
                  window.location.href = "/stuffing";
                }
              });
            }, 500);
          })
          .catch((e) => {
            app.$loading(false);
            console.log(e);
          });
      });
    },

    selectAll(e) {
      this.cargo_total = [];
      this.cargoselection.cargo_id = [];
      if (e.length > 0) {
        this.cargo_total = e.map(
          (val) =>
            (this.$options.filters.number(val.type) * this.form.de_amount) /
            this.form.exchange_rate
        );
        this.cargoselection.cargo_id = e.map((val) => val.cargo_id);
      }
    },
  },
  mounted() {},
  created() {
    this.getBolInfo();
  },
};
</script>
