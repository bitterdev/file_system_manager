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
use Concrete\Package\FileManager\Controller\Dialog\FileManager\Rename;

/** @var Rename $controller */
/** @var string $old */
/** @var string $new */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
?>

<form method="post" data-dialog-form="file-manager-rename" action="<?php echo $controller->action('submit') ?>">
    <?php echo $form->hidden("old", $old); ?>
    <?php echo $form->text("new", $new); ?>
    
    <div class="dialog-buttons">
        <button class="btn btn-default pull-left" data-dialog-action="cancel">
            <?php echo t('Cancel') ?>
        </button>

        <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right">
            <?php echo t('Rename') ?>
        </button>
    </div>
</form>