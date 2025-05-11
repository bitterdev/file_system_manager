<?php /** @noinspection PhpInconsistentReturnPointsInspection */

namespace Concrete\Package\FileSystemManager\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use League\Flysystem\FileNotFoundException;
use Punic\Exception;
use Punic\Exception\BadArgumentType;
use Symfony\Component\HttpFoundation\Response;
use Bitter\FileSystemManager\FileSystemManager as FileSystemManagerService;
use Bitter\FileSystemManager\Exception\NoPermissionsException;

class FileSystemManager extends DashboardPageController
{

    /** @var ResponseFactory */
    protected $responseFactory;

    /** @var Request */
    protected $request;

    /** @var FileSystemManagerService; */
    protected $fileSystemManager;

    public function on_start()
    {
        parent::on_start();

        /*
         * Load dependencies
         */

        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->request = $this->app->make(Request::class);
        $this->fileSystemManager = $this->app->make(FileSystemManagerService::class);
    }

    public function delete()
    {
        try {
            $file = $this->request->query->get("file");

            $success = $this->fileSystemManager->delete($file);

            return $this->responseFactory->json(["success" => $success]);
        } catch (NoPermissionsException $err) {
            return $this->responseFactory->create('', Response::HTTP_FORBIDDEN);
        } catch (FileNotFoundException $e) {
        }
    }

    public function download()
    {
        try {
            $file = $this->request->query->get("file");

            $this->fileSystemManager->download($file);
        } catch (NoPermissionsException $err) {
            return $this->responseFactory->create('', Response::HTTP_FORBIDDEN);
        }
    }

    public function dir()
    {
        try {
            $dir = $this->request->request->get("dir");
            $orderBy = $this->request->request->get("orderBy");
            $isAsc = (int)$this->request->request->get("isAsc", 1) === 1;

            $result = $this->fileSystemManager->dir($dir, $orderBy, $isAsc);

            return $this->responseFactory->json($result);
        } catch (NoPermissionsException $err) {
            return $this->responseFactory->create('', Response::HTTP_FORBIDDEN);
        } catch (BadArgumentType $e) {
        } catch (Exception $e) {
        }
    }

    public function view()
    {
        $this->requireAsset("bitter/file_system_manager");

        $config = [
            "urls" => [
                "dir" => $this->action("dir"),
                "download" => $this->action("download"),
                "delete" => $this->action("delete")
            ],
            "initDir" => DIR_BASE,
            "i18n" => [
                "deleteFileButton" => t("Delete"),
                "deleteFileText" => t("Are you sure that you want to delete the file \"{file}\"?"),
                "cancel" => t("Cancel"),
                "go" => t("Go"),
                "confirm" => t("Confirm"),
            ]
        ];

        $this->set("config", $config);
    }

}
