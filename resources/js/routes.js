import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '@/js/stores';

import Home from '@/js/components/Home';
import DashBoard from '../js/components/DashBoard.vue';
import About from '@/js/components/About';
import View from '@/js/views/View';
import Login from '@/js/pages/LoginPage';
import Client from '../js/views/clients/Clients.vue';
import Loan from '../js/views/loans/Loans';
import Payment from '../js/views/payments/Payments.vue';
import Details from '@/js/views/payments/PaymentsDetails.vue';

Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '',
            component: View,
            children: [
                {
                    path: '/',
                    name: 'home',
                    component: Home
                },
                {
                    path: '/dashboard',
                    name: 'dashboard',
                    component: DashBoard
                },
                {
                    path: '/about',
                    name: 'about',
                    component: About
                },
                {
                    path: '/clients',
                    name: 'clients',
                    component: Client
                },
                {
                    path: '/loans',
                    name: 'loans',
                    component: Loan
                },
                {
                    path: '/payments',
                    name: 'payments',
                    component: Payment
                },
                {
                    path: '/payments/:id',
                    name: 'details',
                    component: Details
                }
            ]
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        }
    ]
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = store.state.isAuthenticated;
    if (to.name !== 'login' && !isAuthenticated) next({ name: 'login'});
    else if (to.name === 'login' && isAuthenticated) next({ name: 'home' });
    else next();
});

export default router;
