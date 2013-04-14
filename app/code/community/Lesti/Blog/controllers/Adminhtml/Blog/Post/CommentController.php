<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 14.04.13
 * Time: 16:02
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Adminhtml_Blog_Post_CommentController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Init actions
     *
     * @return Lesti_Blog_Adminhtml_Blog_Post_CommentController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('blog/comment')
            ->_addBreadcrumb(Mage::helper('blog')->__('Comment'), Mage::helper('blog')->__('Comment'))
            ->_addBreadcrumb(Mage::helper('blog')->__('Manage Comments'), Mage::helper('blog')->__('Manage Comments'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Comments'))
            ->_title($this->__('Manage Comments'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Edit comment
     */
    public function editAction()
    {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Comment'))
            ->_title($this->__('Manage Comments'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('comment_id');
        $model = Mage::getModel('blog/post_comment');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blog')->__('This comment no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($this->__('Comment'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('blog_post_comment', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
            $id ? Mage::helper('blog')->__('Edit Comment')
                : Mage::helper('blog')->__('New Comment'),
            $id ? Mage::helper('blog')->__('Edit Comment')
                : Mage::helper('blog')->__('New Comment'));

        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            //init model and set data
            $model = Mage::getModel('blog/post_comment');

            if ($id = $this->getRequest()->getParam('comment_id')) {
                $model->load($id);
            }
            $modelData = $model->getData();
            $modelData['status'] = $data['status'];

            $model->setData($modelData);

            Mage::dispatchEvent('blog_post_comment_prepare_save', array('comment' => $model, 'request' => $this->getRequest()));

            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blog')->__('The comment has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('comment_id' => $model->getId(), '_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('blog')->__('An error occurred while saving the comment.'));
            }

            $this->_getSession()->setFormData($modelData);
            $this->_redirect('*/*/edit', array('comment_id' => $this->getRequest()->getParam('comment_id')));
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('comment_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('blog/post_comment');
                $model->load($id);
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('cms')->__('The comment has been deleted.'));
                // go to grid
                Mage::dispatchEvent('adminhtml_blog_post_comment_on_delete', array('comment_id' => $id, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('adminhtml_blog_post_comment_on_delete', array('comment_id' => $id, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('comment_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Unable to find a comment to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'new':
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('blog/post_comment/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('blog/post_comemnt/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('blog/post_comment');
                break;
        }
    }

}