<?php

namespace Qsoft\NewsletterCustom\Plugin\Newsletter;

class YourUnsubscribe extends \Magento\Newsletter\Controller\Subscriber\Unsubscribe
{
    const UNSUBSCRIBE_PATH = 'subscription/subscriber/unsubscribe/';

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $code = (string)$this->getRequest()->getParam('code');

        if ($id && $code) {
            try {
                $this->_subscriberFactory->create()->load($id)->setCheckCode($code)->unsubscribe();
                $this->messageManager->addSuccess(__('You unsubscribed.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addException($e, $e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while unsubscribing you.'));
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirect */
        $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $redirectUrl = $this->_redirect->getRedirectUrl();

        return $redirect->setUrl($redirectUrl.self::UNSUBSCRIBE_PATH.'id/'.$id.'/code/'.$code.'/');
    }
}