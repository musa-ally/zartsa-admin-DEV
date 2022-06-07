import axios from "axios";
import createPersistedState from "vuex-persistedstate";
import { BASE_URL } from "./../../config/MainURL";

const applications = {
    namespaced: true,
    state: {
        getapplications: []
    },

    plugins: [createPersistedState()],

    getters: {
        getapplications: state => {
            return state.getapplications;
        }
    },

    actions: {
        LOAD_APPLICATIONS: function({ commit }) {
            const token = localStorage.getItem("jwt");
            axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
            axios
                .get(BASE_URL + "/auth/customer_applications")
                .then(r => r.data)
                .then(
                    getapplications => {
                        commit("SET_APPLICATIONS", getapplications);
                    },
                    err => {
                        console.log(err);
                    }
                );
        }
    },

    mutations: {
        SET_APPLICATIONS(state, getapplications) {
            state.getapplications = getapplications;
        }
    }
};

export default applications;
