<?php
class Table_ResourceMeta_ElementMetaName extends Omeka_Db_Table
{
    public function fetchElementMetaNames($elementSetId)
    {
        $db = $this->getDb();
        $sql = "
        SELECT emn.*
        FROM $db->ResourceMeta_ElementMetaNames AS emn
        JOIN $db->Element AS e ON emn.element_id = e.id
        WHERE e.element_set_id = ?";
        return $this->fetchAll($sql, $elementSetId);
    }

    public function getElementMetaNames($elementSetId)
    {
        $elementMetaNames = [];
        foreach ($this->fetchElementMetaNames($elementSetId) as $value) {
            $elementId = $value['element_id'];
            $metaNames = json_decode($value['meta_names'], true);
            $elementMetaNames[$elementId] = $metaNames;
        }
        return $elementMetaNames;
    }
}
