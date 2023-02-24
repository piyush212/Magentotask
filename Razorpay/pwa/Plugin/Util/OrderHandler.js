import Razorpay from "Codilar_Razorpay/Util/OrderHandler";

export const METHOD_CODE = "razorpay";

class OrderHandlerPlugin {
    /**
     * Appending the Razorpay handler to the handler list
     *
     * @param instance
     * @param handlers
     * @return {*}
     */
    afterGetHandlers(instance, handlers) {
        handlers[METHOD_CODE] = Razorpay;
        return handlers;
    }
}

export default new OrderHandlerPlugin();
