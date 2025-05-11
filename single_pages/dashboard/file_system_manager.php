<?php

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\View\View;

/** @var array $config */

?>

<div class="ccm-dashboard-header-buttons">
    <?php
    /** @noinspection PhpUnhandledExceptionInspection */
    View::element('/dashboard/help', null, 'file_system_manager');
    ?>
</div>

<?php

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/did_you_know', null, 'file_system_manager');

?>

<div id="file-system-manager">
    <?php echo t("Please wait..."); ?>
</div>

<script type="text/html" id="file-system-manager-template">
    <div id="ccm-search-results-table">
        <table class="ccm-file-system-manager-list ccm-search-results-table ccm-search-results-table-icon">
            <thead>
                <tr>
                    <th>
                        &nbsp;
                    </th>

                    <th class="ccm-search-results-name <%- (orderBy == " name " ? (isAsc ? "ccm-results-list-active-sort-desc" : "ccm-results-list-active-sort-asc") : "") %>">
                        <a href="javascript:void(0);" class="order-column" data-order-by="name">
                            <?php echo t("Name"); ?>
                        </a>
                    </th>

                    <th class="ccm-search-results-name <%- (orderBy == " mimeType " ? (isAsc ? "ccm-results-list-active-sort-desc" : "ccm-results-list-active-sort-asc") : "") %>">
                        <a href="javascript:void(0);" class="order-column" data-order-by="mimeType">
                            <?php echo t("Type"); ?>
                        </a>
                    </th>

                    <th class="ccm-search-results-name  <%- (orderBy == " dateTime " ? (isAsc ? "ccm-results-list-active-sort-desc" : "ccm-results-list-active-sort-asc") : "") %>">
                        <a href="javascript:void(0);" class="order-column" data-order-by="dateTime">
                            <?php echo t("Date"); ?>
                        </a>
                    </th>

                    <th class="ccm-search-results-name <%- (orderBy == " size " ? (isAsc ? "ccm-results-list-active-sort-desc" : "ccm-results-list-active-sort-asc") : "") %>">
                        <a href="javascript:void(0);" class="order-column" data-order-by="size">
                            <?php echo t("Size"); ?>
                        </a>
                    </th>

                    <th>
                        &nbsp;
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

                <td class="ccm-search-results-menu-launcher">
                    <div class="dropdown float-end" data-menu="search-result">
                        <button class="btn btn-icon show" data-boundary="viewport" type="button">
                            <svg width="16" height="4">
                                <use xlink:href="#icon-menu-launcher"></use>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>

            <% index++ %>
            <% }); %>
            </tbody>
        </table>
    </div>
</script>

<!--suppress JSUnresolvedVariable -->
<script type="text/javascript">
    (function ($) {
        $(function () {
            window.Bitter.fileSystemManager.init(<?php /** @noinspection PhpComposerExtensionStubsInspection */echo json_encode($config); ?>);
        });
    })(jQuery);
</script>
