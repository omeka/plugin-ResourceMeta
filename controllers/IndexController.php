<?php
class ResourceMeta_IndexController extends Omeka_Controller_AbstractActionController
{

    public function indexAction()
    {
        $elementSets = $this->_helper->db->getTable('ElementSet')->findAll();
        $this->view->element_sets = $elementSets;
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
            $elementMetaNameTable->setElementMetaNames($elementSet->id, $this->getRequest()->getPost('element_meta_names', []));
            $this->_helper->flashMessenger(__('The meta names were was successfully saved!'), 'success');
            $this->_helper->redirector('index');
        }

        $metaNames = $metaNameTable->getMetaNames();
        $elementMetaNames = $elementMetaNameTable->getElementMetaNames($elementSet->id);

        $this->view->element_set = $elementSet;
        $this->view->meta_names = $metaNames;
        $this->view->element_meta_names = $elementMetaNames;
    }
}
