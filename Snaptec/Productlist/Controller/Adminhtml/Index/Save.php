<?php

namespace Snaptec\Productlist\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
//        var_dump('<pre>');
//        var_dump($data);
//        die('gggggggg');
        $snaptecObjectManager = $this->_objectManager;
        $model = $snaptecObjectManager->create('Snaptec\Productlist\Model\Productlist');
        $id = $this->getRequest()->getParam('productlist_id');
        if ($id) {
            $model->load($id);
        }
        $data['list_type']    = isset($data['list_type']) ? $data['list_type'] : 6;
        $data['category_id'] = isset($data['category_id'])?$data['category_id']:0;

        $is_delete_productlist = isset($data['list_image']['delete']) ? $data['list_image']['delete'] : false;
        $data['list_image']    = isset($data['list_image']['value']) ? $data['list_image']['value'] : '';
        $is_delete_productlist_tablet=isset($data['list_image_tablet']['delete'])?
                $data['list_image_tablet']['delete'] : false;
        $data['list_image_tablet'] = isset($data['list_image_tablet']['value']) ?
                $data['list_image_tablet']['value'] : '';

        $model->addData($data);

        try {
            $imageHelper = $snaptecObjectManager->get('Snaptec\Productlist\Helper\Data');
            if ($is_delete_productlist && $model->getListImage()) {
                $model->setListImage('');
            } else {
                $imageFile = $imageHelper->uploadImage('list_image', 'productlist');
                if ($imageFile) {
                    $model->setListImage($imageFile);
                }
            }
            if ($is_delete_productlist_tablet && $model->setListImageTablet()) {
                $model->setListImageTablet('');
            } else {
                $imageFiletablet = $imageHelper->uploadImage('list_image_tablet', 'productlist');
                if ($imageFiletablet) {
                    $model->setListImageTablet($imageFiletablet);
                }
            }
            $model->setData('storeview_id', null);
            $model->save();

            $this->messageManager->addSuccess(__('The Data has been saved.'));
            $snaptecObjectManager->get('Magento\Backend\Model\Session')->setFormData(false);

            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', ['productlist_id' => $model->getId(), '_current' => true]);
                return;
            }
            $this->_redirect('*/*/');
            return;
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
            $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
        }

        $this->_getSession()->setFormData($data);

        $this->_redirect('*/*/edit', ['productlist_id' => $this->getRequest()->getParam('productlist_id')]);
    }
}
