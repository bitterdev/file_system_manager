<?php

/**
 * @project:   File Manager
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\FileManager\Controller\Dialog\FileManager;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Concrete\Core\File\EditResponse;
use Concrete\Core\Permission\Key\Key;
use Bitter\FileManager\FileManager;
use Bitter\FileManager\Exception\NoPermissionsException;
use Concrete\Core\Support\Facade\Application;

class Edit extends UserInterface
{

    protected $viewPath = '/dialogs/file_manager/edit';
    /** @var Request */
    protected $request;
    /** @var FileManager; */
    protected $fileManager;

    public function __construct()
    {
        parent::__construct();

        if (is_null($this->app)) {
            $this->app = Application::getFacadeApplication();
        }

        $this->request = $this->app->make(Request::class);
        $this->fileManager = $this->app->make(FileManager::class);
    }

    public function view()
    {
        $file = $this->request->query->get("file");
        $this->set("file", $file);

        try {
            $content = $this->fileManager->getContent($file);
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
            $this->fileManager->setContent($file, $content);
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
