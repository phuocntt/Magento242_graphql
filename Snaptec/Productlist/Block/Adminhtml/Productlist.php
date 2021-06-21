<?php

namespace Snaptec\Productlist\Block\Adminhtml;

class Productlist extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_controller     = 'adminhtml_productlist';
        $this->_blockGroup     = 'Snaptec_Productlist';
        $this->_headerText     = __('Product List');
        $this->_addButtonLabel = __('Add Product List');
        parent::_construct();
//        $this->buttonList->update('add', 'label', __('Add Product List'));
    }

}
