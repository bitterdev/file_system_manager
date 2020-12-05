<?php /** @noinspection PhpDeprecationInspection */

/**
 * @project:   File Manager
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\FileManager\Provider;

use Bitter\FileManager\RouteList;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Routing\Router;
use Concrete\Core\Http\Response;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Asset\Asset;

class ServiceProvider implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /** @var Router */
    protected $router;

    /** @var ResponseFactory */
    protected $responseFactory;

    /** @var Package */
    protected $pkg;

    public function __construct(
        PackageService $packageService,
        ResponseFactory $responseFactory,
        Router $router
    )
    {
        $this->router = $router;
        $this->pkg = $packageService->getByHandle("file_manager");
        $this->responseFactory = $responseFactory;
    }

    public function register()
    {

        /*
         * Register the file manager asset
         */

        /** @var AssetList $al */
        $al = AssetList::getInstance();

        $al->register(
            "javascript",
            "bitter/file_manager",
            "js/file-manager.js",
            [
                "position" => Asset::ASSET_POSITION_FOOTER
            ],
            $this->pkg
        );

        $al->registerGroup("bitter/file_manager", [
            ["javascript", "jquery"],
            ["javascript", "underscore"],
            ["javascript", "bitter/file_manager"]
        ]);

        /*
         * Register routes for dialogs
         */


        $this->router->register('/bitter/file_manager/dialogs/move', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\Move::view');
        $this->router->register('/bitter/file_manager/dialogs/move/move', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\Move::move');
        $this->router->register('/bitter/file_manager/dialogs/move/get_items', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\Move::getItems');

        $this->router->register('/bitter/file_manager/dialogs/copy', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\Copy::view');
        $this->router->register('/bitter/file_manager/dialogs/copy/copy', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\Copy::copy');
        $this->router->register('/bitter/file_manager/dialogs/copy/get_items', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\Copy::getItems');

        $this->router->register('/bitter/file_manager/dialogs/edit', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\Edit::view');
        $this->router->register('/bitter/file_manager/dialogs/edit/submit', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\Edit::submit');

        $this->router->register('/bitter/file_manager/dialogs/upload', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\Upload::view');
        $this->router->register('/bitter/file_manager/dialogs/upload/upload', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\Upload::upload');

        $this->router->register('/bitter/file_manager/dialogs/rename', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\Rename::view');
        $this->router->register('/bitter/file_manager/dialogs/rename/submit', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\Rename::submit');

        $this->router->register('/bitter/file_manager/dialogs/create_dir', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\CreateDir::view');
        $this->router->register('/bitter/file_manager/dialogs/create_dir/submit', '\Concrete\Package\FileManager\Controller\Dialog\FileManager\CreateDir::submit');


        /*
         * Register marketing routes
         */

        $this->router->register("/bitter/file_manager/full_access/allow", function () {
            $this->pkg->getConfig()->save('settings.allow_full_access', true);
            $this->responseFactory->create("", Response::HTTP_OK)->send();
            $this->app->shutdown();
        });

        $this->router->register("/bitter/file_manager/full_access/disallow", function () {
            $this->pkg->getConfig()->save('settings.allow_full_access', false);
            $this->responseFactory->create("", Response::HTTP_OK)->send();
            $this->app->shutdown();
        });

        $this->router->register("/bitter/file_manager/reminder/hide", function () {
            $this->pkg->getConfig()->save('reminder.hide', true);
            $this->responseFactory->create("", Response::HTTP_OK)->send();
            $this->app->shutdown();
        });

        $this->router->register("/bitter/file_manager/did_you_know/hide", function () {
            $this->pkg->getConfig()->save('did_you_know.hide', true);
            $this->responseFactory->create("", Response::HTTP_OK)->send();
            $this->app->shutdown();
        });

        $this->router->register("/bitter/file_manager/license_check/hide", function () {
            $this->pkg->getConfig()->save('license_check.hide', true);
            $this->responseFactory->create("", Response::HTTP_OK)->send();
            $this->app->shutdown();
        });

        $list = new RouteList();
        $list->loadRoutes($this->router);
    }

}
