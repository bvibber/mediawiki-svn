/********************************************************
*                                                       *
*   Copyright (C) Microsoft. All rights reserved.       *
*                                                       *
********************************************************/

/*

BSD license:

Copyright (c) 2010, Microsoft 
All rights reserved.

Redistribution and use in $source and binary forms, with or without modification, are permitted provided that the following conditions are met:


•	Redistributions of $source code must retain the above copyright notice, this list of conditions and the following disclaimer.

•	Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

•   Neither the name of Microsoft nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
    
•   THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

/*
* 
* Available Classes:
* 1) wikiMarkupEditWindow   - Describes the wiki markup edit window, which enables user to edit the wiki markup and privew the same and save the same to the target language article
* 
*/

//make sure the namespace exists.
if (typeof (wikiBhasha.windowManagement) === "undefined") {
    wikiBhasha.windowManagement = {};
}

(function() {
    //describes the search window, which enables user to search various articles in wikipedia 
    //for contributing to the target language article
    wikiBhasha.windowManagement.wikiMarkupEditWindow = {

        //search window div Id.
        windowId: "wbWikiMarkupEditWindow",

        wikiMarkupEditWindowHTML: "",

        wikiMarkupEditObject:'',

        //displays search window on application UI
        show: function(elem, e) {
            var $wikiMarkupEditWindowElem = $("#" + this.windowId),
                wikiMarkupText = elem ? elem.getAttribute("_data") : null;
            //store the mouse hover element reference
            this.wikiMarkupEditObject=elem;

            //check if the window was created already
            if ($wikiMarkupEditWindowElem.length === 0) {
                wbUIHelper.createWindow(this.windowId, wbGlobalSettings.wikiMarkupEditWindowHTML);
                 //popup window tabs
                $('#wbWikiMarkupEditTabsContainer').tabs();

                if (wbWorkflow.config.currentPane) {
                    //make the wiki markup tab editable
                    $("#wbWikiMarkupEditTab").attr("contentEditable", "true");
                } else { 
                    $("#wbWikiMarkupEditTab").attr("contentEditable", "false");
                }
                
                //priview $link
                $("#wbWikiMarkupEditPrivewLink").click(function(){ wbWikiMarkupEdit.priview();});
                //submit links
                $("#wbWikiMarkupEditSubmitLink").click(function(){ wbWikiMarkupEdit.submit();});
                //clicking out side the div hide the wiki markup edit popup
                $('#wbTranslationWindow').click(function() { wbWikiMarkupEdit.hide(); });
                $('#wbWikiMarkupEditDiv').click(function(event){  event.stopPropagation(); });
                //close button
                $(".wbExit").click(function(){wbWikiMarkupEdit.hide();});

                $("#wbWikiMarkupEditHeader").html(wbLocal.wbWikiMarkupEditHeader);
                $wikiMarkupEditWindowElem = $("#" + this.windowId);
                wbUIHelper.makeDraggable(this.windowId, "wbWikiMarkupEditDraggableHandle");
            }
            else {
                $wikiMarkupEditWindowElem.show();
            } 
            //popup window tabs
            $('#wbWikiMarkupEditTabsContainer').tabs("option", "selected", 0);
            wbUIHelper.setWindowOnContext(this.windowId, e);
            //populate the $content
            $("#wbWikiMarkupEditTab").html(wikiMarkupText);
            //clear the priview tab
            $("#wbWikiMarkupEditPrivewTab").html("");
           
            // bring the window always on top.
            $wikiMarkupEditWindowElem.maxZIndex({ inc: 5 });

            // log the usage of search window.
            wbLoggerService.logFeatureUsage(wbGlobalSettings.sessionId, "WikiMarkupEditWindowInvoked");
        },

        priview : function(){
            wbWikiSite.getPriviewContent(wbGlobalSettings.targetLanguageCode, wbGlobalSettings.targetLanguageArticleTitle, $("#wbWikiMarkupEditTab").html(), function(priviewData){
            $("#wbWikiMarkupEditPrivewTab").html(priviewData);
                });
        },

        submit : function(){
            wbUtil.setDataAttribute(wbWikiMarkupEdit.wikiMarkupEditObject, $("#wbWikiMarkupEditTab").html());
            this.hide();
        },

        //removes the window from the application window
        unload: function() {
            wbUIHelper.removeWindow(this.windowId);
        },

        //hides the window from application UI
        hide: function() {
            $("#wbWikiMarkupEditTab").html("");
            //clear the priview tab
            $("#wbWikiMarkupEditPrivewTab").html("");
            $("#" + this.windowId).hide();
        }
    };

    //shortcut to call wikiBhasha.windowManagement.wikiMarkupEditWindow
    wbWikiMarkupEdit = wikiBhasha.windowManagement.wikiMarkupEditWindow;
})();