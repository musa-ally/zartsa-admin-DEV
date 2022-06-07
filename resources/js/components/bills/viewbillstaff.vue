<template>
  <div>
    <div id="printCustomerBill">
      <table class="table table-bordered" elevation="3">
        <thead>
        <tr>
          <th colspan="2" class="tablebgcol"
              style="background-color: #20778b !important;color: white !important;font-family: 'Trebuchet MS';font-size: 18px;">
            Customer Bill
          </th>
        </tr>
        <tr>
          <th colspan="2" v-if="printImg === true">
            <div align="center">
              <v-img
                  :src="image_src"
                  max-height="320"
                  max-width="200"
                  class="pr_logo"
              ></v-img>
            </div>
          </th>
        </tr>
        <tr>
          <th class="title-23">Bill ID</th>
          <td class="title-23">{{ form.bill_id || 'N/A' }}</td>
        </tr>
        <tr>
          <th class="title-23">Full Name</th>
          <td class="title-23">{{ form.payer_full_name || 'N/A' }}</td>
        </tr>
        <tr>
          <th class="title-23">Phone Number</th>
          <td class="title-23">{{ form.payer_phone_number || 'N/A' }}</td>
        </tr>
        <tr>
          <th class="title-23">Email</th>
          <td class="title-23">{{ form.payer_email_address || 'N/A' }}</td>
        </tr>
        <tr>
          <th class="title-23">Control Number</th>
          <td class="title-23">{{ form.cn || 'N/A' }}</td>
        </tr>
        </thead>
      </table>
      <table class="table table-striped responsive-table" elevation="3">
        <thead class="title-5">
        <tr style="color: white">
          <th class="title-11" style="background-color: #20778b !important;">Cargo No</th>
          <th class="title-11" style="background-color: #20778b !important;">Cargo Type</th>
          <th class="title-11" style="background-color: #20778b !important;">Weight(Kg)</th>
          <th class="title-11" style="background-color: #20778b !important;">BoL</th>
          <th class="title-11" style="background-color: #20778b !important;">Payment Status</th>
        </tr>
        </thead>
        <tr v-for="item in customercargos" :key="item.id">
          <td class="title-23">{{ item.number || 'N/A' }}</td>
          <td class="title-23">{{ item.name || 'N/A' }}</td>
          <td class="title-23">{{ item.weight_kg || 'N/A' }}</td>
          <td class="title-23">{{ item.bl || 'N/A' }}</td>
          <td class="title-23">{{ item.bill_status || 'N/A' }}</td>
        </tr>
        <br/><br/>
        <tr>
          <th colspan="3" class="title-23"></th>
          <th colspan="1" class="title-23">VAT (15%)</th>
          <td class="title-23">{{ CalVat | currency("TZS ", 0) }}</td>
        </tr>
        <tr>
          <th colspan="3" class="title-23"></th>
          <th colspan="1" class="title-23">Subtotal</th>
          <td class="title-23">{{ subTotal | currency("TZS ", 0) }}</td>
        </tr>
        <tr>
          <th colspan="3" class="title-23"></th>
          <th colspan="1" class="title-23">Grand Total</th>
          <td class="title-23">{{ grandTotal | currency("TZS ", 0) }}</td>
        </tr>
        <br/><br/>
      </table>
    </div>
    <v-btn
        large
        class="float-right"
        color="primary"
        style="color: #fff"
        elevation="1"
        @click="printBill()"
    >
      <span><v-icon>mdi-printer</v-icon> Print</span>
    </v-btn>
  </div>
</template>

<script>
import {BASE_URL, WEB_URL} from "../../config/MainURL";
import printJS from 'print-js';
let web_url = WEB_URL;
export default {
  name: "ViewBillStaff",
  data() {
    let appid = this.$route.query.appid;
    let servname = this.$route.query.servname;
    return {
      web_url,
      printImg: false,
      dloading: false,
      search: "",
      show: false,
      form: new Form({
        first_name: "",
        last_name: "",
        phone_number: "",
        application_status: "",
        appid: appid,
        bol: "",
        cn: "",
        service_name: servname,
        amount_tzs: '',
        bill_id: '',
        payer_full_name: "",
        payer_phone_number: "",
        payer_email_address: ""
      }),
      customercargos: [],
      styleCard: {
        backgroundColor: '#20778b',
        color: "white",
        fontSize: 23,
        fontFamily: "Trebuchet MS",
        textTransform: "Capitalize",
      },
      image_src: web_url + '/images/bandari.png',
    };
  },
  computed: {
    filterBillStatus() {
      return this.customercargos.map(function (cc) {
        return cc.bill_status;
      });
    },

    CalVat: function () {
      return (this.form.amount_tzs * 15) / 100;
    },

    subTotal: function () {
      return (this.form.amount_tzs - this.CalVat);
    },

    grandTotal: function () {
      return (this.subTotal + this.CalVat);
    },
  },
  methods: {
    printBill() {
      this.printImg = true;
      printJS({
        printable: 'printCustomerBill',
        type: 'html',
        targetStyles: ['*'],
        css: web_url + '/css/print/printing.css',
        onLoadingEnd: function () {
          this.printImg = true;
        },
        onPrintDialogClose: this.printImg = false,
        onError: function (error) {
          alert('Error found => ' + error.message)
        }
      })
    },
    getCustomerPD() {
      let app = this;
      app.$loading(true);
      axios
          .get(BASE_URL + "/api/auth/customer_personal_data", {
            params: {
              appid: app.form.appid,
              usertype: 'I'
            },
          })
          .then(function (response) {
            setTimeout(() => {
              app.form.payer_full_name = response.data[0].payer_full_name;
              app.form.payer_phone_number = response.data[0].payer_phone_number;
              app.form.payer_email_address = response.data[0].payer_email_address;
              app.form.application_status = response.data[0].application_status;
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
              app.form.amount_tzs = response.data[0].amount_tzs;
              app.form.bill_id = response.data[0].bill_id;
              app.dloading = false;
            }, 500);
          })
          .catch((e) => {
            app.dloading = false;
            console.log(e);
          });
    },

    printInfo() {
      this.printImg = true;
      this.$htmlToPaper('printCustomerBill', null, () => {
        this.printImg = false;
      });

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
