<?php

namespace Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Permission\Key\Key;
use Bitter\FileSystemManager\FileSystemManager;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Upload extends UserInterface {

    protected $viewPath = '/dialogs/file_system_manager/upload';

    /** @var Request */
    protected $request;

    /** @var FileSystemManager; */
    protected $fileSystemManager;

    /** @var ResponseFactory */
    protected $responseFactory;

    public function __construct() {
        parent::__construct();

        if (is_null($this->app)) {
            $this->app = Application::getFacadeApplication();
        }

        $this->request = $this->app->make(Request::class);
        $this->fileSystemManager = $this->app->make(FileSystemManager::class);
        $this->responseFactory = $this->app->make(ResponseFactory::class);
    }

    public function view() {
        $file = $this->request->query->get("file");

        $file = dirname($file);
        
        $file = rtrim($file, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        
        $this->set("dest", $file);

        $this->requireAsset("dropzone");
    }

    public function upload() {

        $success = true;

        if ($this->request->query->has("dest")) {
            $dest = $this->request->query->get("dest");

            /** @var UploadedFile $file */
            foreach ($this->request->files->all() as $file) {
                $file->move($dest, $file->getClientOriginalName());
            }
        } else {
            $success = false;
        }

        return $this->responseFactory->json(["success" => $success]);
    }

    public function canAccess() {
        return Key::getByHandle("upload_files")->validate();
    }

}
