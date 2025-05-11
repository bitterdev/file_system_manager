<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\CreateDir;

/** @var string $base */
/** @var CreateDir $controller */

$app = Application::getFacadeApplication();
/** @var Form $form */
/** @noinspection PhpUnhandledExceptionInspection */
$form = $app->make(Form::class);

?>

<form method="post" data-dialog-form="file-system-manager-create-dir" action="<?php echo $controller->action('submit') ?>">
    <?php echo $form->hidden("base", $base); ?>
    
    <div class="input-group">
        <span class="input-group-text">
            <?php
                $maxDisplayLength = 40;
                
                if (strlen($base) > $maxDisplayLength) {
                    echo "..." . substr($base, strlen($base) - $maxDisplayLength - 3);
                } else {
                    echo $base;
                }
            ?>
        </span>
        
        <?php echo $form->text("dir", null); ?>
    </div>

    <div class="dialog-buttons">
        <button class="btn btn-secondary float-left" data-dialog-action="cancel">
            <?php echo t('Cancel') ?>
        </button>

        <button type="button" data-dialog-action="submit" class="btn btn-primary float-right">
            <?php echo t('Create Dir') ?>
        </button>
    </div>
</form>