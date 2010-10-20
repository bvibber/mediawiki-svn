/*
*
*   Copyright (c) Microsoft. All rights reserved.
*
*	This code is licensed under the Apache License, Version 2.0.
*   THIS CODE IS PROVIDED *AS IS* WITHOUT WARRANTY OF
*   ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING ANY
*   IMPLIED WARRANTIES OF FITNESS FOR A PARTICULAR
*   PURPOSE, MERCHANTABILITY, OR NON-INFRINGEMENT.
*
*   The apache license details from 
*   ‘http://www.apache.org/licenses/’ are reproduced 
*   in ‘Apache2_license.txt’ 
*
*/


/*
* 
* Available Classes:
* 1) applyBackground     - Includes all the methods and properties to manipulate themes of the application
* 
*/

(function () {
    //includes all the methods and properties to manipulate themes of the application
    wikiBhasha.applyBackground = {
        //supported themes and their respective style sheets
        globalImages: {
            'body': wbGlobalSettings.imgBaseUrl + 'images/bg.png',
            'div.wbIndexHeader': wbGlobalSettings.imgBaseUrl + 'images/bg.png',
            '#wbSplashWindow': wbGlobalSettings.imgBaseUrl + 'images/loadingImage.png',
            '#wbContent': wbGlobalSettings.imgBaseUrl + 'images/selectionBg.png',
            '#wbFooterDiv': wbGlobalSettings.imgBaseUrl + 'images/selectionBgBottom.png',
            '#wbLogoContainer': wbGlobalSettings.imgBaseUrl + 'images/logoBlack.png',
            '#wbWindowTopLeft': wbGlobalSettings.imgBaseUrl + 'images/tabToolbarTopLeft.png',
            '.wbTrans': wbGlobalSettings.imgBaseUrl + 'images/trans.gif',
            '.wbWindowTopRight': wbGlobalSettings.imgBaseUrl + 'images/tabToolbarTopRight.png',
            '.wbWindowBottomLeft': wbGlobalSettings.imgBaseUrl + 'images/tabToolbarBottomLeft.png',
            '.wbWindowBottomRight': wbGlobalSettings.imgBaseUrl + 'images/tabToolbarBottomRight.png',
            'a.wbSearchLink': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbSearchLink:hover': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            '.wbSearchExit': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbExit': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            '.workFlowBtnClicked': wbGlobalSettings.imgBaseUrl + 'images/button.gif',
            '#wbPreviousButton': wbGlobalSettings.imgBaseUrl + 'images/arrowLeft.png',
            '#wbPreviousButton:hover': wbGlobalSettings.imgBaseUrl + 'images/arrowLeftHoverState.png',
            '#wbNextButton': wbGlobalSettings.imgBaseUrl + 'images/arrowRight.png',
            '#wbNextButton:hover': wbGlobalSettings.imgBaseUrl + 'images/arrowRightHoverState.png',
            '.wbCollectNormal': wbGlobalSettings.imgBaseUrl + 'images/researchButton.png',
            '#wbCollectNormal:hover': wbGlobalSettings.imgBaseUrl + 'images/researchButtonActiveState.png',
            '.wbCollectActive': wbGlobalSettings.imgBaseUrl + 'images/researchButtonActiveState.png',
            '.wbComposeNormal': wbGlobalSettings.imgBaseUrl + 'images/composeButton.png',
            '.wbComposeNormal:hover': wbGlobalSettings.imgBaseUrl + 'images/composeButtonActiveState.png',
            '.wbComposeActive': wbGlobalSettings.imgBaseUrl + 'images/composeButtonActiveState.png',
            '.wbPublishNormal': wbGlobalSettings.imgBaseUrl + 'images/publishButton.png',
            '.wbPublishNormal:hover': wbGlobalSettings.imgBaseUrl + 'images/publishButtonActive.png',
            '.wbPublishActive': wbGlobalSettings.imgBaseUrl + 'images/publishButtonActive.png',
            '.wbLightBlueTitleBar': wbGlobalSettings.imgBaseUrl + 'images/titlebarBgLightBlue.png',
            '.wbLightYellowTitleBar': wbGlobalSettings.imgBaseUrl + 'images/titlebarBgLightYellow.png'
        },
        themeBlack: {
            '.wbBgTopRight': wbGlobalSettings.imgBaseUrl + 'images/themes/black/topRightCorner.png',
            '.wbBgTopLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/black/topLeft.png',
            '.wbBgContentArea': wbGlobalSettings.imgBaseUrl + 'images/themes/black/bg.png',
            '.wbBgContentAreaRight': wbGlobalSettings.imgBaseUrl + 'images/themes/black/bgRight.png',
            '.wbBgBottomLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/black/bottomLeft.png',
            '.wbBgBottomRight': wbGlobalSettings.imgBaseUrl + 'images/themes/black/bottomRightCorner.png',
            '.wbCollapseWindowToolbarCenter': wbGlobalSettings.imgBaseUrl + 'images/themes/black/toolbarBg.png',
            '.wbWindowTopLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/black/tabToolbarTopLeft.png',
            '.wbWindowTopRight': wbGlobalSettings.imgBaseUrl + 'images/themes/black/tabToolbarTopRight.png',
            '.wbWindowToolbarLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/black/toolbarBg.png',
            '.wbWindowToolbarCenter': wbGlobalSettings.imgBaseUrl + 'images/themes/black/toolbarBg.png',
            '.wbWindowToolbarRight': wbGlobalSettings.imgBaseUrl + 'images/themes/black/toolbarBg.png',
            '.wbWindowToolbarRight': wbGlobalSettings.imgBaseUrl + 'images/themes/black/toolbarBg.png',
            '.wbWindowBottomLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/black/tabToolbarBottomLeft.png',
            '.wbWindowBottomRight': wbGlobalSettings.imgBaseUrl + 'images/themes/black/tabToolbarBottomRight.png',
            'li.wbHeader': wbGlobalSettings.imgBaseUrl + 'images/themes/black/tabUnselectedLeft.png',
            'li.wbLiright': wbGlobalSettings.imgBaseUrl + 'images/themes/black/tabUnselectedRight.png',
            'li.wbSelected': wbGlobalSettings.imgBaseUrl + 'images/themes/black/tabSelectedLeft.png',
            'li.wbSelectedright': wbGlobalSettings.imgBaseUrl + 'images/themes/black/tabSelectedRight.png',
            'a.wbHelp': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbMaximize': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbMinimize': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbClose': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbDelete': wbGlobalSettings.imgBaseUrl + 'images/delete.png',
            'a.wbBlueIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbSilverIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbBlackIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbBlueSelectedIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbSilverSelectedIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbBlackSelectedIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbSearchButton': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbScratchPadButton': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbFeedbackButton': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'INPUT.wbTranslateButton': wbGlobalSettings.imgBaseUrl + 'images/translation.png',
            'INPUT.wbClearButton': wbGlobalSettings.imgBaseUrl + 'images/clearAll.png',
            'INPUT.wbButton': wbGlobalSettings.imgBaseUrl + 'images/wikiSmall.png',
            'INPUT.wbGetEntireButton': wbGlobalSettings.imgBaseUrl + 'images/move.png',
            'INPUT.wbCollapseButton': wbGlobalSettings.imgBaseUrl + 'images/collapse.png',
            'INPUT.wbCollapseRightButton': wbGlobalSettings.imgBaseUrl + 'images/collapseRight.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/page_white_edit.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/cut.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/page_white_copy.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/page_white_paste.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/page_white_delete.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/door.png',
            '.wbTutorial': wbGlobalSettings.imgBaseUrl + 'images/logoBlack.png',
            '.wbTutorialPreviousBtn': wbGlobalSettings.imgBaseUrl + 'images/themes/black/previous.png',
            '.wbTutorialNextBtn': wbGlobalSettings.imgBaseUrl + 'images/themes/black/next.png',
            '.wbTutorialDisabledPreviousBtn': wbGlobalSettings.imgBaseUrl + 'images/disabledButtonPrevious.png',
            '.wbTutorialDisabledNextBtn': wbGlobalSettings.imgBaseUrl + 'images/disabledButtonNext.png',
            '.wbSplitter': wbGlobalSettings.imgBaseUrl + 'images/splitterImg.png',
            '.wbTutorialBgContentArea': wbGlobalSettings.imgBaseUrl + 'images/themes/black/helpBg.png'
        },

        themeSilver: {
            '.wbBgTopRight': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/topRightCorner.png',
            '.wbBgTopLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/topLeft.png',
            '.wbBgContentArea': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/bg.png',
            '.wbBgContentAreaRight': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/bgRight.png',
            '.wbBgBottomLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/bottomLeft.png',
            '.wbBgBottomRight': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/bottomRightCorner.png',
            '.wbCollapseWindowToolbarCenter': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/toolbarBg.png',
            '.wbWindowTopLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/tabToolbarTopLeft.png',
            '.wbWindowTopRight': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/tabToolbarTopRight.png',
            '.wbWindowToolbarLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/toolbarBg.png',
            '.wbWindowToolbarCenter': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/toolbarBg.png',
            '.wbWindowToolbarRight': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/toolbarBg.png',
            '.wbWindowToolbarRight': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/toolbarBg.png',
            '.wbWindowBottomLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/tabToolbarBottomLeft.png',
            '.wbWindowBottomRight': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/tabToolbarBottomRight.png',
            'li.wbHeader': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/tabUnselectedLeft.png',
            'li.wbLiright': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/tabUnselectedRight.png',
            'li.wbSelected': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/tabSelectedLeft.png',
            'li.wbSelectedright': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/tabSelectedRight.png',
            'a.wbHelp': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbMaximize': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbMinimize': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbClose': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbDelete': wbGlobalSettings.imgBaseUrl + 'images/delete.png',
            'a.wbBlueIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbSilverIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbBlackIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbBlueSelectedIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbSilverSelectedIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbBlackSelectedIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbSearchButton': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbScratchPadButton': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbFeedbackButton': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'INPUT.wbTranslateButton': wbGlobalSettings.imgBaseUrl + 'images/translation.png',
            'INPUT.wbClearButton': wbGlobalSettings.imgBaseUrl + 'images/clearAll.png',
            'INPUT.wbButton': wbGlobalSettings.imgBaseUrl + 'images/wikiSmall.png',
            'INPUT.wbGetEntireButton': wbGlobalSettings.imgBaseUrl + 'images/move.png',
            'INPUT.wbCollapseButton': wbGlobalSettings.imgBaseUrl + 'images/collapse.png',
            'INPUT.wbCollapseRightButton': wbGlobalSettings.imgBaseUrl + 'images/collapseRight.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/page_white_edit.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/cut.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/page_white_copy.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/page_white_paste.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/page_white_delete.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/door.png',
            '.wbTutorial': wbGlobalSettings.imgBaseUrl + 'images/logoBlack.png',
            '.wbTutorialPreviousBtn': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/previous.png',
            '.wbTutorialNextBtn': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/next.png',
            '.wbTutorialDisabledPreviousBtn': wbGlobalSettings.imgBaseUrl + 'images/disabledButtonPrevious.png',
            '.wbTutorialDisabledNextBtn': wbGlobalSettings.imgBaseUrl + 'images/disabledButtonNext.png',
            '.wbSplitter': wbGlobalSettings.imgBaseUrl + 'images/splitterImg.png',
            '.wbTutorialBgContentArea': wbGlobalSettings.imgBaseUrl + 'images/themes/silver/helpBg.png'
        },

        themeBlue: {
            '.wbBgTopRight': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/topRightCorner.png',
            '.wbBgTopLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/topLeft.png',
            '.wbBgContentArea': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/bg.png',
            '.wbBgContentAreaRight': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/bgRight.png',
            '.wbBgBottomLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/bottomLeft.png',
            '.wbBgBottomRight': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/bottomRightCorner.png',
            '.wbCollapseWindowToolbarCenter': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/toolbarBg.png',
            '.wbWindowTopLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/tabToolbarTopLeft.png',
            '.wbWindowTopRight': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/tabToolbarTopRight.png',
            '.wbWindowToolbarLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/toolbarBg.png',
            '.wbWindowToolbarCenter': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/toolbarBg.png',
            '.wbWindowToolbarRight': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/toolbarBg.png',
            '.wbWindowToolbarRight': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/toolbarBg.png',
            '.wbWindowBottomLeft': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/tabToolbarBottomLeft.png',
            '.wbWindowBottomRight': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/tabToolbarBottomRight.png',
            'li.wbHeader': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/tabUnselectedLeft.png',
            'li.wbLiright': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/tabUnselectedRight.png',
            'li.wbSelected': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/tabSelectedLeft.png',
            'li.wbSelectedright': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/tabSelectedRight.png',
            'a.wbHelp': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbMaximize': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbMinimize': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbClose': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbDelete': wbGlobalSettings.imgBaseUrl + 'images/delete.png',
            'a.wbBlueIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbSilverIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbBlackIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbBlueSelectedIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbSilverSelectedIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbBlackSelectedIcon': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbSearchButton': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbScratchPadButton': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'a.wbFeedbackButton': wbGlobalSettings.imgBaseUrl + 'images/TopRightIconSpriteSquare.png',
            'INPUT.wbTranslateButton': wbGlobalSettings.imgBaseUrl + 'images/translation.png',
            'INPUT.wbClearButton': wbGlobalSettings.imgBaseUrl + 'images/clearAll.png',
            'INPUT.wbButton': wbGlobalSettings.imgBaseUrl + 'images/wikiSmall.png',
            'INPUT.wbGetEntireButton': wbGlobalSettings.imgBaseUrl + 'images/move.png',
            'INPUT.wbCollapseButton': wbGlobalSettings.imgBaseUrl + 'images/collapse.png',
            'INPUT.wbCollapseRightButton': wbGlobalSettings.imgBaseUrl + 'images/collapseRight.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/page_white_edit.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/cut.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/page_white_copy.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/page_white_paste.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/page_white_delete.png',
            '.wbContextMenu': wbGlobalSettings.imgBaseUrl + 'images/door.png',
            '.wbTutorial': wbGlobalSettings.imgBaseUrl + 'images/logoBlack.png',
            '.wbTutorialPreviousBtn': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/previous.png',
            '.wbTutorialNextBtn': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/next.png',
            '.wbTutorialDisabledPreviousBtn': wbGlobalSettings.imgBaseUrl + 'images/disabledButtonPrevious.png',
            '.wbTutorialDisabledNextBtn': wbGlobalSettings.imgBaseUrl + 'images/disabledButtonNext.png',
            '.wbSplitter': wbGlobalSettings.imgBaseUrl + 'images/splitterImg.png',
            '.wbTutorialBgContentArea': wbGlobalSettings.imgBaseUrl + 'images/themes/blue/helpBg.png'
        },

        //bind the click event on available theme buttons
        loadBackgroundImages: function (imagesListObject) {
            if (imagesListObject) {
                for (var key in imagesListObject) {
                    $(key).css("background-image", "url(" + imagesListObject[key] + ")");
                }
            }
        },

        changeImagePath: function (theme) {
            wbImageRef.loadBackgroundImages(wbImageRef.globalImages);
            switch (theme) {
                case 'Black':
                    {
                        wbImageRef.loadBackgroundImages(wbImageRef.themeBlack);
                    } break;
                case 'Silver':
                    {
                        wbImageRef.loadBackgroundImages(wbImageRef.themeSilver);
                    } break;
                case 'Blue':
                default:
                    {
                        wbImageRef.loadBackgroundImages(wbImageRef.themeBlue);
                    }
            }
        }
    }

    //shortcut to call wikiBhasha.applyBackground
    wbImageRef = wikiBhasha.applyBackground;

})();
