import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import store from '../store/index';
import App from './App.vue';
import License from './components/pages/License.vue';
import GeneralTab from './components/tabs/GeneralTab.vue';
import TabNavigation from './components/tabs/Navigation.vue';
import OTPTab from './components/tabs/OTPTab.vue';

// Define routes
const routes = [
    {
        path: '/',
        components: { default: GeneralTab, tab: TabNavigation },
    },
    {
        path: '/otp',
        components: { default: OTPTab, tab: TabNavigation },
    },
    {
        path: '/license',
        components: { default: License },
    },
];

// Create the router
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Create the app
createApp(App)
    .use(store)
    .use(router)
    .mount('#order-shield-admin-app');
