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
use Concrete\Package\FileManager\Controller\Dialog\FileManager\CreateDir;

/** @var string $base */
/** @var CreateDir $controller */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<form method="post" data-dialog-form="file-manager-create-dir" action="<?php echo $controller->action('submit') ?>">
    <?php echo $form->hidden("base", $base); ?>
    
    <div class="input-group">
        <span class="input-group-addon">
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
        <button class="btn btn-default pull-left" data-dialog-action="cancel">
            <?php echo t('Cancel') ?>
        </button>

        <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right">
            <?php echo t('Create Dir') ?>
        </button>
    </div>
</form>