<?php
class Table_ResourceMeta_ElementMetaName extends Omeka_Db_Table
{
    /**
     * Get ElementMetaName data.
     *
     * @param ?array $params
     * @return array
     */
    public function getElementMetaNames($params = null)
    {
        if (is_array($params)) {
            $findArray = $this->findBy($params);
        } else {
            $findArray = $this->findAll();
        }
        $elementMetaNames = [];
        foreach ($findArray as $value) {
            $elementId = $value->element_id;
            $metaNames = json_decode($value->meta_names, true);
            $elementMetaNames[$elementId] = $metaNames;
        }
        return $elementMetaNames;
    }

    /**
     * Set ElementMetaName data by element set.
     *
     * @param int $elementSetId
     * @param array $elementMetaNames
     */
    public function setElementMetaNamesByElementSet($elementSetId, $elementMetaNames)
    {
        if (!(is_numeric($elementSetId) && is_array($elementMetaNames))) {
            return; // Invalid format - function arguments
        }

        $elementSetTable = $this->getTable('ElementSet');
        $elementTable = $this->getTable('Element');

        $elementSet = $elementSetTable->find($elementSetId);
        if (!$elementSet) {
            return; // Invalid element set - does not exist
        }

        // Create/update rows
        $toRetain = [0];
        foreach ($elementMetaNames as $key => $value) {
            if (!(is_numeric($key) && is_array($value))) {
                continue; // Invalid format - array key/value
            }
            foreach ($value as $valueValue) {
                if (!is_string($valueValue)) {
                    continue 2; // Invalid format - meta names must be an array of strings
                }
            }
            $element = $elementTable->find($key);
            if (!$element) {
                continue; // Invalid element - does not exist
            }
            if ($element->element_set_id !== $elementSet->id) {
                continue; // Invalid element - does not belong to element set
            }
            $elementMetaName = $this->findBy(['element_set_id' => $elementSet->id, 'element_id' => $key]);
            if ($elementMetaName) {
                $elementMetaName = $elementMetaName[0];
            } else {
                // This is a new element meta name
                $elementMetaName = new ResourceMeta_ElementMetaName;
                $elementMetaName->element_set_id = $elementSet->id;
                $elementMetaName->element_id = $element->id;
            }
            $elementMetaName->meta_names = json_encode($value);
            $elementMetaName->save();
            $toRetain[] = $elementMetaName->id;
        }

        // Delete rows not included in the request
        $db = $this->getDb();
        $sql = "
        DELETE FROM {$this->getTableName()}
        WHERE element_set_id = $elementSet->id
        AND id NOT IN (%s)";
        $this->query(sprintf($sql, implode(',', $toRetain)));
    }
}
