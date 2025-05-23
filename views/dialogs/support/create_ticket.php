<?php

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Captcha\CaptchaInterface;
use Concrete\Core\Editor\EditorInterface;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Package\FileSystemManager\Controller\Dialog\Support\CreateTicket;

$ticketTypes = [
    "bug" => t("Bug"),
    "enhancement" => t("Enhancement"),
    "proposal" => t("Proposal"),
    "task" => t("Task")
];

$ticketPriorities = [
    "trivial" => t("Trivial"),
    "minor" => t("Minor"),
    "major" => t("Major"),
    "critical" => t("Critical"),
    "blocker" => t("Blocker")
];

/** @var CreateTicket $controller */
/** @var null|int $projectId */

$user = new User();

$app = Application::getFacadeApplication();
/** @var Form $form */
/** @noinspection PhpUnhandledExceptionInspection */
$form = $app->make(Form::class);
/** @var EditorInterface $editor */
/** @noinspection PhpUnhandledExceptionInspection */
$editor = $app->make(EditorInterface::class);
/** @var CaptchaInterface $captcha */
/** @noinspection PhpUnhandledExceptionInspection */
$captcha = $app->make(CaptchaInterface::class);

?>

<form action="<?php echo Url::to("/ccm/system/dialogs/file_system_manager/create_ticket/submit"); ?>"
      data-dialog-form="create-ticket"
      method="post"
      enctype="multipart/form-data">

    <?php echo $form->hidden('projectHandle', "file_system_manager"); ?>

    <div class="form-group">
        <?php echo $form->label('email', t("E-Mail")); ?>
        <?php echo $form->email('email'); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('title', t("Title")); ?>
        <?php echo $form->text('title'); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('content', t("Content")); ?>
        <?php echo $editor->outputStandardEditor('content'); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('ticketType', t("Type")); ?>
        <?php echo $form->select('ticketType', $ticketTypes); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('ticketPriority', t("Priority")); ?>
        <?php echo $form->select('ticketPriority', $ticketPriorities); ?>
    </div>

    <div class="dialog-buttons">
        <button class="btn btn-secondary float-left" data-dialog-action="cancel">
            <?php echo t('Cancel') ?>
        </button>

        <button type="button" data-dialog-action="submit" class="btn btn-primary float-right">
            <?php echo t('Create Ticket') ?>
        </button>
    </div>
</form>