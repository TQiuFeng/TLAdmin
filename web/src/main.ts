/**
 * 应用入口:Pinia → Router → 权限指令。
 * TDesign 组件由 vite.config.ts 里的 unplugin-vue-components 按需引入,这里只引全量样式(主题变量依赖它)。
 * Author: qiufeng
 */
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from '@/App.vue';
import router from '@/router';
import { permission } from '@/directives/permission';

import 'tdesign-vue-next/es/style/index.css';
import '@/styles/global.css';

const app = createApp(App);
app.use(createPinia());
app.use(router);
app.directive('permission', permission);
app.mount('#app');
