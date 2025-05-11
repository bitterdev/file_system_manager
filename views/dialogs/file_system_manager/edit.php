<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Edit;

/** @var Edit $controller */
/** @var string $file */
/** @var string $content */

$app = Application::getFacadeApplication();
/** @var Form $form */
/** @noinspection PhpUnhandledExceptionInspection */
$form = $app->make(Form::class);
?>

<!--suppress CssUnusedSymbol -->
<style type="text/css">
    #content, #edit-file-form {
        width: 100%;
        height: 100%;
    }
</style>

<form method="post" data-dialog-form="file-system-manager-edit" action="<?php echo $controller->action('submit') ?>"
      id="edit-file-form">
    <?php echo $form->hidden("file", $file); ?>

    <?php echo $form->textarea("content", $content); ?>

    <div class="dialog-buttons">
        <button class="btn btn-secondary float-left" data-dialog-action="cancel">
            <?php echo t('Cancel') ?>
        </button>

        <button type="button" data-dialog-action="submit" class="btn btn-primary float-right">
            <?php echo t('Save') ?>
        </button>
    </div>
</form>