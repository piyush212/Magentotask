import { connect } from 'react-redux';
import Image from 'Component/Image';
import Link from 'Component/Link';
import './FeaturedBrandsWidget.style.scss';

const mapStateToProps = state => ({
    featuredBrands: state.BrandReducer.featuredBrands
});

export class FeaturedBrandsWidget extends PureComponent {

    renderBrandList(brands) {
        return (
            <div className='BrandList'>
                {brands.map(brand => this.renderBrandCard(brand))}
            </div>
        )
    }

    renderBrandCard(brand) {
        return (
            <Link to={ brand.url_key }>
                <div className='BrandCard'>
                    <div className='BrandImage'>
                        <Image src={brand.image} ratio='custom' />
                    </div>
                    <div className='BrandLabel'>
                        <span>{brand.title}</span>
                    </div>
                </div>
            </Link>
        )
    }

    renderBrandTitle() {
        const { title, showTitle } = this.props;

        if (!showTitle || !title) return null;

        return (
            <div className='BrandTitle'>
                <h3>{title}</h3>
            </div>
        )
    }

    render() {
        const { featuredBrands } = this.props;
        if (!featuredBrands.length) return null;
        return (
            <div className='FeaturedBrandsWidget'>
                {this.renderBrandTitle()}
                {this.renderBrandList(featuredBrands)}
            </div>
        )
    }

}

export default connect(mapStateToProps)(FeaturedBrandsWidget);
