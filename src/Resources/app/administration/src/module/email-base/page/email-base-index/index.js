import {Component} from 'src/core/shopware';
import template from './email-base-index.html.twig';
import Criteria from 'src/core/data-new/criteria.data';

Component.register('email-base-index', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            customerRepository: null,
            customerId: null,
            customer: null,

            emailRepository: null,
            emailColumns: [{
                property: 'createdAt',
                label: 'Datum'
            }, {
                property: 'subject',
                label: 'Betreff',
            }, {
                property: 'bodyPlain',
                label: 'Inhalt',
                rawData: false
            }],
            emails: null
        };
    },

    created() {
        this.customerId = this.$route.params.id;
        this.customerRepository = this.repositoryFactory.create('customer');

        this.customerRepository
            .get(this.customerId, Shopware.Context.api)
            .then((result) => {
                this.customer = result;
            });


        this.emailRepository = this.repositoryFactory.create('blauband_email_logged_mail');
        this.emailRepository
            .search(
                (new Criteria()).addFilter(Criteria.equals('customer', this.customerId)),
                Shopware.Context.api
            )
            .then((result) => {
                this.emails = result;
            });
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },
});
