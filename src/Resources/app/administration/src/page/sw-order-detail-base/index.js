import {Component} from 'src/core/shopware';
import template from './sw-order-detail-base.html.twig';
import Criteria from 'src/core/data-new/criteria.data';

Component.override('sw-order-detail-base', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            customerRepository: null,
            customerId: null,
            customer: null,
            orderCustomerEmail: null,

            order: null,
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
            customerEmails: null,
            currentEmail: null,
            buttonDisabled: false,
            emailsToShow: null,
            groupPage: 1,
            limit: 10,
        };
    },

    created() {
        this.orderId = this.$route.params.id;
        this.orderRepository = this.repositoryFactory.create('order');

        this.orderRepository
            .get(this.orderId, Shopware.Context.api)
            .then((result) => {
                this.orderCustomerId = result.orderCustomer.customerId;
                this.emailRepository = this.repositoryFactory.create('blauband_email_logged_mail');
                this.emailRepository
                    .search(
                        (new Criteria()).addFilter(Criteria.equals('customer', this.orderCustomerId)),
                        Shopware.Context.api
                    )
                    .then((result) => {
                        this.customerEmails = result;
                        this.emailsToShow = this.customerEmails.slice(0, this.limit);

                        this.currentEmail = this.customerEmails[0];
                        this.buttonDisabled = this.currentEmail.id;
                    });
            });
    },
    methods: {
        onOpenModal(email) {
            this.currentEmail = email;
        },

        onCloseModal() {
            this.currentEmail = false;
        },

        showEmail(email){
            this.currentEmail = email;
            this.buttonDisabled = this.currentEmail.id;
        },

        isButtonDisabled(email) {
            return email && this.buttonDisabled ===  email.id;
        },

        onGroupPageChange(pagination) {
            this.groupPage = pagination.page;
            this.emailsToShow = this.customerEmails.slice((pagination.page - 1) * pagination.limit,
                (pagination.page) * pagination.limit);
        },
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },
});
