<template>
  <div class="container">
    <div class="col-lg-12">
      <v-toolbar
          flat
          class="title-1"
          style="margin-bottom: 25px; background-color: #FFF;"
      >
        Customer Applications
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
      </v-toolbar>
      <v-data-table
          :headers="headers"
          :items="getapplications"
          :search="search"
          :loading="dloading"
          loading-text="Loading... Please wait"
          class="elevation-3"
      >
        <template v-slot:top>
          <v-toolbar flat class="title-1" style="margin-bottom: 20px;padding-top: 10px">
            <v-col cols="12" sm="3">
              <v-btn
                  large
                  link
                  class="float-left"
                  color="primary"
                  style="color: white"
                  elevation="2"
                  @click="launchDialog()"
              >
                <span><v-icon>mdi-filter</v-icon> Filter</span>
              </v-btn>
            </v-col>
            <v-spacer></v-spacer>
            <div class="col-lg-4">
              <v-text-field
                  v-model="search"
                  append-icon="mdi-magnify"
                  label="Search"
                  hide-details
                  outlined
                  color="#008b8b"
              ></v-text-field>
            </div>
          </v-toolbar>
        </template>
        <template v-slot:[`item.application_status`]="{ item }">
          <span v-if="item.application_status == 'Cleared'">
            <v-chip
                class="ma-2"
                color="#32CD32"
                label
                text-color="white"
                style="color: white"
            >
              <v-icon left>mdi-check-circle</v-icon>
              {{ item.application_status }}</v-chip
            >
          </span>
          <span v-else-if="item.application_status == 'Not Cleared'">
            <v-chip
                class="ma-2"
                color="#FF0000"
                label
                text-color="white"
                style="color: white"
            >
              {{ item.application_status }}</v-chip
            >
          </span>
          <span v-else-if="item.application_status == 'Pending'">
            <v-chip
                class="ma-2"
                color="Primary"
                label
                text-color="white"
                style="color: white"
            >
              {{ item.application_status }}</v-chip
            >
          </span>
          <span v-else-if="item.application_status == 'Discharged'">
            <v-chip
                class="ma-2"
                color="#32CD32"
                label
                text-color="white"
                style="color: white"
            >
              <v-icon left>mdi-check-circle</v-icon>
              {{ item.application_status }}</v-chip
            >
          </span>
          <span v-else>N/A</span>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
          <v-btn
              text
              Normal
              style="text-decoration: none; color: inherit"
              :href="
              web_url + '/customer_applications/bill_clearance?appid=' +
              item.application_id +'&servname='+ item.service_name
            "
          >
            <span> <v-icon>mdi-eye</v-icon> </span>
          </v-btn>
        </template>
      </v-data-table>
    </div>

    <v-dialog
        v-model="dialog"
        scrollable
        persistent
        max-width="500px"
    >
      <v-card>
        <v-card-title style="background-color: #008b8b;color: white;">
          <span class="title-17">Search Filters</span>
          <v-spacer></v-spacer>
          <v-btn icon dark @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text height="600px" style="margin-top: 23px;">

          <v-col cols="12" sm="12">
            <v-select
                :items="as"
                item-text="status"
                item-value="code"
                label="Status"
                outlined
                name="app_code"
                class="nt v-input__control2"
                color="#008b8b"
                v-model="app_code"
            ></v-select>
          </v-col>
          <v-col cols="12" sm="12">
            <v-dialog
                ref="stdialog"
                v-model="stdatemodal"
                :return-value.sync="date"
                persistent
                width="290px"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                    v-model="form.start"
                    label="From"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    outlined
                    class="nt v-input__control2"
                    color="#008b8b"
                ></v-text-field>
              </template>
              <v-date-picker
                  v-model="form.start"
                  scrollable
                  color="#008b8b"
                  @change="$refs.stdialog.save(date)"
              >
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="#008b8b"
                    @click="stdatemodal = false"
                >
                  Cancel
                </v-btn>
              </v-date-picker>
            </v-dialog>
          </v-col>
          <v-col cols="12" sm="12">
            <v-dialog
                ref="endialog"
                v-model="endatemodal"
                :return-value.sync="date"
                persistent
                width="290px"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                    v-model="form.end"
                    label="To"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    outlined
                    class="nt v-input__control2"
                    color="#008b8b"
                ></v-text-field>
              </template>
              <v-date-picker
                  v-model="form.end"
                  scrollable
                  color="#008b8b"
                  @change="$refs.endialog.save(date)"
              >
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="#008b8b"
                    @click="endatemodal = false"
                >
                  Cancel
                </v-btn>
              </v-date-picker>
            </v-dialog>
          </v-col>
          <v-col cols="12" sm="12">
            <v-btn
                large
                link
                block
                class="float-left"
                color="primary"
                style="color: white"
                elevation="2"
                @click="getApplications()"
            >
              <span><v-icon>mdi-magnify</v-icon> Search</span>
            </v-btn>
          </v-col>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import {BASE_URL, WEB_URL} from "../../config/MainURL";

let web_url = WEB_URL;
export default {
  data() {
    return {
      web_url,
      dloading: false,
      date: new Date().toLocaleDateString("fr-CA"),
      stdatemodal: false,
      endatemodal: false,
      search: "",
      dialog: false,
      as: [
        {status: 'Cleared', code: 'CL001'},
        {status: 'Not Cleared', code: 'NC001'},
        {status: 'Discharged', code: 'DS001'},
        {status: 'Cancelled', code: 'CA001'},
        {status: 'Expired', code: 'EX001'},
      ],
      form: new Form({
        start: new Date().toLocaleDateString("fr-CA"),
        end: new Date().toLocaleDateString("fr-CA"),
      }),
      headers: [
        {
          text: "#",
          value: "external_app_id",
          align: "start",
          class: "title-11",
          sortable: true,
        },
        {
          text: "FirstName",
          value: "first_name",
          class: "title-11",
          sortable: true,
        },
        {
          text: "Surname",
          value: "last_name",
          class: "title-11",
          sortable: false,
        },
        {
          text: "Service Name",
          value: "service_name",
          class: "title-11",
          sortable: false,
        },
        {
          text: "Status",
          value: "application_status",
          class: "title-11",
          sortable: false,
        },
        {
          text: "Action",
          value: "actions",
          class: "title-11",
          sortable: false,
        },
      ],
      getapplications: [],
      app_code: 'NC001',
    };
  },
  computed: {},
  methods: {
    launchDialog() {
      this.dialog = true;
    },
    getApplications() {
      let app = this;
      app.msg = "";
      app.dloading = true;
      axios
          .get(BASE_URL + "/api/auth/customer_applications", {
            params: {
              usertype: 'E',
              app_code: app.app_code,
              start: app.form.start,
              end: app.form.end,
            }
          })
          .then(function (response) {
            setTimeout(() => {
              app.getapplications = response.data;
              app.dialog = false;
              app.dloading = false;
              // loader.hide();
              app.msg = "";
            }, 500);
          })
          .catch((e) => {
            app.dialog = false;
            app.dloading = false;
            //   loader.hide();
            console.log(e);
          });
    },
  },
  mounted() {
  },
  created() {
    this.getApplications();
  },
};
</script>
