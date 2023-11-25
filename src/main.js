import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import VueSidebarMenu from 'vue-sidebar-menu'
import 'vue-sidebar-menu/dist/vue-sidebar-menu.css'
import ListTasks from './components/ListTasks.vue';
import AppComponent from './App.vue';
import Layout from "./components/LayoutComponent.vue";
import ToastPlugin from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css';


const app = createApp({});
app.use(VueSidebarMenu)
app.component('app-component', AppComponent);
app.component('list-tasks', ListTasks);
app.component('layout-component', Layout);

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: AppComponent },
    ],
});


app.use(router);
app.use(ToastPlugin);


app.mount('#app');
