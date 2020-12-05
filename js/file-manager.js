/**
 * @project:   File Manager
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global _, NProgress, ConcreteMenu, ConcreteAlert */


(function (global, $) {
    'use strict';

    global.Bitter = global.Bitter || {};

    global.Bitter.fileManager = {
        config: null,
        results: {
            orderBy: "name",
            isAsc: true
        },
        download: function (file) {
            global.location.href = global.Bitter.fileManager.config.urls.download + "?file=" + encodeURI(file);
        },
        getBasename: function (path) {
            return path.split('/').reverse()[0];
        },
        delete: function (file) {
            ConcreteAlert.confirm(
                    global.Bitter.fileManager.config.i18n.deleteFileText.replace("{file}", this.getBasename(file)),
                    function () {
                        $(".ui-dialog").hide();
                        $(".ui-widget-overlay").remove();

                        NProgress.start();

                        $.ajax({
                            url: global.Bitter.fileManager.config.urls.delete,
                            type: 'GET',
                            data: {
                                'file': file
                            },
                            dataType: 'json',
                            success: function (result) {
                                NProgress.done();

                                if (result.success) {
                                    global.Bitter.fileManager.dir(); // update dir
                                } else {
                                    // @todo: add error handling
                                }

                            }, error: function () {
                                // @todo: add error handling

                                NProgress.done();
                            }
                        });

                    },
                    'btn-danger',
                    global.Bitter.fileManager.config.i18n.deleteFileButton
                    );
        },
        dir: function (dir, orderBy, isAsc) {
            if (typeof dir !== "string" || dir === "") {
                dir = global.Bitter.fileManager.results.dir;
            }

            if (typeof orderBy !== "string" || orderBy === "") {
                orderBy = global.Bitter.fileManager.results.orderBy;
            }

            if (typeof isAsc !== "boolean") {
                isAsc = global.Bitter.fileManager.results.isAsc;
            }

            NProgress.start();

            $.ajax({
                url: global.Bitter.fileManager.config.urls.dir,
                type: 'POST',
                data: {
                    'dir': dir,
                    'orderBy': orderBy,
                    'isAsc': isAsc ? 1 : 0
                },
                dataType: 'json',
                success: function (results) {
                    var template = $("#file-manager-template").html();

                    global.Bitter.fileManager.results = results;

                    var html = _.template(template, results);

                    $("#file-manager").html(html);

                    $("h1").html(results.dir);

                    /*
                     * Bind events for table sorting
                     */

                    $("#file-manager thead .order-column").on("click", function () {
                        var orderBy = $(this).data("orderBy");
                        var isAsc = global.Bitter.fileManager.results.isAsc;

                        if (orderBy === global.Bitter.fileManager.results.orderBy) {
                            isAsc = !isAsc;
                        }

                        global.Bitter.fileManager.dir(null, orderBy, isAsc);
                    });

                    /*
                     * Bind events to result entries
                     */

                    $("#file-manager tbody tr").on("mouseover", function () {
                        $(this).addClass("ccm-search-select-hover");
                    }).on("mouseout", function () {
                        $(this).removeClass("ccm-search-select-hover");
                    }).on("click", function () {
                        var selectedIndex = $(this).data("index");

                        var selectedFile = global.Bitter.fileManager.results.files[selectedIndex];

                        if (typeof selectedFile === "object") {
                            if (selectedFile.isDirectory) {
                                global.document.location.hash = selectedFile.file;
                            }
                        }
                    }).contextmenu(function (e) {
                        e.preventDefault();

                        var $el = $(this);

                        $("#file-manager tbody tr").each(function () {
                            if ($(this).data("index") != $el.data("index")) {
                                $(this).removeClass("ccm-menu-item-active");
                            }
                        });

                        var selectedIndex = $(this).data("index");

                        var selectedFile = global.Bitter.fileManager.results.files[selectedIndex];

                        if (typeof selectedFile === "object") {
                            var concreteMenu = new ConcreteMenu($(this), {
                                menu: selectedFile.menu,
                                handle: 'none',
                                container: $(this)
                            });

                            concreteMenu.show(e);

                            $("#ccm-popover-menu-container").find('a[data-file-manager-action=delete]').on('click', function (e) {
                                e.preventDefault();

                                global.Bitter.fileManager.delete($(this).data("file"));

                                return false;
                            });
                        }

                        return false;
                    });

                    NProgress.done();
                }, error: function () {
                    // @todo: add error handling

                    NProgress.done();
                }
            });
        },
        init: function (config) {
            global.Bitter.fileManager.config = config;



            // Add pollyfill for version 8.0.0
            if (typeof ConcreteAlert.confirm === "undefined") {
                ConcreteAlert.confirm = function (a, c, d, e) {
                    var f = $('<div id="ccm-popup-confirmation" class="ccm-ui"><div id="ccm-popup-confirmation-message">' + a + "</div>");
                    d = d ? "btn " + d : "btn btn-primary",
                            e = e ? e : global.Bitter.fileManager.config.i18n.go,
                            f.dialog({
                                title: global.Bitter.fileManager.config.i18n.confirm,
                                width: 500,
                                maxHeight: 500,
                                modal: !0,
                                dialogClass: "ccm-ui",
                                close: function () {
                                    f.remove()
                                },
                                buttons: [{}],
                                open: function () {
                                    $(this).parent().find(".ui-dialog-buttonpane").addClass("ccm-ui").html(""),
                                            $(this).parent().find(".ui-dialog-buttonpane").append('<button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-default">' + global.Bitter.fileManager.config.i18n.cancel + '</button><button data-dialog-action="submit-confirmation-dialog" class="btn ' + d + ' pull-right">' + e + "</button></div>")
                                }
                            }),
                            f.parent().on("click", "button[data-dialog-action=submit-confirmation-dialog]", function () {
                        return c()
                    })
                };
            }

            /*
             * Dir file manager on hash change
             */

            $(global).on('hashchange', function (e) {
                var path = global.document.location.hash.substr(1);
                global.Bitter.fileManager.dir(path);
            });

            /*
             * Load initial directory
             */

            if (global.document.location.hash.length > 1) {
                var path = global.document.location.hash.substr(1);
                global.Bitter.fileManager.dir(path);
            } else {
                global.document.location.hash = global.Bitter.fileManager.config.initDir;
            }
        }
    };

})(window, jQuery);