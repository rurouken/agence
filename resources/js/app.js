import './bootstrap';
import { createApp } from "vue";
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap';
import store from './store.js';
import router from "./routes.js";

import AppComponent from './components/App.vue';

const app= createApp({
    components: {
        AppComponent
    },
});

app.use(store)

app.use(router)

app.mount('#app')
