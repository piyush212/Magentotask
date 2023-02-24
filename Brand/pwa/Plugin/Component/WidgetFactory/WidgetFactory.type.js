import { lazy } from 'react';

const FeaturedBrandsWidget = lazy(() => import(/* webpackMode: "lazy", webpackChunkName: "brand" */ 'Codilar_Brand/Component/FeaturedBrandsWidget'));

class WidgetFactoryTypePlugin {

    afterGetWidgetTypes(instance, types) {
        types['FeaturedBrands'] = {
            component: FeaturedBrandsWidget
        };
        return types;
    }

}

export default new WidgetFactoryTypePlugin();
