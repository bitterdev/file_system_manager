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
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use Concrete\Core\File\EditResponse;
use Concrete\Core\Permission\Key\Key;
use Bitter\FileManager\FileManager;
use Bitter\FileManager\Exception\InvalidSourceException;
use Bitter\FileManager\Exception\InvalidDestinationException;
use Bitter\FileManager\Exception\NoPermissionsException;
use Concrete\Core\Support\Facade\Application;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;

class Move extends UserInterface {

    protected $viewPath = '/dialogs/file_manager/move';
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var Request */
    protected $request;
    /** @var FileManager; */
    protected $fileManager;

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

        $this->set("file", $file);

        $this->requireAsset('fancytree');
    }

    public function getItems() {
        $file = $this->request->query->get("file");

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->fileManager->dirFancytree($file);

        return $this->responseFactory->json($result);
    }

    public function move() {
        $response = new EditResponse();
        /** @var ErrorList $errorList */
        $errorList = $this->app->make('error');

        $src = $this->request->request->get("src");
        $dest = $this->request->request->get("dest");

        try {
            $this->fileManager->move($src, $dest);
        } catch (InvalidSourceException $err) {
            $errorList->add(t('Please select a valid source.'));
        } catch (InvalidDestinationException $err) {
            $errorList->add(t('Please select a valid destination.'));
        } catch (NoPermissionsException $err) {
            $errorList->add(t('You have no permissions to perform this action.'));
        } catch (FileExistsException $e) {
            $errorList->add(t('Please select a valid source.'));
        } catch (FileNotFoundException $e) {
            $errorList->add(t('Please select a valid source.'));
        }

        if ($errorList->has()) {
            $response->setError($errorList);
        } else {
            $response->setMessage(t('File moved successfully.'));
        }

        /** @noinspection PhpDeprecationInspection */
        return $response->outputJSON();
    }

    public function canAccess() {
        return Key::getByHandle("move_files")->validate();
    }

}
