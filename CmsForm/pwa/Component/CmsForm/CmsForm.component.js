import Form from 'Component/Form';
import Loader from "Component/Loader";
import './CmsForm.style.scss';

export class CmsFormComponent extends PureComponent {

    render() {
        const { isLoading, onSubmit, children } = this.props;
        return (
            <div className='Codilar_CmsForm-CmsForm'>
                <Loader isLoading={ isLoading } />
                <Form
                    onSubmitSuccess={ onSubmit }
                >
                    { children }
                </Form>
            </div>
        )
    }

}

export default CmsFormComponent;
