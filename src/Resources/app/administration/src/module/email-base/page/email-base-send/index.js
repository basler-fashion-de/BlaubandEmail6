const { Application, Component } = Shopware;
import template from './email-base-send.html.twig';
import Criteria from 'src/core/data-new/criteria.data';

Component.register('email-base-send', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Shopware.Mixin.getByName('notification')
    ],

    data() {
        return {
            customerId: null,
            customer: null,

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

            httpClient: null
        };
    },

    created() {
        const initContainer = Application.getContainer('init');
        this.httpClient = initContainer.httpClient;

        this.customerId = this.$route.params.id;

        this.repositoryFactory.create('customer')
            .get(this.customerId, Shopware.Context.api)
            .then((customer) => {
                this.customer = customer;
                this.mail.senderName = customer.firstName + ' ' + customer.lastName;
                this.mail.to = customer.email;
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

            var headers = {
                Accept: 'application/vnd.api+json',
                Authorization: `Bearer ${Shopware.Context.api.authToken.access}`,
                'Content-Type': 'application/json'
            };

            this.httpClient
                .post('/_action/mail-template/send', this.mail, { headers })
                .then((response) => {
                    this.createNotificationSuccess({
                        title: 'EKS',
                        message: 'Email wurde versendet'
                    });
                });
        },
    }
});
