import { Field } from 'Util/Query';

export class Query {

    getCreateRazorpayOrderMutation(orderToken) {
        return new Field('createRazorpayOrder')
            .setAlias('response')
            .addArgument('orderToken', 'String!', orderToken)
            .addFieldList([
                'key',
                'order_id',
                new Field('prefill').addFieldList([
                    'name',
                    'email',
                    'contact'
                ]),
                new Field('theme').addFieldList([
                    'color'
                ])
            ]);
    }

}

export default new Query();
