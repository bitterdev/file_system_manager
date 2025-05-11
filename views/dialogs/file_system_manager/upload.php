<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Package\FileSystemManager\Controller\Dialog\FileSystemManager\Upload;

/** @var Upload $controller */
/** @var string $dest */

$app = Application::getFacadeApplication();
/** @var Form $form */
/** @noinspection PhpUnhandledExceptionInspection */
$form = $app->make(Form::class);
/** @var Identifier $idHelper */
/** @noinspection PhpUnhandledExceptionInspection */
$idHelper = $app->make(Identifier::class);

$previewTemplateId = "ccm-" . $idHelper->getString() . "-file-upload-preview-template";
?>

<script type="text/template" id="<?php echo $previewTemplateId; ?>">
    <div class="ccm-file-upload-wrapper">
        <div class="ccm-file-upload-item-wrapper">
            <div class="ccm-file-upload-item">
                <div class="ccm-file-upload-item-inner">
                    <div class="ccm-file-upload-image-wrapper">
                        <img data-dz-thumbnail="">
                    </div>

                    <div class="ccm-file-upload-progress-text">
                        <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
                                  class="ccm-file-upload-progress-text-value"></text>
                        </svg>
                    </div>

                    <svg viewbox="0 0 120 120" width="120" height="120" class="ccm-file-upload-progress">
                        <circle stroke="#4A90E2" stroke-width="5" fill="transparent" r="52" cx="60" cy="60"></circle>
                    </svg>
                </div>
            </div>

            <div class="ccm-file-upload-label dz-filename" data-dz-name></div>

            <input name="file[]" value="" type="hidden"/>
        </div>
    </div>
</script>

