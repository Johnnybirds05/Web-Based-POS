import "./bootstrap";
import { createApp } from 'vue/dist/vue.esm-bundler.js';
import vuetify from "./vuetify";
import axios from 'axios';
import VueSweetalert2 from "vue-sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";



const componentContext = import.meta.glob('./components/**/*.vue',{ eager: true });

const app = createApp({});

for (const path in componentContext) {
    const componentModule = componentContext[path];
    const fileName = path.split('/').pop().replace(/\.\w+$/, '');
  
    const component = componentModule.default;
    app.component(fileName, component);
  };
  
app.use(vuetify,VueSweetalert2).mount("#app");
