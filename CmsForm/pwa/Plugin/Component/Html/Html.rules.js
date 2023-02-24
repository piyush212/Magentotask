import { lazy, Suspense } from 'react';
import domToReact from 'html-react-parser/lib/dom-to-react';
import attributesToProps from 'html-react-parser/lib/attributes-to-props';

const CmsForm = lazy(() => import(/* webpackMode: "lazy", webpackChunkName: "Codilar_CmsForm" */'Codilar_CmsForm/Component/CmsForm'));

const replaceCmsForm = ({ attribs, children }, subject) => {
    attribs = subject.extractDataPropAttributes(attribs);
    return (
        <Suspense fallback={ null }>
            <CmsForm { ...attributesToProps(attribs) }>
                { domToReact(children, subject.parserOptions) }
            </CmsForm>
        </Suspense>
    );
}

class HtmlRulesPlugin {

    afterGetRules(instance, result) {
        result.push({
            query: { name: ['cms-form'] },
            replace: args => replaceCmsForm(args, instance.getSubject())
        })
        return result;
    }

}

export default new HtmlRulesPlugin();
