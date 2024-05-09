import "./bootstrap";
import { createApp } from 'vue/dist/vue.esm-bundler.js';
import vuetify from "./vuetify";
import axios from 'axios';



const componentContext = import.meta.glob('./components/**/*.vue',{ eager: true });

const app = createApp({});

for (const path in componentContext) {
    const componentModule = componentContext[path];
    const fileName = path.split('/').pop().replace(/\.\w+$/, '');
  
    const component = componentModule.default;
    app.component(fileName, component);
  };
  
app.use(vuetify).mount("#app");
