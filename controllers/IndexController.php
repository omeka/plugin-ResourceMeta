<?php
class ResourceMeta_IndexController extends Omeka_Controller_AbstractActionController
{

    public function indexAction()
    {
        $db = $this->_helper->db;
        $elementSets = $db->getTable('ElementSet')->findAll();
        $elementMetaNames = $db->getTable('ResourceMeta_ElementMetaName')->getElementMetaNamesByElementSet();

        $this->view->element_sets = $elementSets;
        $this->view->element_meta_names = $elementMetaNames;
    }

    public function editAction()
    {
        $db = $this->_helper->db;
        $elementSetTable = $db->getTable('ElementSet');
        $metaNameTable = $db->getTable('ResourceMeta_MetaName');
        $elementMetaNameTable = $db->getTable('ResourceMeta_ElementMetaName');

        $elementSet = $elementSetTable->find($this->getParam('id'));
        if (!$elementSet) {
            $this->_helper->redirector('index');
        }

        if ($this->getRequest()->isPost()) {
            $postElementMetaNames = $this->getRequest()->getPost('element_meta_names', []);
            $elementMetaNameTable->setElementMetaNamesByElementSet($elementSet->id, $postElementMetaNames);
            $this->_helper->flashMessenger(sprintf(__('The "%s" meta names were was successfully saved!'), $elementSet->name), 'success');
            $this->_helper->redirector('index');
        }

        $metaNames = $metaNameTable->getMetaNames();
        $elementMetaNames = $elementMetaNameTable->getElementMetaNamesByElement(['element_set_id' => $elementSet->id]);

        $this->view->element_set = $elementSet;
        $this->view->meta_names = $metaNames;
        $this->view->element_meta_names = $elementMetaNames;
    }
}