<div class="ccm-file-upload-container-wrapper">
    <div data-dialog-form="file-system-manager-upload"
         class="dropzone ccm-file-upload ccm-file-upload-container"
         data-preview-element="<?php echo $previewTemplateId; ?>">
        <div class="dz-default dz-message">
            <button type="button" class="dz-button">
                <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDI0LjEuMiwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkViZW5lXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAxMzIgMTMyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAxMzIgMTMyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+Cgkuc3Qwe29wYWNpdHk6MC45O2ZpbGwtcnVsZTpldmVub2RkO2NsaXAtcnVsZTpldmVub2RkO2ZpbGw6I0U2RjVGRjtlbmFibGUtYmFja2dyb3VuZDpuZXcgICAgO30KCS5zdDF7ZmlsbDojNEE5MEUyO30KCS5zdDJ7ZmlsbDpub25lO3N0cm9rZTojRThGNkZGO3N0cm9rZS13aWR0aDo4O30KPC9zdHlsZT4KPGcgaWQ9IlBhZ2UtMSI+Cgk8ZyBpZD0iRmlsZS1NYW5hZ2VyLS0tRmlsZVNldHMtRHJvcGRvd24iIHRyYW5zZm9ybT0idHJhbnNsYXRlKC02NTUuMDAwMDAwLCAtNjY3LjAwMDAwMCkiPgoJCTxyZWN0IGlkPSJSZWN0YW5nbGUiIHg9IjEiIHk9IjQ2OCIgY2xhc3M9InN0MCIgd2lkdGg9IjE0NDAiIGhlaWdodD0iNTQwIi8+CgkJPGcgaWQ9Imljb25zOC1kcmFnLWFuZC1kcm9wIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSg2NTUuMDAwMDAwLCA2NjcuMDAwMDAwKSI+CgkJCTxwYXRoIGlkPSJTaGFwZSIgY2xhc3M9InN0MSIgZD0iTTMuMiwwYzAsMC0wLjEsMC0wLjEsMEMzLjEsMCwzLDAsMi45LDBDMi44LDAsMi43LDAuMSwyLjYsMC4xQzIuNCwwLjIsMi4xLDAuMiwxLjgsMC4zCgkJCQlDMS43LDAuNCwxLjUsMC41LDEuNCwwLjZDMS4yLDAuOCwxLDAuOSwwLjksMUMwLjgsMS4xLDAuNywxLjMsMC42LDEuNEMwLjUsMS42LDAuMywxLjgsMC4yLDIuMUMwLjIsMi4yLDAuMiwyLjQsMC4xLDIuNQoJCQkJQzAuMSwyLjcsMCwyLjksMCwzLjF2MTMuMWMwLDEuNywxLjQsMy4xLDMuMiwzLjFzMy4yLTEuNCwzLjItMy4xVjYuNGgxM2MxLjcsMCwzLjEtMS40LDMuMS0zLjJTMjEuMSwwLDE5LjQsMEgzLjUKCQkJCUMzLjQsMCwzLjQsMCwzLjIsMEMzLjMsMCwzLjIsMCwzLjIsMHogTTMyLjEsMEMzMC40LDAsMjksMS40LDI5LDMuMnMxLjQsMy4yLDMuMSwzLjJoNi42YzEuNywwLDMuMS0xLjQsMy4xLTMuMlM0MC40LDAsMzguNywwCgkJCQlIMzIuMXogTTUxLjQsMGMtMS43LDAtMy4xLDEuNC0zLjEsMy4yczEuNCwzLjIsMy4xLDMuMmg2LjZjMS43LDAsMy4xLTEuNCwzLjEtMy4yUzU5LjgsMCw1OC4xLDBINTEuNHogTTcwLjcsMAoJCQkJYy0xLjcsMC0zLjEsMS40LTMuMSwzLjJzMS40LDMuMiwzLjEsMy4yaDYuNmMxLjcsMCwzLjEtMS40LDMuMS0zLjJTNzkuMSwwLDc3LjQsMEg3MC43eiBNOTAsMGMtMS43LDAtMy4xLDEuNC0zLjEsMy4yCgkJCQlzMS40LDMuMiwzLjEsMy4yaDEzdjkuOGMwLDEuNywxLjQsMy4xLDMuMiwzLjFjMS44LDAsMy4yLTEuNCwzLjItMy4xVjMuMWMwLTAuMi0wLjEtMC40LTAuMS0wLjZjMC0wLjIsMC0wLjMtMC4xLTAuNQoJCQkJYy0wLjEtMC4yLTAuMi0wLjQtMC40LTAuN2MtMC4xLTAuMS0wLjItMC4zLTAuMy0wLjRjLTAuMi0wLjItMC4zLTAuMy0wLjUtMC40Yy0wLjItMC4xLTAuMy0wLjItMC41LTAuMwoJCQkJYy0wLjItMC4xLTAuNS0wLjEtMC44LTAuMmMtMC4xLDAtMC4yLTAuMS0wLjMtMC4xYy0wLjEsMC0wLjEsMC0wLjIsMGMwLDAtMC4xLDAtMC4xLDBjMCwwLDAsMC0wLjEsMGMtMC4xLDAtMC4xLDAtMC4yLDBIOTB6CgkJCQkgTTMuMiwyNS44Yy0xLjgsMC0zLjIsMS40LTMuMiwzLjF2Ni42YzAsMS43LDEuNCwzLjEsMy4yLDMuMXMzLjItMS40LDMuMi0zLjF2LTYuNkM2LjQsMjcuMiw1LDI1LjgsMy4yLDI1Ljh6IE0xMDYuMiwyNS44CgkJCQljLTEuOCwwLTMuMiwxLjQtMy4yLDMuMXY2LjZjMCwxLjcsMS40LDMuMSwzLjIsMy4xYzEuOCwwLDMuMi0xLjQsMy4yLTMuMXYtNi42QzEwOS41LDI3LjIsMTA4LDI1LjgsMTA2LjIsMjUuOHogTTMuMiw0NS4xCgkJCQljLTEuOCwwLTMuMiwxLjQtMy4yLDMuMXY2LjZDMCw1Ni41LDEuNCw1OCwzLjIsNThzMy4yLTEuNCwzLjItMy4xdi02LjZDNi40LDQ2LjUsNSw0NS4xLDMuMiw0NS4xeiBNNDEuOSw0NS4xCgkJCQlDMzQuOCw0NS4xLDI5LDUwLjksMjksNTh2NjEuMmMwLDcuMSw1LjgsMTIuOSwxMi45LDEyLjloNzcuM2M3LjEsMCwxMi45LTUuOCwxMi45LTEyLjlWNThjMC03LjEtNS44LTEyLjktMTIuOS0xMi45SDQxLjl6CgkJCQkgTTQxLjksNTEuNWg3Ny4zYzMuNiwwLDYuNCwyLjgsNi40LDYuNHY2MS4yYzAsMy42LTIuOCw2LjQtNi40LDYuNEg0MS45Yy0zLjYsMC02LjQtMi44LTYuNC02LjRWNTgKCQkJCUMzNS40LDU0LjQsMzguMyw1MS41LDQxLjksNTEuNXogTTMuMiw2NC40Yy0xLjgsMC0zLjIsMS40LTMuMiwzLjF2Ni42YzAsMS43LDEuNCwzLjEsMy4yLDMuMXMzLjItMS40LDMuMi0zLjF2LTYuNgoJCQkJQzYuNCw2NS44LDUsNjQuNCwzLjIsNjQuNHogTTc0LDcwLjhWMTAzbDcuOC02LjZsNC45LDExLjVsNC4xLTEuOGwtNS4yLTExLjNsMTAuOS0xLjRMNzQsNzAuOHogTTMuMiw4My43CgkJCQljLTEuOCwwLTMuMiwxLjQtMy4yLDMuMXYxMi43YzAsMC4xLDAsMC4xLDAsMC4yYzAsMCwwLDAsMCwwLjFjMCwwLDAsMC4xLDAsMC4xYzAsMC4xLDAsMC4xLDAsMC4yYzAsMC4xLDAuMSwwLjIsMC4xLDAuMwoJCQkJYzAuMSwwLjMsMC4xLDAuNSwwLjIsMC44YzAuMSwwLjIsMC4yLDAuMywwLjMsMC41YzAuMSwwLjIsMC4yLDAuNCwwLjQsMC41YzAuMSwwLjEsMC4zLDAuMiwwLjQsMC4zYzAuMiwwLjEsMC40LDAuMywwLjcsMC40CgkJCQljMC4xLDAuMSwwLjMsMC4xLDAuNSwwLjFjMC4yLDAsMC40LDAuMSwwLjYsMC4xaDE2LjNjMS43LDAsMy4xLTEuNCwzLjEtMy4ycy0xLjQtMy4yLTMuMS0zLjJoLTEzdi05LjhDNi40LDg1LjEsNSw4My43LDMuMiw4My43CgkJCQl6Ii8+CgkJPC9nPgoJCTxyZWN0IGlkPSJSZWN0YW5nbGUtQ29weS00IiB4PSI0IiB5PSIxNzciIGNsYXNzPSJzdDIiIHdpZHRoPSIxNDMyIiBoZWlnaHQ9IjgyOSIvPgoJPC9nPgo8L2c+Cjwvc3ZnPgo="
                     :alt="i18n.dropFilesHere">
                <span>

                <?php echo t("Drop files here or click to upload"); ?>
            </button>
        </div>

        <input type="file" class="ccm-file-upload-container-dropzone-file-element d-none" multiple="multiple">
    </div>
