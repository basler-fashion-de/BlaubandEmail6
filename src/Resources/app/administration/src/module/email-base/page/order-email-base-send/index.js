import {Application, Component} from 'src/core/shopware';
import template from './order-email-base-send.html.twig';
import Criteria from 'src/core/data-new/criteria.data';

Component.register('order-email-base-send', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            customerId: null,
            customer: null,
            orderRepository: null,
            orderId: null,

            mail: {
                subject: null,
                from: null,
                to: null,
                recipients: null,
                senderName: null,
                bcc: null,
                contentHtml: null,
                contentPlain: null,
                salesChannelId: null,
                mediaIds: []
            },

            users: [],
            salesChannels: [],

            httpClient: null,
            showAlert: false
        };
    },

    created() {
        const initContainer = Application.getContainer('init');
        this.httpClient = initContainer.httpClient;
        this.orderId = this.$route.params.id;
        this.orderRepository = this.repositoryFactory.create('order');

        this.orderRepository
            .get(this.orderId, Shopware.Context.api)
            .then((result) => {
                this.customerId = result.orderCustomer.customerId;
                console.log(this.customerId);
                this.repositoryFactory.create('customer')
                    .get(this.customerId, Shopware.Context.api)
                    .then((customer) => {
                        this.customer = customer;
                        this.mail.senderName = customer.firstName + ' ' + customer.lastName;
                        this.mail.to = customer.email;
                        console.log(this.mail.to);
                    });
            });

        this.repositoryFactory.create('user')
            .search(new Criteria(), Shopware.Context.api)
            .then((users) => {
                this.users = users;
                this.mail.from = users[0];
            });

        this.repositoryFactory.create('sales_channel')
            .search(new Criteria(), Shopware.Context.api)
            .then((channels) => {
                this.salesChannels = channels;
                this.mail.salesChannelId = channels[0];
            });
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    methods: {
        sendEmail() {
            var recipients = {};
            recipients[this.mail.to] = this.mail.to;
            this.mail.recipients = recipients;
            this.mail.contentHtml = this.mail.contentPlain.replace(/(?:\r\n|\r|\n)/g, '<br/>');

            this.httpClient
                .post('/_action/mail-template/send', this.mail, {})
                .then((response) => {
                    this.showAlert = true;
                });
        },
        onCloseAlert() {
            this.showAlert = false;
        },
    }
});
