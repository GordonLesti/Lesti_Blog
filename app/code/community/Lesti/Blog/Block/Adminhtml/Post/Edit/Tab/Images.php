<?php

class Lesti_Blog_Block_Adminhtml_Post_Edit_Tab_Images extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {

        /* @var $model Lesti_Blog_Model_post */
        $model = Mage::registry('blog_post');
        
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('post_');
        
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('blog')->__('Images')));

        
        $fieldset->addField('main_image', 'image', array(
            'name' => 'main_image',
            'label' => 'Main Image',
            'title' => 'Main Image'
        ));
       
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('blog')->__('Images');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blog')->__('Images');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('blog/post/' . $action);
    }
}
