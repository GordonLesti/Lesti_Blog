<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 28.03.13
 * Time: 14:30
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Adminhtml_Blog_PostController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Init actions
     *
     * @return Lesti_Blog_Adminhtml_Blog_PostController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('blog/post')
            ->_addBreadcrumb(Mage::helper('blog')->__('Blog'), Mage::helper('blog')->__('Post'))
            ->_addBreadcrumb(Mage::helper('blog')->__('Manage Posts'), Mage::helper('blog')->__('Manage Posts'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Posts'))
            ->_title($this->__('Manage Posts'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new post
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit post
     */
    public function editAction()
    {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Post'))
            ->_title($this->__('Manage Posts'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('post_id');
        $model = Mage::getModel('blog/post');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blog')->__('This post no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Post'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('blog_post', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
            $id ? Mage::helper('blog')->__('Edit Post')
                : Mage::helper('blog')->__('New Post'),
            $id ? Mage::helper('blog')->__('Edit Post')
                : Mage::helper('blog')->__('New Post'));

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
            $model = Mage::getModel('blog/post');

            if ($id = $this->getRequest()->getParam('post_id')) {
                $model->load($id);
            }

            if(!isset($data['author_id'])) {
                $data['author_id'] = (int) Mage::getSingleton('admin/session')->getUser()->getUserId();
            }

            $model->setData($data);

            Mage::dispatchEvent('blog_post_prepare_save', array('post' => $model, 'request' => $this->getRequest()));


            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blog')->__('The post has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('post_id' => $model->getId(), '_current'=>true));
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
                    Mage::helper('blog')->__('An error occurred while saving the post.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('post_id' => $this->getRequest()->getParam('post_id')));
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
        if ($id = $this->getRequest()->getParam('post_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('blog/post');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('cms')->__('The post has been deleted.'));
                // go to grid
                Mage::dispatchEvent('adminhtml_blog_post_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('adminhtml_blog_post_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('post_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Unable to find a post to delete.'));
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
                return Mage::getSingleton('admin/session')->isAllowed('blog/post/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('blog/post/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('blog/post');
                break;
        }
    }

}