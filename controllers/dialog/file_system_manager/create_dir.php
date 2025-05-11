<?php

namespace Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Concrete\Core\File\EditResponse;
use Concrete\Core\Permission\Key\Key;
use Bitter\FileSystemManager\FileSystemManager;
use Bitter\FileSystemManager\Exception\NoPermissionsException;
use Concrete\Core\Support\Facade\Application;

class CreateDir extends UserInterface
{

    protected $viewPath = '/dialogs/file_system_manager/create_dir';
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
    }

    public function view()
    {
        $file = $this->request->query->get("file");

        $file = dirname($file);

        $file = rtrim($file, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->set("base", $file);
    }

    public function submit()
    {
        $response = new EditResponse();
        /** @var ErrorList $errorList */
        $errorList = $this->app->make('error');

        $base = $this->request->request->get("base");

        $dir = $base . DIRECTORY_SEPARATOR . $this->request->request->get("dir");

        try {
            $this->fileSystemManager->createDir($dir);
        } catch (NoPermissionsException $err) {
            $errorList->add(t('You have no permissions to perform this action.'));
        }

        if ($errorList->has()) {
            $response->setError($errorList);
        } else {
            $response->setMessage(t('Directory created successfully.'));
        }

        /** @noinspection PhpDeprecationInspection */
        return $response->outputJSON();
    }

    public function canAccess()
    {
        return Key::getByHandle("create_dirs")->validate();
    }

}
