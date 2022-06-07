import Vue from "vue";
import VueRouter from "vue-router";
Vue.use(VueRouter);

//Bills
import CreateBill from "./components/bills/createbill";


//Applications
import ApplicationIndex from "./components/applications/applicationindex";
import BillClearance from "./components/clearance/bill_clearance";


//Stuffing & Destuffing
import Destuffing from "./components/destuffing/destuffingindex";
import CreateDestuffingBill from "./components/destuffing/createdestuffingbill";

const routes = [
    {
        path: "/customer_applications",
        name: "apps",
        component: ApplicationIndex,
    },
    {
        path: "/destuffing",
        name: "destuffing",
        component: Destuffing,
    },
    {
        path: "/bill_clearance",
        name: "billclearance",
        component: BillClearance
    },
    {
        path: "/destuffing/create_bill",
        name: "createdestuffingbill",
        component: CreateDestuffingBill,
    },
    {
        path: "/create_bill",
        name: "createbill",
        component: CreateBill
    },
];

const router = new VueRouter({
    mode: "history",
    linkActiveClass: "active",
    linkExactActiveClass: "exact-active",
    routes // short for `routes: routes`
});


export default router;
