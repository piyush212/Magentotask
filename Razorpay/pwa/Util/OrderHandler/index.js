import { Suspense, lazy } from 'react';
import { fetchMutation } from "Util/Request";
import RazorpayQuery from '../../Query/Razorpay';

const AssetImageLoader = lazy(() => import(/* webpackMode: "lazy", webpackChunkName: "codilar_razorpay" */'Component/AssetImageLoader'));

export class Index {

    async handle(orderData, props) {
        const mutation = RazorpayQuery.getCreateRazorpayOrderMutation(orderData.order_token)
        const { response } = await fetchMutation(mutation);
        const moveToOrderConfimation = () => {
            props.history.push('/order-confirmation');
        }
        const options = {
            ...response,
            handler: moveToOrderConfimation,
            modal: {
                ondismiss: moveToOrderConfimation
            }
        }
        await this.addRazorpayToPage();
        window.rzpo = options;
        const razorpay = new window.Razorpay(options);
        razorpay.open();
        window.rzp = razorpay;
    }

    render() {
        const containerStyle = {
            width: "100vw",
            height: "100vh",
            position: "fixed",
            zIndex: 100000,
            top: 0,
            left: 0,
            background: "#e9f3e7",
            display: "flex",
            alignItems: "center"
        };
        const loaderStyle = {
            width: "5rem",
            height: "auto",
            margin: "auto"
        };
        return (
            <div className='razorpay-container' style={ containerStyle }>
                <div style={ loaderStyle }>
                    <Suspense fallback={ null }>
                        <AssetImageLoader src={ 'assets/images/global/loader.svg' } no_convert={ true } />
                    </Suspense>
                </div>
            </div>
        );
    }

    addRazorpayToPage() {
        return new Promise((resolve, reject) => {
            const script = window.document.createElement('script');
            script.src = "https://checkout.razorpay.com/v1/checkout.js";
            script.id = "razorpay-script";
            script.addEventListener('load', () => resolve());
            window.document.head.appendChild(script);
        });
    }
}

export default new Index();