</div>

<style>
    .ccm-file-upload-container-dropzone {
        position: relative
    }

    .ccm-file-upload-container-dropzone .ccm-file-upload-container-dropzone-file-element {
        height: 100%;
        left: 0;
        opacity: 0;
        position: absolute;
        right: 0;
        top: 0;
        width: 100%;
        z-index: 9999
    }

    .ccm-file-upload-container-dropzone .ccm-file-upload-container-dropzone-file-element.hidden {
        display: none
    }

    .ccm-file-upload-container-wrapper {
        border: 1px solid #989898;
        cursor: pointer;
        display: block;
        font-family: Source Sans Pro, sans-serif;
        font-size: 16px;
        margin: auto;
        position: relative;
        width: 100%
    }

    .ccm-incoming-files-container {
        margin-bottom: 15px;
        max-height: 280px;
        overflow-y: scroll
    }

    .ccm-file-upload-container {
        display: block;
        height: 280px;
        overflow-y: auto;
        position: relative;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
        width: 100%
    }

    .ccm-file-upload-container.dz-started .dz-default {
        display: none
    }

    .ccm-file-upload-container .dz-default {
        height: 100%;
        position: relative;
        width: 100%
    }

    .ccm-file-upload-container .dz-default .dz-button {
        background-color: #ebf5fb;
        border: 0;
        cursor: pointer;
        font: inherit;
        font-size: 1.2em;
        height: 100%;
        outline: none;
        position: absolute;
        width: 100%
    }

    .ccm-file-upload-container .dz-default .dz-button img {
        display: block;
        margin: auto auto 15px;
        width: 150px
    }

    .ccm-file-upload-container .dz-default .dz-button span {
        display: block;
        font-size: 1em;
        margin: auto;
        width: 150px
    }

    .ccm-file-upload-wrapper {
        position: relative
    }

    .ccm-file-upload-wrapper .ccm-file-upload-item-wrapper {
        border: 1px solid rgba(0, 0, 0, .075);
        box-shadow: 5px 5px 7px -5px rgba(0, 0, 0, .75);
        display: block;
        float: left;
        margin: 20px 20px 50px;
        max-width: 150px;
        padding: 10px;
        position: relative;
        width: 25%
    }

    .ccm-file-upload-wrapper .ccm-file-upload-item-wrapper .ccm-file-upload-label {
        bottom: -30px;
        color: #4c4f56;
        font-size: 1em;
        left: 0;
        overflow: hidden;
        position: absolute;
        text-align: center;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 100%
    }

    .ccm-file-upload-wrapper .ccm-file-upload-item-wrapper .ccm-file-upload-item {
        background: #d8d8d8;
        overflow: hidden;
        padding-top: 100%;
        position: relative
    }

    .ccm-file-upload-wrapper .ccm-file-upload-item-wrapper .ccm-file-upload-item .ccm-file-upload-item-inner {
        height: 100%;
        position: absolute;
        top: 0;
        width: 100%
    }

    .ccm-file-upload-wrapper .ccm-file-upload-item-wrapper .ccm-file-upload-item .ccm-file-upload-item-inner .ccm-file-upload-image-wrapper {
        display: block;
        height: 100%;
        position: relative;
        width: 100%
    }

    .ccm-file-upload-wrapper .ccm-file-upload-item-wrapper .ccm-file-upload-item .ccm-file-upload-item-inner img {
        bottom: 0;
        height: auto;
        margin: auto;
        position: absolute;
        top: 0;
        transform: scale(1.2);
        width: 100%
    }

    .ccm-file-upload-wrapper.in-progress .ccm-file-upload-item-wrapper .ccm-file-upload-item .ccm-file-upload-item-inner img {
        filter: blur(5px)
    }

    .ccm-file-upload-wrapper .ccm-file-upload-item .ccm-file-upload-item-inner .ccm-file-upload-progress-text svg {
        color: #4c4f56;
        font-size: 1em;
        font-weight: 700;
        left: 0;
        position: absolute;
        top: 0;
        width: 100%
    }

    .ccm-file-upload-wrapper .ccm-file-upload-item .ccm-file-upload-item-inner .ccm-file-upload-progress {
        display: none;
        height: 100%;
        left: 0;
        padding: 10px;
        position: absolute;
        top: 0;
        width: 100%
    }

    .ccm-file-upload-wrapper .ccm-file-upload-item .ccm-file-upload-item-inner .ccm-file-upload-progress-text {
        display: none;
        height: 100%;
        left: 0;
        position: absolute;
        top: 0;
        width: 100%
    }

    .ccm-file-upload-wrapper.in-progress .ccm-file-upload-item-inner .ccm-file-upload-progress, .ccm-file-upload-wrapper.in-progress .ccm-file-upload-item-inner .ccm-file-upload-progress-text {
        display: block
    }
