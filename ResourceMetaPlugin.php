<?php
class ResourceMetaPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install',
        'uninstall',
        'define_acl',
        'define_routes',
        'public_head',
    );

    protected $_filters = array(
        'admin_navigation_main',
    );

    public function hookInstall()
    {
        // @todo: Install data model
    }

    public function hookUninstall()
    {
        // @todo: Uninstall data model
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
                array(
                    'module' => 'resource-meta',
                    'controller' => 'index',
                    'action' => 'edit'
                )
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
        $id = $request->getParam('id');
        $db = get_db();
        $elementTexts = $db->getTable($tableName)->find($id)->getAllElementTexts();
        foreach ($elementTexts as $elementText) {
            // @todo: Add meta tags of this resource (item, file, collection)
            // ex. echo '<meta name="foo" content="bar">';
        }
    }

    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Resource Meta'),
            'uri' => url('resource-meta'),
            'resource' => ('ResourceMeta_Index'),
        );
        return $nav;
    }
}
