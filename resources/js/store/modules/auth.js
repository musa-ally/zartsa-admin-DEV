import axios from "axios";
import createPersistedState from "vuex-persistedstate";
import { BASE_URL } from "./../../config/MainURL";

const auth = {
    namespaced: true,
    state: {
        isLoggedIn: !!localStorage.getItem("jwt"),
        user: [],
    },

    plugins: [createPersistedState()],

    getters: {
        isLoggedIn: state => {
            return state.isLoggedIn;
        },

        user: state => {
            return state.user;
        }
    },

    actions: {
        LOAD_USER_DATA: function ({ commit }) {
            const token = localStorage.getItem("jwt");
            axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
            axios
                .get(BASE_URL + "/user", {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                })
                .then(
                    response => {
                        commit("SET_USER", response.data);
                    },
                    err => {
                        console.log(err);
                    }
                );
        },


    },

    mutations: {
        isLoggedIn(state) {
            state.isLoggedIn = true;
        },

        logoutUser(state) {
            state.isLoggedIn = false;
        },

        SET_USER(state, user) {
            state.user = user;
        }
    }
};

export default auth;