import { Module } from 'src/core/shopware';

import './page/email-base-index';
import './page/email-base-send';

Module.register('email-base', {
    type: 'plugin',
    name: 'Email (EKS)',
    title: 'Emails (EKS)',
    description: 'Blauband Emails (EKS)',
    color: '#0000bb',
    icon: 'default-shopping-paper-bag-product',

    routes: {
        index: {
            component: 'email-base-index',
            path: 'index/:id'
        },

        send: {
            component: 'email-base-send',
            path: 'send/:id',
            meta: {
                parentPath: 'email.base.index'
            }
        },
    },

    // navigation: [{
    //     label: 'Email (EKS)',
    //     color: '#0000bb',
    //     path: 'email.base.index',
    //     icon: 'default-shopping-paper-bag-product',
    //     position: -10
    // }]
});
