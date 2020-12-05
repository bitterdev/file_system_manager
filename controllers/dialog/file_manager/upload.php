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
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Permission\Key\Key;
use Bitter\FileManager\FileManager;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Upload extends UserInterface {

    protected $viewPath = '/dialogs/file_manager/upload';

    /** @var Request */
    protected $request;

    /** @var FileManager; */
    protected $fileManager;

    /** @var ResponseFactory */
    protected $responseFactory;

    public function __construct() {
        parent::__construct();

        if (is_null($this->app)) {
            $this->app = Application::getFacadeApplication();
        }

        $this->request = $this->app->make(Request::class);
        $this->fileManager = $this->app->make(FileManager::class);
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
        $dest = $this->request->request->get("dest");

        $success = true;

        if (is_dir($dest)) {
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
