<?php

namespace Bitter\FileSystemManager;

use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class RouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router->buildGroup()->setNamespace('Concrete\Package\FileSystemManager\Controller\Dialog\Support')
            ->setPrefix('/ccm/system/dialogs/file_system_manager')
            ->routes('dialogs/support.php', 'file_system_manager');
    }
}