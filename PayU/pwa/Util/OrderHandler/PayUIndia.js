import { fetchMutation } from "Util/Request";
import PayUIndiaQuery from "NexPWA_PayUIndia/Query";

export class PayUIndia {

    state = {}

    /**
     * method for setting the states
     *
     * @param state @type {}
     */
    setState(state) {
        this.state = {
            ...this.state,
            ...state
        };
    }

    /**
     *
     * @param event
     */
    backhandle = (event) => {
        const { overlay, history:reactHistory } = this.state;
        if(overlay) {
            overlay.remove();
            reactHistory.push('/order-confirmation')
        } else {
            history.back();
        }
        window.removeEventListener('popstate', this.backhandle)


    }

    /**
     * @param order
     * @param props
     */
    async handle({ order_token }, props) {
        const { history } = props;
        window.codilar._isRedirecting = true;
        this.setState({history})
        this.generatePageOverlay(props);
        const mutation = PayUIndiaQuery.getGeneratePayUIndiaPaymentDataMutation(order_token);
        const { generatePayUIndiaPaymentData: response } = await fetchMutation([mutation]);
        const form = this.generateForm(response.action, 'POST', response.fields);
        form.submit();
    }

    /**
     *
     * @param props
     */
    generatePageOverlay(props) {
        const overlay = document.createElement('div');
        overlay.style.position = 'fixed';
        overlay.style.background = '#000';
        overlay.style.width = '100vw';
        overlay.style.height = '100vh';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.zIndex = '1000';
        overlay.style.opacity = '0.85';
        const messageContainer = document.createElement('div');
        messageContainer.style.fontSize = '2em';
        messageContainer.style.color = '#f0f0f0';
        messageContainer.style.textAlign = 'center';
        messageContainer.style.marginTop = 'calc(25vh)';
        const message = document.createElement('span');
        message.innerText = __('Loading the payment page. Please do not refresh or close the browser');
        messageContainer.appendChild(message);
        overlay.appendChild(messageContainer);
        document.body.appendChild(overlay);
        this.setState({overlay:overlay});
        overlay.addEventListener('click', ()=>{
            const { history:{ location: { pathname } = {} } = {} } = props;
            if(pathname !== '/checkout') {
                overlay.remove()
            }
        })
        window.addEventListener('popstate', this.backhandle)
    }

    generateForm(action, method, fields) {
        const form = document.createElement('form');
        form.setAttribute('action', action);
        form.setAttribute('method', method);
        form.style.display = 'none';
        fields.forEach(({ name, value }) => {
            const field = document.createElement('input');
            field.setAttribute('type', 'hidden');
            field.setAttribute('name', name);
            field.setAttribute('value', value);
            form.appendChild(field);
        });
        document.body.appendChild(form);
        return form;
    }

}

export default new PayUIndia();
