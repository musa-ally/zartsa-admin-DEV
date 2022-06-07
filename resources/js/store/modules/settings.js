import axios from "axios";
import createPersistedState from "vuex-persistedstate";
import { BASE_URL } from "./../../config/MainURL";
import store from "../store";
import router from "../../router";

const settings = {
    namespaced: true,
    state: {
        getsystemusers: [],
    },

    plugins: [createPersistedState()],

    getters: {
        getsystemusers: state => {
            return state.getsystemusers;
        },

    },

    actions: {
        LOAD_SYSTEM_USERS: function ({ commit }) {
            const token = localStorage.getItem("jwt");
            axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
            axios
                .get(BASE_URL + "/auth/v1/system-users")
                .then(r => r.data)
                .then(
                    getsystemusers => {
                        commit("SET_SYSTEM_USERS", getsystemusers);
                    },
                    err => {
                        console.log(err);
                        if (token !== null) {
                            Swal.fire({
                                title: "Session Expired",
                                icon: "info",
                                text: "Sign-In to activate your session",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: `Sign-In`,
                                denyButtonText: `Don't save`,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                allowOutsideClick: false,
                            }).then(function (result) {
                                // Send request to the server
                                if (result.isConfirmed) {
                                    localStorage.removeItem('jwt')
                                    store.commit("auth/logoutUser");
                                    router.push({ name: 'login' })
                                } else if (result.isDenied) {
                                    //
                                }
                            });
                        }
                    }
                );
        },
    },

    mutations: {
        SET_SYSTEM_USERS(state, getsystemusers) {
            state.getsystemusers = getsystemusers;
        },
    }
};

export default settings;