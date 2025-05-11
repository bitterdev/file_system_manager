<?php

namespace Bitter\FileSystemManager;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu as ContextMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DialogLinkItem;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Support\Facade\Url;

class Menu extends ContextMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-file-menu'];
    protected $minItemThreshold = 2; // because we already have clear and the divider, we just hide them with JS

    public function __construct($file)
    {
        parent::__construct();

        if (is_file($file)) {

            if (Key::getByHandle("download_files")->validate()) {
                $this->addItem(
                    new LinkItem(
                        sprintf(
                            "%s?%s",
                            Url::to("/dashboard/file_system_manager/download"),
                            http_build_query(
                                [
                                    "file" => $file
                                ]
                            )
                        ),

                        t('Download'),

                        [
                            'data-file-system-manager-action' => 'download'
                        ]
                    )
                );

                $this->addItem(new DividerItem());
            }

            if (Key::getByHandle("edit_files")->validate()) {
                $this->addItem(
                    new DialogLinkItem(
                        sprintf(
                            "%s?%s",
                            Url::to("/bitter/file_system_manager/dialogs/edit"),
                            http_build_query(
                                [
                                    "file" => $file
                                ]
                            )
                        ),
                        t("Edit"),
                        t("Edit file %s", basename($file)),
                        500,
                        500
                    )
                );
            }
        }

        if (Key::getByHandle("delete_files")->validate()) {
            $this->addItem(new LinkItem('#', t('Delete'), ['data-file-system-manager-action' => 'delete', 'data-file' => $file]));
        }

        if (Key::getByHandle("rename_files")->validate()) {
            $this->addItem(
                new DialogLinkItem(
                    sprintf(
                        "%s?%s",
                        Url::to("/bitter/file_system_manager/dialogs/rename"),
                        http_build_query(
                            [
                                "file" => $file
                            ]
                        )
                    ),
                    t('Rename'),
                    t("Rename %s to...", basename($file)),
                    500,
                    500,
                    [
                        "dialog-on-close" => "window.Bitter.fileSystemManager.dir()"
                    ]
                )
            );
        }

        if (Key::getByHandle("move_files")->validate()) {
            $this->addItem(
                new DialogLinkItem(
                    sprintf(
                        "%s?%s",
                        Url::to("/bitter/file_system_manager/dialogs/move"),
                        http_build_query(
                            [
                                "file" => $file
                            ]
                        )
                    ),
                    t('Move'),
                    t("Move %s to...", basename($file)),
                    500,
                    500,
                    [
                        "dialog-on-close" => "window.Bitter.fileSystemManager.dir()"
                    ]
                )
            );
        }

        if (Key::getByHandle("copy_files")->validate()) {
            $this->addItem(
                new DialogLinkItem(
                    sprintf(
                        "%s?%s",
                        Url::to("/bitter/file_system_manager/dialogs/copy"),
                        http_build_query(
                            [
                                "file" => $file
                            ]
                        )
                    ),
                    t('Copy'),
                    t("Copy %s to...", basename($file)),
                    500,
                    500
                )
            );
        }

        $this->addItem(new DividerItem());

        if (Key::getByHandle("upload_files")->validate()) {
            $this->addItem(
                new DialogLinkItem(
                    sprintf(
                        "%s?%s",
                        Url::to("/bitter/file_system_manager/dialogs/upload"),
                        http_build_query(
                            [
                                "file" => $file
                            ]
                        )
                    ),
                    t('Upload Files'),
                    t("Upload Files"),
                    500,
                    500,
                    [
                        "dialog-on-close" => "window.Bitter.fileSystemManager.dir()"
                    ]
                )
            );
        }

        if (Key::getByHandle("create_dirs")->validate()) {
            $this->addItem(
                new DialogLinkItem(
                    sprintf(
                        "%s?%s",
                        Url::to("/bitter/file_system_manager/dialogs/create_dir"),
                        http_build_query(
                            [
                                "file" => $file
                            ]
                        )
                    ),
                    t('Create Directory'),
                    t("Create Directory"),
                    500,
                    500,
                    [
                        "dialog-on-close" => "window.Bitter.fileSystemManager.dir()"
                    ]
                )
            );
        }
    }
}
