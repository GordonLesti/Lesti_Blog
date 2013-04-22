<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 22.04.13
 * Time: 22:48
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Adminhtml_Author_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        /* @var $model Lesti_Blog_Model_Author */
        $model = Mage::registry('blog_author');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }


        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('author_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('blog')->__('Author Information')));


        $fieldset->addField('author_name', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('blog')->__('Author Name'),
            'title'     => Mage::helper('blog')->__('Author Name'),
            'required'  => true,
            'disabled'  => $isElementDisabled
        ));

        Mage::dispatchEvent('adminhtml_blog_author_tab_main_prepare_form', array('form' => $form));

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
        return Mage::helper('blog')->__('Author Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blog')->__('Author Information');
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
        return Mage::getSingleton('admin/session')->isAllowed('blog/author/' . $action);
    }
}