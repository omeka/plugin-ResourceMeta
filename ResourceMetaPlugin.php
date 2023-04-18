<?php
class ResourceMetaPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = [
        'install',
        'uninstall',
        'after_delete_element',
        'define_acl',
        'define_routes',
        'public_head',
    ];

    protected $_filters = [
        'admin_navigation_main',
    ];

    public function hookInstall()
    {
        $db = $this->_db;
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->ResourceMeta_ElementMetaName` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `element_set_id` int(10) unsigned NOT NULL ,
          `element_id` int(10) unsigned NOT NULL ,
          `meta_names` text collate utf8_unicode_ci NOT NULL,
          PRIMARY KEY  (`id`),
          KEY `element_set_id` (`element_set_id`),
          KEY `element_id` (`element_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $db->query($sql);
    }

    public function hookUninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `$db->ResourceMeta_ElementMetaName`";
        $db->query($sql);
    }

    public function hookAfterDeleteElement($args)
    {
        // Delete meta names when an element is deleted.
        $element = $args['record'];
        $db = $this->_db;
        $sql = "DELETE FROM $db->ResourceMeta_ElementMetaName WHERE element_id = ?";
        $db->query($sql, $element->id);
    }

    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $acl->addResource('ResourceMeta_Index');
    }

    public function hookDefineRoutes($args)
    {
        $router = $args['router'];
        $router->addRoute(
            'resource-meta/id',
            new Zend_Controller_Router_Route(
                'resource-meta/:id',
                [
                    'module' => 'resource-meta',
                    'controller' => 'index',
                    'action' => 'edit'
                ]
            )
        );
    }

    public function hookPublicHead($args)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if (!('default' === $module && 'show' === $action)) {
            return;
        }
        switch ($controller) {
            case 'collections':
                $tableName = 'Collection';
                break;
            case 'items':
                $tableName = 'Item';
                break;
            case 'files':
                $tableName = 'File';
                break;
            default:
                return;
        }
        $db = get_db();
        $elementMetaNames =  $db->getTable('ResourceMeta_ElementMetaName')->getElementMetaNames();
        $elementTexts = $db->getTable($tableName)->find($request->getParam('id'))->getAllElementTexts();
        foreach ($elementTexts as $elementText) {
            $elementId = $elementText->element_id;
            if (!array_key_exists($elementId, $elementMetaNames)) {
                continue;
            }
            foreach ($elementMetaNames[$elementId] as $metaName) {
                echo sprintf(
                    '<meta name="%s" content="%s">',
                    htmlspecialchars($metaName),
                    htmlspecialchars($elementText->text)
                );
            }
        }
    }

    public function filterAdminNavigationMain($nav)
    {
        $nav[] = [
            'label' => __('Resource Meta'),
            'uri' => url('resource-meta'),
            'resource' => ('ResourceMeta_Index'),
        ];
        return $nav;
    }
}
