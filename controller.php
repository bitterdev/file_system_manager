<?php

namespace Concrete\Package\FileSystemManager;

use Concrete\Core\Package\Package;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Access\Access;
use Bitter\FileSystemManager\Provider\ServiceProvider;
use Concrete\Core\User\Group\GroupRepository;

class Controller extends Package
{
    protected string $pkgHandle = 'file_system_manager';
    protected string $pkgVersion = '1.0.1';
    protected $appVersionRequired = '9.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/Bitter/FileSystemManager' => 'Bitter\FileSystemManager',
    ];

    public function getPackageDescription(): string
    {
        return t('A powerful file system manager fully integrated into the Concrete CMS dashboard—no FTP client needed.');
    }

    public function getPackageName(): string
    {
        return t('File System Manager');
    }

    public function on_start()
    {
        /** @var ServiceProvider $serviceProvider */
        /** @noinspection PhpUnhandledExceptionInspection */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }

    public function testForInstall($testForAlreadyInstalled = true)
    {
        $errors = parent::testForInstall($testForAlreadyInstalled);
        /** @noinspection PhpUnhandledExceptionInspection */
        $errors = is_object($errors) ? $errors : $this->app->make('error');

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $errors->add(t("Currently this add-on is not supporting Microsoft Windows OS."));
        }

        return $errors->has() ? $errors : true;
    }

    public function install()
    {
        $pkg = parent::install();

        $singlePage = Single::add("/dashboard/file_system_manager", $pkg);

        /** @var Page $singlePage */
        $singlePage->update([
            "cName" => t("File System Manager")
        ]);

        $taskPermissions = [
            [
                "handle" => "access_file_system_manager",
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

        /** @var GroupRepository $repository */
        /** @noinspection PhpUnhandledExceptionInspection */
        $repository = $this->app->make(GroupRepository::class);
        $group = $repository->getGroupByID(ADMIN_GROUP_ID);

        $adminGroupEntity = GroupEntity::getOrCreate($group);

        foreach ($taskPermissions as $taskPermission) {
            $pk = Key::add('admin', $taskPermission["handle"], $taskPermission["name"], "", false, false, $pkg);

            $pa = Access::create($pk);
            $pa->addListItem($adminGroupEntity);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->assignPermissionAccess($pa);
        }
    }

}
