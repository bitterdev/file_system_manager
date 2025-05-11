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

class Edit extends UserInterface
{

    protected $viewPath = '/dialogs/file_system_manager/edit';
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
        $this->set("file", $file);

        try {
            $content = $this->fileSystemManager->getContent($file);
            $this->set("content", $content);
        } catch (NoPermissionsException $err) {
            $this->app->shutdown();
        }

    }

    public function submit()
    {
        $response = new EditResponse();
        /** @var ErrorList $errorList */
        $errorList = $this->app->make('error');

        $file = $this->request->request->get("file");
        $content = $this->request->request->get("content");

        try {
            $this->fileSystemManager->setContent($file, $content);
        } catch (NoPermissionsException $err) {
            $errorList->add(t('You have no permissions to perform this action.'));
        }

        if ($errorList->has()) {
            $response->setError($errorList);
        } else {
            $response->setMessage(t('File updated successfully.'));
        }

        /** @noinspection PhpDeprecationInspection */
        $response->outputJSON();
    }

    public function canAccess()
    {
        return Key::getByHandle("edit_files")->validate();
    }

}
