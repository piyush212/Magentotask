import { connect } from 'react-redux';
import { fetchMutation } from 'Util/Request';
import CmsFormQuery from 'Codilar_CmsForm/Query/CmsForm.query';
import CmsFormComponent from './CmsForm.component';
import { showNotification } from "Store/Notification";

export const mapDispatchToProps = dispatch => ({
    showNotification: (status, message) => dispatch(showNotification(status, message))
});

export class CmsFormContainer extends PureComponent {

    state = {
        isLoading: false
    }

    containerFunctions = {
        onSubmit: this.onSubmit.bind(this)
    }

    handleResponse(status, message) {
        const { showNotification } = this.props;
        if (status) {
            showNotification('success', message);
        } else {
            showNotification('error', message);
        }
    }

    async onSubmit() {
        const target = event.target;
        const formData = new FormData(target);
        const { name: formName, success_message, error_message } = this.props;
        const args = {};
        for (let [key, value] of formData.entries()) {
            args[key] = value;
        }
        this.setState({ isLoading: true });
        const query = CmsFormQuery.buildQuery(formName, args, success_message, error_message);
        try {
            const { response: { status = false, message } = {} } = await fetchMutation(query);
            this.handleResponse(status, message);
            if (status) {
                target.reset();
            }
        } catch (e) {
            this.handleResponse(false, e.message);
        }
        this.setState({ isLoading: false });
    }

    render() {
        return (
            <CmsFormComponent
                { ...this.props }
                { ...this.state }
                { ...this.containerFunctions }
            />
        )
    }

}

export default connect(null, mapDispatchToProps)(CmsFormContainer);
