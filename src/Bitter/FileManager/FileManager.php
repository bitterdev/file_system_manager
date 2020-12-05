<?php

/**
 * @project:   File Manager
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\FileManager;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\File\Service\File;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Package\Package;
use Concrete\Core\Utility\Service\Number;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Package\PackageService;
use League\Flysystem\FileExistsException;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Bitter\FileManager\Exception\InvalidSourceException;
use Bitter\FileManager\Exception\InvalidDestinationException;
use Bitter\FileManager\Exception\NoPermissionsException;
use League\Flysystem\FileNotFoundException;
use Punic\Exception\BadArgumentType;
use Exception;

class FileManager implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /** @var File */
    protected $fileSystem;

    /** @var Date */
    protected $dateHelper;

    /** @var Number */
    protected $numberHelper;

    /** @var Request */
    protected $request;

    /** @var Package */
    protected $pkg;

    public function __construct(
        File $fileSystem,
        Date $dateHelper,
        Number $numberHelper,
        PackageService $packageService,
        Request $request
    )
    {
        $this->fileSystem = $fileSystem;
        $this->dateHelper = $dateHelper;
        $this->numberHelper = $numberHelper;
        $this->request = $request;
        $this->pkg = $packageService->getByHandle("file_manager");
    }

    /**
     * @param string $file
     * @return bool
     * @throws NoPermissionsException
     * @throws FileNotFoundException
     */
    public function delete($file)
    {
        if (Key::getByHandle("delete_files")->validate() && $this->isAllowed(dirname($file))) {
            if (is_dir($file)) {
                $success = $this->fileSystem->removeAll($file, true);
            } else {
                $pathInfo = pathinfo($file);
                $local = new Local($pathInfo["dirname"]);
                $fs = new Filesystem($local);
                $success = $fs->delete($pathInfo["basename"]);
            }

            return $success;
        } else {
            throw new NoPermissionsException();
        }
    }

    /**
     * @param string $file
     * @throws NoPermissionsException
     */
    public function download($file)
    {
        if (Key::getByHandle("download_files")->validate() && $this->isAllowed(dirname($file))) {
            /** @noinspection PhpParamsInspection */
            $this->fileSystem->forceDownload($file);
        } else {
            throw new NoPermissionsException();
        }
    }

    /**
     * @param string $file
     * @return string
     * @throws NoPermissionsException
     */
    public function getContent($file)
    {
        if (Key::getByHandle("edit_files")->validate() && $this->isAllowed(dirname($file))) {
            return $this->fileSystem->getContents($file);
        } else {
            throw new NoPermissionsException();
        }
    }

    /**
     * @param string $file
     * @param string $content
     * @return void
     * @throws NoPermissionsException
     */
    public function setContent($file, $content)
    {
        if (Key::getByHandle("edit_files")->validate() && $this->isAllowed(dirname($file))) {
            $this->fileSystem->clear($file);
            $this->fileSystem->append($file, $content);
        } else {
            throw new NoPermissionsException();
        }
    }

    /**
     * @return string
     */
    private function getBaseDir()
    {
        $allowFullAccess = $this->pkg->getConfig()->get('settings.allow_full_access', false);

        if ($allowFullAccess) {
            $dir = DIRECTORY_SEPARATOR;
        } else {
            $dir = $this->request->server->get("DOCUMENT_ROOT");

            $dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        return $dir;
    }

    /**
     * @param string $dir
     * @return bool
     */
    private function isAllowed($dir)
    {
        return strtolower(substr($dir, 0, strlen($this->getBaseDir()))) === strtolower($this->getBaseDir());
    }

    /**
     *
     * @param string $dir
     * @param string $orderBy
     * @param bool $isAsc
     * @return array
     * @throws NoPermissionsException
     * @throws \Punic\Exception
     * @throws BadArgumentType
     */
    public function dir($dir, $orderBy = 'name', $isAsc = true)
    {
        if (Key::getByHandle("access_file_manager")->validate()) {
            $faIconClasses = [
                // Media
                'image' => 'fa-file-image-o',
                'audio' => 'fa-file-audio-o',
                'video' => 'fa-file-video-o',
                // Documents
                'application/pdf' => 'fa-file-pdf-o',
                'application/msword' => 'fa-file-word-o',
                'application/vnd.ms-word' => 'fa-file-word-o',
                'application/vnd.oasis.opendocument.text' => 'fa-file-word-o',
                'application/vnd.openxmlformats-officedocument.wordprocessingml' => 'fa-file-word-o',
                'application/vnd.ms-excel' => 'fa-file-excel-o',
                'application/vnd.openxmlformats-officedocument.spreadsheetml' => 'fa-file-excel-o',
                'application/vnd.oasis.opendocument.spreadsheet' => 'fa-file-excel-o',
                'application/vnd.ms-powerpoint' => 'fa-file-powerpoint-o',
                'application/vnd.openxmlformats-officedocument.presentationml' => 'fa-file-powerpoint-o',
                'application/vnd.oasis.opendocument.presentation' => 'fa-file-powerpoint-o',
                'text/plain' => 'fa-file-text-o',
                'text/html' => 'fa-file-code-o',
                'application/json' => 'fa-file-code-o',
                // Archives
                'application/gzip' => 'fa-file-archive-o',
                'application/zip' => 'fa-file-archive-o'
            ];

            $files = [];

            if (strlen($dir) == 0 || !$this->isAllowed($dir)) {
                $dir = $this->getBaseDir();
            }

            $dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            $local = new Local($dir);
            $fs = new Filesystem($local);

            /*
             * Scan files from given path
             */

            foreach ($this->fileSystem->getDirectoryContents($dir) as $name) {
                $file = $dir . $name;

                /*
                 * Retrieve file informations
                 */

                try {
                    $fileTime = $fs->getTimestamp($name);
                    $fileSize = (int)$fs->getSize($name);
                    $isDirectory = is_dir($file);
                    $mimeType = $fs->getMimetype($name);

                    if ($isDirectory) {
                        $fontAwesomeHandle = "fa-folder";
                    } else {
                        if (isset($faIconClasses[$mimeType])) {
                            $fontAwesomeHandle = $faIconClasses[$mimeType];
                        } else {
                            $fontAwesomeHandle = 'fa-file-o';
                        }
                    }
                } catch (Exception $err) {
                    // Skip Error
                }

                /*
                 * Add file to results
                 */
                /** @noinspection PhpUnhandledExceptionInspection */
                /** @noinspection PhpUndefinedVariableInspection */
                $files[] = [
                    "file" => $file,
                    "name" => $name,
                    "dateTime" => $fileTime,
                    "displayDateTime" => $this->dateHelper->formatDateTime($fileTime),
                    "size" => $fileSize,
                    "displaySize" => $this->numberHelper->formatSize($fileSize),
                    "isDirectory" => $isDirectory,
                    "mimeType" => $mimeType,
                    "fontAwesomeHandle" => $fontAwesomeHandle,
                    "menu" => new Menu($file)
                ];
            }

            /*
             * Sort results
             */

            if (in_array($orderBy, ["name", "dateTime", "size", "mimeType"]) === false) {
                $orderBy = "name";
            }

            $orderDir = $isAsc ? SORT_ASC : SORT_DESC;

            array_multisort(array_column($files, $orderBy), $orderDir, $files);

            /*
             * Add possibiltiy to navigate to parent directory
             */

            $parentDirectory = dirname($dir);

            if ($dir !== $parentDirectory && $dir != $this->getBaseDir()) {

                /*
                 * Insert ".." entry.
                 */

                array_unshift($files, [
                    "file" => $parentDirectory,
                    "name" => "..",
                    "dateTime" => null,
                    "displayDateTime" => "",
                    "size" => null,
                    "displaySize" => "",
                    "isDirectory" => true,
                    "mimeType" => "",
                    "fontAwesomeHandle" => "fa-folder",
                    "menu" => new Menu($dir)
                ]);
            }

            /*
             * Output the results
             */

            return [
                "dir" => $dir,
                "files" => $files,
                "orderBy" => $orderBy,
                "isAsc" => $isAsc
            ];
        } else {
            throw new NoPermissionsException();
        }
    }

    /**
     *
     * @param string $dir
     * @return array
     * @throws NoPermissionsException
     */
    public function dirFancytree($dir)
    {
        if (Key::getByHandle("access_file_manager")->validate() && ($this->isAllowed($dir) || strlen($dir) == 0)) {
            $treeNodes = [];

            if (strlen($dir) == 0) {
                $dir = $this->getBaseDir();

                $dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

                $treeNodes[] = [
                    "title" => basename($dir),
                    "key" => $dir,
                    "folder" => true,
                    "lazy" => true
                ];
            } else {
                $dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

                /*
                 * Scan files from given path
                 */

                foreach ($this->fileSystem->getDirectoryContents($dir) as $name) {
                    $file = $dir . $name;

                    if (is_dir($file)) {
                        $treeNodes[] = [
                            "title" => $name,
                            "key" => $file,
                            "folder" => true,
                            "lazy" => true
                        ];
                    }
                }
            }

            return $treeNodes;
        } else {
            throw new NoPermissionsException();
        }
    }

    /**
     *
     * @param string $src
     * @param string $dest
     * @return boolean
     * @throws InvalidSourceException
     * @throws InvalidDestinationException
     * @throws NoPermissionsException
     */
    public function copy($src, $dest)
    {
        if (Key::getByHandle("copy_files")->validate() && $this->isAllowed($src) && $this->isAllowed($dest)) {
            if (!file_exists($src)) {
                throw new InvalidSourceException();
            } elseif (!file_exists($dest) || !is_dir($dest)) {
                throw new InvalidDestinationException();
            }

            $this->fileSystem->copyAll($src, $dest . DIRECTORY_SEPARATOR . basename($src));

            return true;
        } else {
            throw new NoPermissionsException();
        }
    }

    /**
     *
     * @param string $src
     * @param string $dest
     * @return boolean
     * @throws FileNotFoundException
     * @throws InvalidDestinationException
     * @throws InvalidSourceException
     * @throws NoPermissionsException
     * @throws FileExistsException
     */
    public function move($src, $dest)
    {
        if (Key::getByHandle("move_files")->validate() && $this->isAllowed($src) && $this->isAllowed($dest)) {
            if (!file_exists($src)) {
                throw new InvalidSourceException();
            } elseif (!file_exists($dest) || !is_dir($dest)) {
                throw new InvalidDestinationException();
            }

            $local = new Local("/");
            $fs = new Filesystem($local);
            $fs->rename($src, $dest . DIRECTORY_SEPARATOR . basename($src));

            return true;
        } else {
            throw new NoPermissionsException();
        }
    }

    /**
     *
     * @param string $src
     * @param string $dest
     * @return boolean
     * @throws FileNotFoundException
     * @throws InvalidSourceException
     * @throws NoPermissionsException
     * @throws FileExistsException
     */
    public function rename($src, $dest)
    {
        if (Key::getByHandle("rename_files")->validate() && $this->isAllowed($src) && $this->isAllowed($dest)) {
            if (!file_exists($src)) {
                throw new InvalidSourceException();
            }

            $local = new Local("/");
            $fs = new Filesystem($local);

            $fs->rename($src, $dest);

            return true;
        } else {
            throw new NoPermissionsException();
        }
    }

    /**
     * @param string $dir
     * @throws NoPermissionsException
     */
    public function createDir($dir)
    {
        if (Key::getByHandle("create_dirs")->validate() && $this->isAllowed($dir)) {
            $local = new Local("/");
            $fs = new Filesystem($local);
            $fs->createDir($dir);
        } else {
            throw new NoPermissionsException();
        }
    }

}
