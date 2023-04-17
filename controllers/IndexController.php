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
            $elementMetaNames = $this->getRequest()->getPost('element_meta_names');
            foreach ($elementMetaNames as $key => $value) {
                $elementMetaName = $elementMetaNameTable->findBy(['element_id' => $key]);
                // @todo: Set new meta names (in JSON) and save
                // @todo: Mark ID as retained
            }
            // @todo: Delete all element meta names of this element set that were not retained
            $this->_helper->flashMessenger(__('The meta names were was successfully saved!'), 'success');
            $this->_helper->redirector('index');
        }

        $this->view->element_set = $elementSet;
        $this->view->meta_names = $metaNameTable->getMetaNames();
        $this->view->element_meta_names = $elementMetaNameTable->getElementMetaNames($elementSet->id);
    }
}
