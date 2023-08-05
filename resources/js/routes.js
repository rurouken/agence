import { createRouter, createWebHistory } from "vue-router";

import DashboardComponent from '@/components/pages/Dashboard.vue';

const routes = [
    {
        path: '/',
        name: 'dashboard',
        component: DashboardComponent
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
