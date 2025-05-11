<?php

namespace Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use Concrete\Core\File\EditResponse;
use Concrete\Core\Permission\Key\Key;
use Bitter\FileSystemManager\FileSystemManager;
use Bitter\FileSystemManager\Exception\InvalidSourceException;
use Bitter\FileSystemManager\Exception\InvalidDestinationException;
use Bitter\FileSystemManager\Exception\NoPermissionsException;
use Concrete\Core\Support\Facade\Application;

class Copy extends UserInterface
{

    protected $viewPath = '/dialogs/file_system_manager/copy';
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var Request */
    protected $request;
    /** @var FileSystemManager; */
    protected $fileSystemManager;

    public function __construct()
    {
        parent::__construct();

        if (is_null($this->app)) {
            $this->app = Application::getFacadeApplication();
        }

        $this->request = $this->app->make(Request::class);
        $this->fileSystemManager = $this->app->make(FileSystemManager::class);
        $this->responseFactory = $this->app->make(ResponseFactory::class);
    }

    public function view()
    {
        $file = $this->request->query->get("file");

        $this->set("file", $file);

        $this->requireAsset('fancytree');
    }

    public function getItems()
    {
        $file = $this->request->query->get("file");

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->fileSystemManager->dirFancytree($file);

        return $this->responseFactory->json($result);
    }

    public function copy()
    {
        $response = new EditResponse();
        /** @var ErrorList $errorList */
        $errorList = $this->app->make('error');

        $src = $this->request->request->get("src");
        $dest = $this->request->request->get("dest");

        try {
            $this->fileSystemManager->copy($src, $dest);
        } catch (InvalidSourceException $err) {
            $errorList->add(t('Please select a valid source.'));
        } catch (InvalidDestinationException $err) {
            $errorList->add(t('Please select a valid destination.'));
        } catch (NoPermissionsException $err) {
            $errorList->add(t('You have no permissions to perform this action.'));
        }

        if ($errorList->has()) {
            $response->setError($errorList);
        } else {
            $response->setMessage(t('File copied successfully.'));
        }

        /** @noinspection PhpDeprecationInspection */
        return $response->outputJSON();
    }

    public function canAccess()
    {
        return Key::getByHandle("copy_files")->validate();
    }

}
