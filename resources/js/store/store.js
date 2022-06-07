import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from "vuex-persistedstate";
import auth from './modules/auth';
import settings from './modules/settings';
import applications from "./modules/applications";

Vue.use(Vuex)
export default new Vuex.Store({
    strict: true,
    plugins: [createPersistedState()],
    modules: {
        // auth,
        settings,
        applications
    },
})
