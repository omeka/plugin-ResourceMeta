<?php
class ResourceMetaPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = [
        'install',
        'uninstall',
        'after_delete_element',
        'define_acl',
        'define_routes',
        'admin_head',
        'public_head',
    ];

    protected $_filters = [
        'admin_navigation_main',
    ];

    /**
     * Install ResourceMeta.
     */
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

    /**
     * Uninstall ResourceMeta.
     */
    public function hookUninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `$db->ResourceMeta_ElementMetaName`";
        $db->query($sql);
    }

    /**
     * Delete ElementMetaNames when an element is deleted.
     *
     * @param array $args
     */
    public function hookAfterDeleteElement($args)
    {
        $element = $args['record'];
        $db = $this->_db;
        $sql = "DELETE FROM $db->ResourceMeta_ElementMetaName WHERE element_id = ?";
        $db->query($sql, $element->id);
    }

    /**
     * Add ResourceMeta ACL resource.
     *
     * @param array $args
     */
    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $acl->addResource('ResourceMeta_Index');
    }

    /**
     * Add ResourceMeta route.
     *
     * @param array $args
     */
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

    /**
     * Queue assets.
     *
     * @param array $args
     */
    public function hookAdminHead($args)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if (!('resource-meta' === $module && 'index' === $controller && 'edit' === $action)) {
            return;
        }
        queue_css_file('chosen.min');
        queue_js_file('chosen.jquery.min');
    }

    /**
     * Add meta tags to public resource pages (items, files, collections).
     *
     * @param array $args
     */
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
            case 'items':
                $tableName = 'Item';
                break;
            case 'files':
                $tableName = 'File';
                break;
            case 'collections':
                $tableName = 'Collection';
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

    /**
     * Add ResourceMeta navigation link.
     *
     * @param array $nav
     */
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
