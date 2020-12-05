<?php
/**
 * @project:   File Manager
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Package\FileManager\Controller\Dialog\FileManager\Upload;

/** @var Upload $controller */
/** @var string $dest */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
?>

<form method="post" data-dialog-form="file-manager-upload" action="<?php echo $controller->action('upload') ?>"
      class="dropzone">
    <?php echo $form->hidden("dest", $dest); ?>
</form>

<div class="dialog-buttons">
    <button class="btn btn-default pull-left" data-dialog-action="cancel">
        <?php echo t('Close') ?>
    </button>
</div>

<script>
    $(".dropzone").dropzone();
</script>