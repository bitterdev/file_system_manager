<?php /** @noinspection PhpDeprecationInspection */

namespace Bitter\FileSystemManager\Provider;

use Bitter\FileSystemManager\RouteList;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Asset\AssetInterface;
use Concrete\Core\Entity\Package;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Routing\RouterInterface;

class ServiceProvider implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected RouterInterface $router;
    protected ResponseFactory $responseFactory;
    protected Package $pkg;

    public function __construct(
        PackageService  $packageService,
        ResponseFactory $responseFactory,
        RouterInterface $router
    )
    {
        $this->router = $router;
        $this->pkg = $packageService->getByHandle("file_system_manager");
        $this->responseFactory = $responseFactory;
    }

    public function register()
    {
        /** @var AssetList $al */
        $al = AssetList::getInstance();

        $al->register(
            "javascript",
            "bitter/file_system_manager",
            "js/file-system-manager.js",
            [
                "position" => AssetInterface::ASSET_POSITION_FOOTER
            ],
            $this->pkg
        );

        $al->registerGroup("bitter/file_system_manager", [
            ["javascript", "jquery"],
            ["javascript", "underscore"],
            ["javascript", "bitter/file_system_manager"]
        ]);

        /*
         * Register routes for dialogs
         */

        $this->router->register('/bitter/file_system_manager/dialogs/move', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Move::view');
        $this->router->register('/bitter/file_system_manager/dialogs/move/move', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Move::move');
        $this->router->register('/bitter/file_system_manager/dialogs/move/get_items', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Move::getItems');

        $this->router->register('/bitter/file_system_manager/dialogs/copy', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Copy::view');
        $this->router->register('/bitter/file_system_manager/dialogs/copy/copy', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Copy::copy');
        $this->router->register('/bitter/file_system_manager/dialogs/copy/get_items', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Copy::getItems');

        $this->router->register('/bitter/file_system_manager/dialogs/edit', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Edit::view');
        $this->router->register('/bitter/file_system_manager/dialogs/edit/submit', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Edit::submit');

        $this->router->register('/bitter/file_system_manager/dialogs/upload', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Upload::view');
        $this->router->register('/bitter/file_system_manager/dialogs/upload/upload', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Upload::upload');

        $this->router->register('/bitter/file_system_manager/dialogs/rename', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Rename::view');
        $this->router->register('/bitter/file_system_manager/dialogs/rename/submit', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Rename::submit');

        $this->router->register('/bitter/file_system_manager/dialogs/create_dir', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\CreateDir::view');
        $this->router->register('/bitter/file_system_manager/dialogs/create_dir/submit', '\Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\CreateDir::submit');


        $list = new RouteList();
        $list->loadRoutes($this->router);
    }

}
