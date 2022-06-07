<template>
  <section>
    <div class="row">
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between p-md-1">
              <div class="d-flex flex-row">
                <div class="align-self-center">
                  <h2 class="mb-0 me-4 title-22">{{totalapplications}}</h2>
                </div>
              </div>
              <div class="align-self-center">
                <i class="fas fa-file text-primary fa-2x"></i>
              </div>
            </div>
            <div>
              <p class="mb-0 title-15">Applications</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between p-md-1">
              <div class="d-flex flex-row">
                <div class="align-self-center">
                  <h2 class="mb-0 me-4 title-22">{{notcleared}}</h2>
                </div>
              </div>
              <div class="align-self-center">
                <i class="far fa-file text-danger fa-2x"></i>
              </div>
            </div>
            <div>
              <p class="mb-0 title-15">Pending Applications</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between p-md-1">
              <div class="d-flex flex-row">
                <div class="align-self-center">
                  <h2 class="mb-0 me-4 title-22">{{cleared}}</h2>
                </div>
              </div>
              <div class="align-self-center">
                <i class="fas fa-file text-success fa-2x"></i>
              </div>
            </div>
            <div>
              <p class="mb-0 title-15">Cleared Applications</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between p-md-1">
              <div class="d-flex flex-row">
                <div class="align-self-center">
                  <h2 class="mb-0 me-4 title-22">{{discharged}}</h2>
                </div>
              </div>
              <div class="align-self-center">
                <i class="far fa-file text-danger fa-2x"></i>
              </div>
            </div>
            <div>
              <p class="mb-0 title-15">Discharged Applications</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <br/><br/>
    <!--    <zingchart :data="myData" :series="mySeries" class="card"></zingchart>-->
    <div class="card">
      <div class="card-header title-17" style="background-color: #20778b;">
        Total Collections: TZS 80,000
      </div>
      <div class="card-body">
        <v-frappe-chart
            type="bar"
            :height="500"
            :labels="['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']"
            :data="[
        { values: [20000, 10000, 30000, 35, 8, 52, 17, 200, 900, 100, 200, 3000] }
    ]"
            :colors="['light-blue']"
        />
      </div>
    </div>
  </section>
</template>

<script>
import {BASE_URL} from "../../config/MainURL";

export default {
  data() {
    return {
      myData: {
        type: 'line',
        title: {
          text: 'Total Collections',
        },
      },
      mySeries: [
        {values: [1, 2, 4, 5, 6]},
      ],
      totalapplications: null,
      cleared: '',
      notcleared: '',
      discharged: ''
    };
  },
  computed: {},
  components: {
  },
  methods: {
    getDashboardStats() {
      let app = this;
      axios
          .get(BASE_URL + "/api/auth/applications_stats")
          .then(function (response) {
            app.totalapplications = response.data[0].totalapplications;
            app.cleared = response.data[0].cleared;
            app.notcleared = response.data[0].notcleared
            app.discharged = response.data[0].discharged;
          })
          .catch((e) => {
            console.log(e);
          });
    },
  },
  mounted() {
  },
  created() {
    this.getDashboardStats();
  },
};
</script>
