<?php


namespace Codilar\Brand\Plugin\Block\Adminhtml\Attribute\Edit;

use \Magento\Framework\Data\Form;
use Mageplaza\Shopbybrand\Block\Adminhtml\Attribute\Edit\Brand as Subject;
use Mageplaza\Shopbybrand\Model\BrandFactory;
use Mageplaza\Shopbybrand\Model\ResourceModel\Brand as BrandResource;
use Codilar\BannerSlider\Model\Config\Source\Slider;

class Brand
{
    /**
     * @var BrandFactory
     */
    private $brandFactory;
    /**
     * @var BrandResource
     */
    private $brandResource;
    /**
     * @var Slider
     */
    private $slider;

    /**
     * Brand constructor.
     * @param BrandFactory $brandFactory
     * @param BrandResource $brandResource
     * @param Slider $slider
     */
    public function __construct(
        BrandFactory $brandFactory,
        BrandResource $brandResource,
        Slider $slider
    )
    {
        $this->brandFactory = $brandFactory;
        $this->brandResource = $brandResource;
        $this->slider = $slider;
    }

    /**
     * Set form object
     *
     * @param Form $form
     * @return array
     */
    public function beforeSetForm(Subject $subject, Form $form): array
    {
        $fieldset = $form->getElement('brand_fieldset');
        $shortDescriptionField = $fieldset->getElements()->searchById('short_description');
        $shortDescriptionField->addData([
            'label' => __('Deals (HTML)'),
            'title' => __('Deals (HTML)'),
            'style' => 'min-height: 300px'
        ]);
        $descriptionField = $fieldset->getElements()->searchById('description');
        $descriptionField->addData([
            'label' => __('Brand Story (HTML)'),
            'title' => __('Brand Story (HTML)'),
            'style' => 'min-height: 300px'
        ]);
        $fieldset->addField('is_enabled', 'select', [
            'name' => 'is_enabled',
            'label' => __('Is Enabled'),
            'title' => __('Is Enabled'),
            'values' => ['1' => __('Yes'), '0' => __('No')]
        ], '^');
        $fieldset->addField('banner_image', 'image', [
            'name' => 'banner_image',
            'label' => __('Banner Image'),
            'title' => __('Banner Image')
        ], 'image');
        $fieldset->addField('slider_id', 'select', [
            'name' => 'slider_id',
            'label' => __('Slider'),
            'title' => __('Slider'),
            'values' => $this->getSliders()
        ], 'banner_image');
        $fieldset->addField('category_ids', 'text', [
            'name' => 'category_ids',
            'label' => __('Category IDs (comma separated)'),
            'title' => __('Category IDs (comma separated)')
        ], 'slider_id');
        $fieldset->addField('product_ids', 'text', [
            'name' => 'product_ids',
            'label' => __('Product IDs (comma separated)'),
            'title' => __('Product IDs (comma separated)')
        ], 'category_ids');
        $fieldset->addField('fb_pixel_id', 'text', [
            'name' => 'fb_pixel_id',
            'label' => __('Facebook Pixel ID'),
            'title' => __('Facebook Pixel ID')
        ], 'product_ids');
        $fieldset->addField('primary_color', 'text', [
            'name' => 'primary_color',
            'label' => __('Primary color'),
            'title' => __('Primary color'),
            'note' => __('Should be a CSS supported valid colour syntax')
        ], 'product_ids');
        $fieldset->addField('secondary_color', 'text', [
            'name' => 'secondary_color',
            'label' => __('Secondary color'),
            'title' => __('Secondary color'),
            'note' => __('Should be a CSS supported valid colour syntax')
        ], 'primary_color');

        $form->addValues($this->getValues($subject->getOptionData()['brand_id'] ?? []));
        return [$form];
    }

    /**
     * @return array
     */
    protected function getSliders(): array
    {
        $sliders = $this->slider->toOptionArray();
        $response = [
            '' => ''
        ];
        foreach ($sliders as $slider) {
            $response[$slider['value']] = $slider['label'];
        }
        return $response;
    }

    /**
     * @param int $brandId
     * @return array
     */
    protected function getValues($brandId): array
    {
        $brand = $this->brandFactory->create();
        $this->brandResource->load($brand, $brandId);
        return [
            'is_enabled' => $brand->getData('is_enabled'),
            'category_ids' => $brand->getData('category_ids'),
            'product_ids' => $brand->getData('product_ids'),
            'slider_id' => $brand->getData('slider_id'),
            'fb_pixel_id' => $brand->getData('fb_pixel_id'),
            'primary_color' => $brand->getData('primary_color') ?: '#fff',
            'secondary_color' => $brand->getData('secondary_color') ?: '#000',
            'banner_image' => $brand->getData('banner_image')
        ];
    }
}
