/**
 * 应用入口:Pinia → Router → TDesign → 权限指令。
 * Author: qiufeng
 */
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import TDesign from 'tdesign-vue-next';
import App from '@/App.vue';
import router from '@/router';
import { permission } from '@/directives/permission';

import 'tdesign-vue-next/es/style/index.css';
import '@/styles/global.css';

const app = createApp(App);
app.use(createPinia());
app.use(router);
app.use(TDesign);
app.directive('permission', permission);
app.mount('#app');
