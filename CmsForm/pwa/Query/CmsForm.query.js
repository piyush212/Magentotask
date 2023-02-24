import { Field } from 'Util/Query';

export class CmsFormQuery {
    buildQuery(name, args, successMessage, errorMessage) {
        const query = new Field('cmsFormDataSubmit')
            .addFieldList(['status', 'message'])
            .setAlias('response');
        const formData = [];
        Object.keys(args).forEach(argument => {
            formData.push({
                key: argument,
                value: args[argument]
            });
        });
        query.addArgument('formName', 'String', name);
        query.addArgument('successMessage', 'String', successMessage);
        query.addArgument('errorMessage', 'String', errorMessage);
        query.addArgument('formData', 'CmsFormData', {
            items: formData
        });
        return query;
    }
}
export default new CmsFormQuery();
