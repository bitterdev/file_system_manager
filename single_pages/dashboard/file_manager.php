<?php
/**
 * @project:   File Manager
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */
defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\View\View;

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'file_manager');

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/reminder', ["packageHandle" => "file_manager", "rateUrl" => "https://www.concrete5.org/marketplace/addons/file-manager/reviews"], 'file_manager');

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/license_check', ["packageHandle" => "file_manager"], 'file_manager');
?>

<div id="file-manager">
    <?php echo t("Please wait..."); ?>
</div>

<script type="text/html" id="file-manager-template">
    <table class="ccm-file-manager-list ccm-search-results-table ccm-search-results-table-icon">
        <thead>
            <tr>
                <th>
                    <div class="dropdown">
                        <button class="btn btn-menu-launcher" disabled="" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
                    </div>
                </th>

                <th class="ccm-search-results-name <%- (orderBy == "name" ? (isAsc ? "ccm-results-list-active-sort-desc" : "ccm-results-list-active-sort-asc") : "") %>">
                    <a href="javascript:void(0);" class="order-column" data-order-by="name">
                            <?php echo t("Name"); ?>
                    </a>
                </th>

                <th class="ccm-search-results-name <%- (orderBy == "mimeType" ? (isAsc ? "ccm-results-list-active-sort-desc" : "ccm-results-list-active-sort-asc") : "") %>">
                    <a href="javascript:void(0);" class="order-column" data-order-by="mimeType">
                            <?php echo t("Type"); ?>
                    </a>
                </th>

                <th class="<%- (orderBy == "dateTime" ? (isAsc ? "ccm-results-list-active-sort-desc" : "ccm-results-list-active-sort-asc") : "") %>">
                    <a href="javascript:void(0);" class="order-column" data-order-by="dateTime">
                            <?php echo t("Date"); ?>
                    </a>
                </th>

                <th class="<%- (orderBy == "size" ? (isAsc ? "ccm-results-list-active-sort-desc" : "ccm-results-list-active-sort-asc") : "") %>">
                    <a href="javascript:void(0);" class="order-column" data-order-by="size">
                            <?php echo t("Size"); ?>
                    </a>
                </th>
            </tr>
        </thead>

        <tbody>
            <% var index = 0 %>

            <% _.each(files, function(file){ %>
            <tr data-index="<%- index %>">
                <td class="ccm-search-results-icon">
                    <i class="fa <%- file.fontAwesomeHandle %>"></i>
                </td>

                <td>
                    <%- file.name %>
                </td>

                <td>
                    <%- file.mimeType %>
                </td>

                <td>
                    <%- file.displayDateTime %>
                </td>

                <td>
                    <%- file.displaySize %>
                </td>
            </tr>

            <% index++ %>
            <% }); %>
        </tbody>
    </table>
</script>

<!--suppress JSUnresolvedVariable -->
    <script type="text/javascript">
    (function ($) {
        $(function () {
            window.Bitter.fileManager.init(<?php /** @noinspection PhpComposerExtensionStubsInspection */echo json_encode($config); ?>);
        });
    })(jQuery);
</script>

<?php
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/did_you_know', ["packageHandle" => "file_manager"], 'file_manager');