</style>

<script>
    var field = $(".ccm-file-upload").get(0);
    var previewElement = $(field).attr("data-preview-element");

    var dropzone = new Dropzone(field, {
        url: <?php echo json_encode((string)Url::to($controller->action('upload'))->setQuery(["dest" => $dest])) ?>,
        previewTemplate: document.getElementById(previewElement).innerHTML,

        success: function (file) {
            const $fileElement = $(file.previewElement)
            $fileElement.removeClass('in-progress')
        },

        uploadprogress: function (file, progress) {
            this.isUploadInProgress = true

            const $fileElement = $(file.previewElement)
            const circle = $fileElement.find('circle').get(0)
            const radius = circle.r.baseVal.value
            const circumference = radius * 2 * Math.PI

            circle.style.strokeDasharray = `${circumference} ${circumference}`
            circle.style.strokeDashoffset = `${circumference}`
            circle.style.strokeDashoffset = circumference - progress / 100 * circumference

            $fileElement.find('.ccm-file-upload-progress-text-value').html(parseInt(progress) + '%')
            $fileElement.addClass('in-progress')
        }
    });
</script>


<div class="dialog-buttons">
    <button class="btn btn-secondary float-left" data-dialog-action="cancel">
        <?php echo t('Close') ?>
    </button>
</div>

