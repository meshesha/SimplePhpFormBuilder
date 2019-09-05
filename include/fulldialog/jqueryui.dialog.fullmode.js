
/**
 * jqueryui.dialog.fullmode.js
 * Ver. : 1.0.0 
 * last update: 03/09/2019
 * Author: meshesha , https://github.com/meshesha
 * LICENSE: MIT
 * url:https://meshesha.github.io/jqueryui.dialog.fullmode
 */

(function ($) {
    $.fn.dialogfullmode = function (options) {
        var settings = $.extend({

        }, options);

        //var imageSource = settings.imageSource;
        //console.log()
        //var dlgId = this[0].id;
        var self = this;

        window.onload = function () {
            //sessionStorage.clear();
            if (sessionStorage.dfmObj !== undefined) {
                sessionStorage.removeItem('dfmObj');
            }
        }

        $(".dialog-full-mode").on("dialogopen", function (event, ui) {
            if ($(this).attr("dfm-id") === undefined) {
                //console.log(sessionStorage.dfmObj)
                if (sessionStorage.dfmObj !== undefined) {
                    var dfmAry = JSON.parse(sessionStorage.dfmObj);
                    var lastObj = dfmAry[dfmAry.length - 1];
                    var indx = lastObj.index;
                    var nextIndex = Number(indx) + 1;
                    $(this).attr("dfm-id", nextIndex);
                    var obj = {
                        index: nextIndex,
                        isFull: "0"
                    }
                    dfmAry.push(obj);
                } else {
                    var obj = {
                        index: 1,
                        isFull: "0"
                    }
                    var dfmAry = ["", obj];
                    $(this).attr("dfm-id", "1");
                }
                sessionStorage.dfmObj = JSON.stringify(dfmAry);
            } else {
                //var dfmId = $(this).attr("dfm-id");
                //var dfmAry = JSON.parse(sessionStorage.dfmObj);
            }
        });
        $(".dialog-full-mode").on("dialogclose", function (event, ui) {
            var dfmId = Number($(this).attr("dfm-id"));
            var dfmAry = JSON.parse(sessionStorage.dfmObj);
            var dialogObj = dfmAry[dfmId];
            if (dialogObj.isFull == "1") {
                dialogObj.isFull = "0";
                //enable drag and resize 
                $(".dialog-full-mode").resizable('enable');
                $(".dialog-full-mode").draggable('enable');

                //
                $(".fullscreen-btn").attr("title", "full mode");

                dfmAry[dfmId] = dialogObj;
                sessionStorage.dfmObj = JSON.stringify(dfmAry);
            }
        })
        $(".dialog-full-mode").on("dialogresize", function (event, ui) {
            self.setDimensionPosition(this);
        });
        $(".dialog-full-mode").on("dialogdrag", function (event, ui) {
            self.setDimensionPosition(this);
        });

        $(".dialog-full-mode")
            .children(".ui-dialog-titlebar")
            .append("<button title='full mode' class='ui-button ui-corner-all ui-widget ui-button-icon-only ui-button-fullscreen'><span class='fullscreen-btn ui-button-icon ui-icon ui-icon-arrow-4-diag'></span></button>");
        $(".ui-button-fullscreen").css({
            "position": "absolute",
            "right": "1.6em",
            "top": "50%",
            "width": "20px",
            "height": "20px",
            "margin": "-10px 0 0 0",
            "padding": "1px"
        });

        //firefox,edge,ie11
        $('.dialog-full-mode').on('click', '.ui-button-fullscreen', function () {
            var dialogWin = $(this).parent().parent();//.parent();
            var titleBar = $(dialogWin).children(".ui-dialog-titlebar");
            var contentWin = $(dialogWin).children(".ui-dialog-content");//.children(".ui-dialog-titlebar").next()

            var dfmId = Number($(dialogWin).attr("dfm-id"));
            var dfmAry = JSON.parse(sessionStorage.dfmObj);
            var dialogObj = dfmAry[dfmId];

            var scrollW = self.getScrollBarWidth()
            var winWidth = $(window).width() - 2 * scrollW;
            var winHeight = $(window).height() - 2 * scrollW;

            if (dialogObj.isFull == "1") {
                dialogObj.isFull = "0";

                //return to ordinal state
                $(dialogWin).css("top", dialogObj.top);
                $(dialogWin).css("left", dialogObj.left);
                $(dialogWin).width(dialogObj.width);
                $(dialogWin).height(dialogObj.height);

                $(contentWin).width(dialogObj.contentWidth)
                $(contentWin).height(dialogObj.contentHeight);

                dfmAry[dfmId] = dialogObj;
                sessionStorage.dfmObj = JSON.stringify(dfmAry);

                //enable drag and resize 
                $(".dialog-full-mode").resizable('enable');
                $(".dialog-full-mode").draggable('enable');

                //
                $(".fullscreen-btn").attr("title", "full mode");
            } else {
                dialogObj.isFull = "1";

                //save original state
                dialogObj.top = $(dialogWin).css("top");
                dialogObj.left = $(dialogWin).css("left");
                dialogObj.width = $(dialogWin).width();
                dialogObj.height = $(dialogWin).height();

                dialogObj.contentWidth = $(contentWin).width();
                dialogObj.contentHeight = $(contentWin).height();

                dfmAry[dfmId] = dialogObj;
                sessionStorage.dfmObj = JSON.stringify(dfmAry);

                //set full screen state
                $(dialogWin).css("top", 0);
                $(dialogWin).css("left", 0);

                $(dialogWin).width(winWidth);
                $(dialogWin).height(winHeight);
                //$(contentWin).css("height", "85%");

                $(contentWin).width(winWidth - 2 * scrollW);
                $(contentWin).height(winHeight - $(titleBar).outerHeight() - 2 * scrollW);

                //disable drag and resize-
                $(".dialog-full-mode").resizable('disable');
                $(".dialog-full-mode").draggable('disable');

                //
                $(".fullscreen-btn").attr("title", "exit full mode");
            }
        });

        self.setDimensionPosition = function (obj) {

            var dfmId = Number($(obj).attr("dfm-id"));
            var dfmAry = JSON.parse(sessionStorage.dfmObj);
            var dialogObj = dfmAry[dfmId];


            dialogObj.top = $(obj).css("top");
            dialogObj.left = $(obj).css("left");
            dialogObj.width = $(obj).width();
            dialogObj.height = $(obj).height();
            dfmAry[dfmId] = dialogObj;
            sessionStorage.dfmObj = JSON.stringify(dfmAry);
        }
        self.getScrollBarWidth = function () {
            var $outer = $('<div>').css({ visibility: 'hidden', width: 100, overflow: 'scroll' }).appendTo('body'),
                widthWithScroll = $('<div>').css({ width: '100%' }).appendTo($outer).outerWidth();
            $outer.remove();
            return 100 - widthWithScroll;
        };
    }
}(jQuery));
