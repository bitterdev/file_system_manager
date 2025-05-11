<?php

namespace Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Concrete\Core\File\EditResponse;
use Concrete\Core\Permission\Key\Key;
use Bitter\FileSystemManager\FileSystemManager;
use Bitter\FileSystemManager\Exception\InvalidSourceException;
use Bitter\FileSystemManager\Exception\NoPermissionsException;
use Concrete\Core\Support\Facade\Application;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;

class Rename extends UserInterface {

    protected $viewPath = '/dialogs/file_system_manager/rename';

    /** @var Request */
    protected $request;

    /** @var FileSystemManager; */
    protected $fileSystemManager;

    public function __construct() {
        parent::__construct();

        if (is_null($this->app)) {
            $this->app = Application::getFacadeApplication();
        }

        $this->request = $this->app->make(Request::class);
        $this->fileSystemManager = $this->app->make(FileSystemManager::class);
    }

    public function view() {
        $file = $this->request->query->get("file");

        $this->set("old", $file);
        $this->set("new", basename($file));
    }

    public function submit() {
        $response = new EditResponse();
        /** @var ErrorList $errorList */
        $errorList = $this->app->make('error');

        $old = $this->request->request->get("old");
        
        $src = $old;
        
        $old = dirname($old);

        $old = rtrim($old, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        
        $dest = $old . $this->request->request->get("new");

        try {
            $this->fileSystemManager->rename($src, $dest);
        } catch (InvalidSourceException $err) {
            $errorList->add(t('Please select a valid source.'));
        } catch (NoPermissionsException $err) {
            $errorList->add(t('You have no permissions to perform this action.'));
        } catch(FileExistsException $err) {
            $errorList->add(t('Please enter a different name.'));
        } catch (FileNotFoundException $e) {
            $errorList->add(t('Please select a valid source.'));
        }

        if ($errorList->has()) {
            $response->setError($errorList);
        } else {
            $response->setMessage(t('File renamed successfully.'));
        }

        /** @noinspection PhpDeprecationInspection */
        return $response->outputJSON();
    }

    public function canAccess() {
        return Key::getByHandle("rename_files")->validate();
    }

}
