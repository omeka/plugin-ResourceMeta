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
        $elementSetId = $this->getParam('id');
        $elementSet = $this->_helper->db->getTable('ElementSet')->find($elementSetId);

        // @todo: Process form, save to data model

        $this->view->element_set = $elementSet;
    }
}
