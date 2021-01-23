<?php

declare(strict_types=1);

namespace Shulgin\Headerstrip\Controller\Adminhtml\Strip;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Uploader extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Image uploader
     *
     * @var \Magento\Catalog\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * Upload constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Model\ImageUploader $imageUploader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Shulgin_Headerstrip::top_level');
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $imageId = $this->_request->getParam('param_name', 'image');

        try {
            //  $result = $this->imageUploader->saveFileToTmpDir($imageId);
            $result = $this->imageUploader->saveFileToBaseDir($this->getRequest()->getParam('input'));
            $result['cookie'] = [
                 'name' => $this->_getSession()->getName(),
                 'value' => $this->_getSession()->getSessionId(),
                 'lifetime' => $this->_getSession()->getCookieLifetime(),
                 'path' => $this->_getSession()->getCookiePath(),
                 'domain' => $this->_getSession()->getCookieDomain(),
             ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}