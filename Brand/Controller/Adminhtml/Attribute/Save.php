<?php


namespace Codilar\Brand\Controller\Adminhtml\Attribute;


use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Uploader;
use Mageplaza\Shopbybrand\Helper\Data as BrandHelper;

class Save extends \Mageplaza\Shopbybrand\Controller\Adminhtml\Attribute\Save
{
    protected function _uploadImage(&$data, &$result)
    {
        $this->uploadImageFields($data, $result, 'image');
        $this->uploadImageFields($data, $result, 'banner_image');
        return $this;
    }

    protected function uploadImageFields(&$data, &$result, $field)
    {
        if (isset($data[$field]['delete']) && $data[$field]['delete']) {
            $data[$field] = '';
        } else {
            try {
                $uploader = $this->_objectManager->create(
                    \Magento\MediaStorage\Model\File\Uploader::class,
                    ['fileId' => $field]
                );
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);

                $uploadPath = BrandHelper::BRAND_MEDIA_PATH . '/' . $field;

                $image = $uploader->save(
                    $this->_fileSystem->getDirectoryRead(DirectoryList::MEDIA)
                        ->getAbsolutePath($uploadPath)
                );

                $data[$field] = $uploadPath. '/' . $image['file'];
                $this->resizeImage($data[$field], 80);
            } catch (\Exception $e) {
                $data[$field] = isset($data[$field]['value']) ? $data[$field]['value'] : '';
                if ((int)$e->getCode() !== Uploader::TMP_NAME_EMPTY) {
                    $result['success'] = false;
                    $result['message'] = $e->getMessage();
                }
            }
        }
    }
}
