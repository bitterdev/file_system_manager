<?php

/**
 * @project:   File Manager
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\FileManager;

use Concrete\Core\Package\Package;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\User\Group\Group;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Access\Access;
use Bitter\FileManager\Provider\ServiceProvider;

class Controller extends Package
{

    protected $pkgHandle = 'file_manager';
    protected $pkgVersion = '1.0.0';
    protected $appVersionRequired = '8.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/Bitter/FileManager' => 'Bitter\FileManager',
    ];

    public function getPackageDescription()
    {
        return t('Manage your entire file system within your concrete5 website. You can navigate, copy, move, edit directories and files and much more.');
    }

    public function getPackageName()
    {
        return t('File Sytem Manager');
    }

    public function on_start()
    {
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }

    public function testForInstall($testForAlreadyInstalled = true)
    {
        $errors = parent::testForInstall($testForAlreadyInstalled);
        $errors = is_object($errors) ? $errors : $this->app->make('error');

        /*
         * Dependency Check
         */

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $errors->add(t("Currently this add-on is not supporting Microsoft Windows OS."));
        }

        return $errors->has() ? $errors : true;
    }

    public function install()
    {

        $pkg = parent::install();

        /*
         * Install the dashboard page
         */

        $singlePage = Single::add("/dashboard/file_manager", $pkg);

        /** @var Page $singlePage */
        $singlePage->update([
            "cName" => t("File Sytem Manager")
        ]);

        /*
         * Install the task permissions
         */

        $taskPermissions = [
            [
                "handle" => "access_file_manager",
                "name" => t("Access File System (File System Manager)")
            ],
            [
                "handle" => "copy_files",
                "name" => t("Copy Files (File System Manager)")
            ],
            [
                "handle" => "create_dirs",
                "name" => t("Create Directories (File System Manager)")
            ],
            [
                "handle" => "edit_files",
                "name" => t("Edit Files (File System Manager)")
            ],
            [
                "handle" => "move_files",
                "name" => t("Move Files (File System Manager)")
            ],
            [
                "handle" => "rename_files",
                "name" => t("Rename Files (File System Manager)")
            ],
            [
                "handle" => "upload_files",
                "name" => t("Upload Files (File System Manager)")
            ],
            [
                "handle" => "download_files",
                "name" => t("Download Files (File System Manager)")
            ],
            [
                "handle" => "delete_files",
                "name" => t("Delete Files (File System Manager)")
            ]
        ];

        $group = Group::getByID(ADMIN_GROUP_ID);

        $adminGroupEntity = GroupEntity::getOrCreate($group);

        foreach ($taskPermissions as $taskPermission) {
            /** @var Key $pk */
            $pk = Key::add('admin', $taskPermission["handle"], $taskPermission["name"], "", false, false, $pkg);

            $pa = Access::create($pk);
            /** @noinspection PhpParamsInspection */
            $pa->addListItem($adminGroupEntity);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->assignPermissionAccess($pa);
        }
    }

}
