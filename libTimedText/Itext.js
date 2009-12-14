/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is HTML5 video itext demonstration code.
 *
 * The Initial Developer of the Original Code is Mozilla Corporation.
 * Portions created by the Initial Developer are Copyright (C) 2009
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *  Silvia Pfeiffer <silvia@siliva-pfeiffer.de>
 * 
 * Adapted by:
 *	Michael Dale <mdale@wikimedia.org> 
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */

// Stop JSLint whinging about globals //
/*global jQuery: true, window: true, ITEXT_ERR: true, ItextCollection: true, Itext: true, LoadFile: true, categoryName: true, languageName: true, parseInt: true, parseSrt: true, parseLrc: true */

// Function to convert language code to language name
// should be available inside the browser
function languageName(abbrev) {
    // see http://www.iana.org/assignments/language-subtag-registry
    // no sign languages included right now
    var langHash = {
        "aa": "Afar",
        "ab": "Abkhazian",
        "ae": "Avestan",
        "af": "Africaans",
        "ak": "Akan",
        "am": "Amharic",
        "an": "Aragonese",
        "anp": "Angika",
        "ar": "Arabic",
        "ar-ae": "Arabic (U.A.E.)",
        "ar-bh": "Arabic (Bahrain)",
        "ar-dz": "Arabic (Algeria)",
        "ar-eg": "Arabic (Egypt)",
        "ar-iq": "Arabic (Iraq)",
        "ar-jo": "Arabic (Jordan)",
        "ar-kw": "Arabic (Kuwait)",
        "ar-lb": "Arabic (Lebanon)",
        "ar-ly": "Arabic (Libya)",
        "ar-ma": "Arabic (Morocco)",
        "ar-om": "Arabic (Oman)",
        "ar-qa": "Arabic (Qatar)",
        "ar-sa": "Arabic (Saudi Arabia)",
        "ar-sy": "Arabic (Syria)",
        "ar-tn": "Arabic (Tunisia)",
        "ar-ye": "Arabic (Yemen)",
        "as": "Assamese",
        "ast": "Asturian",
        "av": "Avaric",
        "ay": "Aymara",
        "az": "Azerbaijani",
        "ba": "Bashkir",
        "be": "Belarusian",
        "bg": "Bulgarian",
        "bg-bg": "Bulgarian (Bulgaria)",
        "bh": "Bihari",
        "bi": "Bislama",
        "bm": "Bambara",
        "bn": "Bengali",
        "bo": "Tibetan",
        "br": "Breton",
        "bs": "Bosnian",
        "ca": "Catalan",
        "ca-es": "Catalan (Catalan)",
        "ce": "Chechen",
        "ch": "Chamorro",
        "co": "Corsican",
        "cr": "Cree",
        "cs": "Czech",
        "cs-cz": "Czech (Czech Republic)",
        "cu": "Church Slavic",
        "cv": "Cuvash",
        "cy": "Welsh",
        "da": "Danish",
        "da-dk": "Danish (Denmark)",
        "de": "German",
        "de-at": "German (Austria)",
        "de-ch": "German (Swiss)",
        "de-de": "German (Germany)",
        "de-li": "Deutsch (Lichtenstein)",
        "de-lu": "Deutsch (Luxemburg)",
        "dv": "Divehi",
        "dz": "Dzongkha",
        "ee": "Ewe",
        "el": "Greek",
        "en": "English",
        "en-au": "English (Australia)",
        "en-bz": "English (Belize)",
        "en-ca": "English (Canada)",
        "en-gb": "English (Great Britan)",
        "en-ie": "English (Ireland)",
        "en-jm": "English (Jamaica)",
        "en-nz": "English (New Zealand)",
        "en-ph": "English (Philippines)",
        "en-uk": "English (Great Britan)",
        "en-us": "English (United States)",
        "en-tt": "English (Trinidad)",
        "en-za": "English (South Africa)",
        "en-zw": "English (Zimbabwe)",
        "eo": "Ensperanto",
        "es": "Spanish",
        "es-ar": "Spanish (Argentina)",
        "es-bo": "Spanish (Bolivia)",
        "es-cl": "Spanish (Chile)",
        "es-co": "Spanish (Colombia)",
        "es-cr": "Spanish (Costa Rica)",
        "es-do": "Spanish (Dominican Republic)",
        "es-ec": "Spanish (Ecuador)",
        "es-es": "Spanish (Spain)",
        "es-gt": "Spanish (Guatemala)",
        "es-hn": "Spanish (Honduras)",
        "es-sv": "Spanish (El Salvador)",
        "es-mx": "Spanish (Mexico)",
        "es-nt": "Spanish (Nicaragua)",
        "es-pa": "Spanish (Panama)",
        "es-pe": "Spanish (Peru)",
        "es-pr": "Spanish (Puerto Rico)",
        "es-py": "Spanish (Paraguay)",
        "es-uy": "Spanish (Uruguay)",
        "es-ve": "Spanish (Venezuela)",
        "et": "Estonian",
        "eu": "Basque",
        "fa": "Persian",
        "ff": "Fulah",
        "fi": "Finnish",
        "fj": "Fijian",
        "fo": "Faroese",
        "fr": "French",
        "fr-be": "French (Belgium)",
        "fr-ca": "French (Canada)",
        "fr-ch": "French (Swiss)",
        "fr-fr": "French (France)",
        "fr-lu": "French (Luxemburg)",
        "fr-mc": "French (Mexico)",
        "frr": "Frisian",
        "fy": "Western Frisian",
        "ga": "Irish",
        "gd": "Gaelic",
        "gl": "Galician",
        "gn": "Guarani",
        "gu": "Gujarati",
        "gv": "Manx",
        "ha": "Hausa",
        "he": "Hebrew",
        "hi": "Hindi",
        "ho": "Hiri Motu",
        "hr": "Croatian",
        "hsb": "High Sorbian",
        "ht": "Haitian",
        "hu": "Hungarian",
        "hy": "Armenian",
        "hz": "Herero",
        "ia": "Interlingua",
        "id": "Indonesian",
        "ie": "Interlingue",
        "ig": "Igbo",
        "ii": "Sichuan Yi",
        "ik": "Inupiaq",
        "in": "Indonesian",
        "io": "Ido",
        "is": "Icelandic",
        "it": "Italian",
        "it-ch": "Italian (Swiss)",
        "iu": "Inuktitut",
        "iw": "Hebrew",
        "ja": "Japanese",
        "ji": "Yiddish",
        "jv": "Javanese",
        "ka": "Georian",
        "kg": "Kongo",
        "ki": "Kikuyu",
        "kj": "Kuanyama",
        "kk": "Kasakh",
        "kl": "Kalaallisut",
        "km": "Central Khmer",
        "kn": "Kannada",
        "ko": "Korean",
        "ko-kp": "Korean (North Korea)",
        "ko-kr": "Korean (South Korea)",
        "kr": "Kanuri",
        "ks": "Kashmiri",
        "ku": "Kurdish",
        "kv": "Komi",
        "kw": "Cornish",
        "ky": "Kyrgyz",
        "la": "Latin",
        "lb": "Luxembourgish",
        "lg": "Ganda",
        "li": "Limburgan",
        "ln": "Lingala",
        "lo": "Lao",
        "lt": "Lithuanian",
        "lu": "Luba-Katanga",
        "lv": "Latvian",
        "mg": "Malagasy",
        "mh": "Marshallese",
        "mi": "Maori",
        "mk": "Macedonian",
        "mk-mk": "Macedonian (F.J.R. Macedonia)",
        "ml": "Malayalam",
        "mn": "Mongolian",
        "mo": "Moldavian",
        "mr": "Marathi",
        "ms": "Malay",
        "mt": "Maltese",
        "my": "Burmese",
        "na": "Nauru",
        "nb": "Nowegian Bokm&#xE5;l",
        "nd": "North Ndebele",
        "ne": "Nepali",
        "ng": "Ndonga",
        "nl": "Dutch",
        "nl-be": "Dutch (Belgium)",
        "nn": "Nowegian Nynorsk",
        "no": "Nowegian",
        "nr": "South Ndebele",
        "nv": "Navajo",
        "ny": "Chichewa",
        "oc": "Occitan",
        "oj": "Ojibwa",
        "om": "Oromo",
        "or": "Oriya",
        "os": "Ossetian",
        "pa": "Panjabi",
        "pi": "Pali",
        "pl": "Polish",
        "ps": "Pushto",
        "pt": "Portuguese",
        "pt-br": "Portuguese (Brasil)",
        "qu": "Quechua",
        "rm": "Romansh",
        "rn": "Rundi",
        "ro": "Romanian",
        "ru": "Russian",
        "rw": "Kinyarwanda",
        "sa": "Sanskit",
        "sc": "Sardinian",
        "sd": "Sindhi",
        "se": "Northern Sami",
        "sg": "Sango",
        "sh": "Serbo-Croatian",
        "si": "Sinhala",
        "sk": "Slovak",
        "sl": "Slovenian",
        "sm": "Samoan",
        "sn": "Shona",
        "so": "Somali",
        "sq": "Albanian",
        "sr": "Serbian",
        "ss": "Swati",
        "st": "Southern Sotho",
        "su": "Sundanese",
        "sv": "Swedish",
        "sv-fi": "Swedisch (Finnland)",
        "sw": "Swahili",
        "ta": "Tamil",
        "te": "Telugu",
        "tg": "Tajik",
        "th": "Thai",
        "ti": "Tigrinya",
        "tk": "Turkmen",
        "tl": "Tagalog",
        "tn": "Tswana",
        "to": "Tonga",
        "tr": "Turkish",
        "ts": "Tsonga",
        "tt": "Tatar",
        "tw": "Twi",
        "ty": "Tahitian",
        "ug": "Uighur",
        "uk": "Ukrainian",
        "ur": "Urdu",
        "uz": "Uzbek",
        "ve": "Venda",
        "vi": "Vietnamese",
        "vo": "Volap&#xFC;k",
        "wa": "Walloon",
        "wo": "Wolof",
        "xh": "Xhosa",
        "yi": "Yiddish",
        "yo": "Yoruba",
        "za": "Zhuang",
        "zh": "Chinese",
        "zh-chs": "Chinese (Simplified)",
        "zh-cht": "Chinese (Traditional)",
        "zh-cn": "Chinese (People's Republic of China)",
        "zh-guoyu": "Mandarin",
        "zh-hk": "Chinese (Hong Kong S.A.R.)",
        "zh-min-nan": "Min-Nan",
        "zh-mp": "Chinese (Macau S.A.R.)",
        "zh-sg": "Chinese (Singapore)",
        "zh-tw": "Chinese (Taiwan)",
        "zh-xiang": "Xiang",
        "zu": "Zulu"
    };
    if (langHash[abbrev]) {
        return langHash[abbrev];
    } else {
        return null;
    }
}

// Function to convert category code to category name
function categoryName(abbrev) {
    // see http://www.iana.org/assignments/language-subtag-registry
    var catHash = {
        "CC":  "Captions",
        "SUB": "Subtitles",
        "TAD": "Audio Description",
        "KTV": "Karaoke",
        "TIK": "Ticker Text",
        "AR":  "Active Regions",
        "NB":  "Annotation",
        "META": "Timed Metadata",
        "TRX": "Transcript",
        "LRC": "Lyrics",
        "LIN": "Linguistic Markup",
        "CUE": "Cue Points"
    };
    if (catHash[abbrev]) {
        return catHash[abbrev];
    } else {
        return null;
    }
}


// This is where the implementation of iText starts
// list of potential errors created with iText parsing
var ITEXT_ERR = {
    ABORTED: 1, // fetching aborted
    NETWORK: 2, // network error
    PARSE: 3,   // parsing error of itext resource
    SRC_NOT_SUPPORTED: 4, // unsuitable itext resource
    LANG: 5    // language mismatch
};


// class to load a file, call the right parsing function,
// and keep the parsed text segments
var LoadFile = function (url, charset, type) {
    this.load(url, charset, type);
};
LoadFile.prototype = {
    url: null,
    textElements: [],
    error: 0,
    load: function (url, charset, type) {
        this.url = url;
        var handler = null;
        var content = [];
        var error = 0;
        // choose parsing function
        if (type === "text/srt") {
            handler = parseSrt;
        } else if (type === "text/lrc") {
            handler = parseLrc;
        } else {
            // no handler for given file type
            this.error = ITEXT_ERR.SRC_NOT_SUPPORTED;
        }
        // set the character encoding before the ajax request
        jQuery.ajaxSetup({
            'beforeSend' : function (xhr) {
                xhr.overrideMimeType("text/text; charset=" + charset);
            }
        });
        jQuery.ajax({
            type: "GET",
            url: url,
            data: {},
            success: function (data, textStatus) {
                content = handler(data);
            },
            error: function () {
                error = ITEXT_ERR.NETWORK;
            },
            dataType: 'text',
            async: false,
            cache: false // REMOVE AFTER TESTING: FIXME
        });
        if (!error && !content) {
            this.error = ITEXT_ERR.PARSE;
        } else if (error) {
            this.error = error;
        }
        this.textElements = content;
    }
};


// class to hold an itext track
var Itext = function (track) {
    this.init(track);
};
Itext.prototype = {    
    src: null,
    lang: null,
    langName: null,
    type: "text/srt",
    charset: null,
    delay: 0,
    stretch: 100,
    fetched: false,
    error: 0,
    allText: [],
    init: function (itext) {
        this.src = jQuery(itext).attr("src");
        this.lang = jQuery(itext).attr("lang");
        this.langName = languageName(this.lang);
        this.type = (jQuery(itext).attr("type") || "text/srt");
        this.charset = (jQuery(itext).attr("charset") || "UTF-8");
        this.delay = (jQuery(itext). attr("delay") || 0);
        this.stretch = (jQuery(itext).attr("stretch") || 100);
    },
    fetch: function () {
        if (this.type === "text/srt" || this.type === "text/lrc") {
            var file = new LoadFile(this.src, this.charset, this.type);
            this.error = file.error;
            this.allText = file.textElements;

            // adjust for delay and stretch factor
            for (var i = 0; i < this.allText.length; i++) {
                this.allText[i].start = (this.allText[i].start * (this.stretch/100.0)) + this.delay;
                this.allText[i].end = (this.allText[i].end * (this.stretch/100.0)) + this.delay;
            }

            this.fetched = true;
        } else {
            this.error = ITEXT_ERR.SRC_NOT_SUPPORTED;
        }
    },
    currentText: function ( currentTime ) {
        var lines = [];
        for (var i = 0; i < this.allText.length; i++) {
            if (this.allText[i].end) {
                if (currentTime >= this.allText[i].start && currentTime < this.allText[i].end) {
                    lines.push('<div class="text">' + this.allText[i].content + '</div>');
                }
            } else {
                if (currentTime >= this.allText[i].start) {
                    lines.push('<div class="text">' + this.allText[i].content + '</div>');
                }
            }
        }
        // produce output
        var content;
        if (lines.length === 0) {
            content = null;
        } else {
            content = lines.join("<br>\n");
        }
        return content;
    }
};


// Class to hold an itextlist
var ItextList = function (itextlist) {
    this.init(itextlist);
};
ItextList.prototype = {
    itextlist: null,
    category: null,
    active: "auto",
    name: null,
    itexts: [],
    onenter: null,
    onleave: null,
    primary_lang: null,
    secondary_lang: null,
    init: function (itextlist) {
        this.itextlist = itextlist;
        this.category = jQuery(itextlist).attr("category");
        this.active = (jQuery(itextlist).attr("active") || "none");
        this.name = jQuery(itextlist).attr("name");
        this.onenter = jQuery(itextlist).attr("onenter");
        this.onleave = jQuery(itextlist).attr("onleave");

        // parse the itext elements
        this.load();

        // fetch the appropriate track
        var default_lang;
        if (this.primary_lang) {
          default_lang = this.primary_lang;
        } else if (this.secondary_lang) {
          default_lang = this.secondary_lang;
        }
        if (this.active != "none") {
          if (this.active == "auto") {
            this.active = default_lang;
          } else {
            if (!this.itexts[this.active]) {
              this.active = "none";
            }
          }
          this.itexts[this.active].fetch();
        }
    },
    load: function() {
        var tracks_tmp = this.itextlist.find('itext');
        var itexts_tmp = {};
        var primary_lang_tmp = null;
        var secondary_lang_tmp = null;
    
        tracks_tmp.each(function (i) {
            // create the text track and add to the itexts array
            var track = new Itext(jQuery(this));
            itexts_tmp[track.lang] = {};
            itexts_tmp[track.lang] = track;

            // check for appropriate language in this track for later fetching
            if (track.lang === window.navigator.language) {
                primary_lang_tmp = track.lang; // complete lang match
            } else if (track.lang === window.navigator.language.substr(0, 2)) {
                secondary_lang_tmp = track.lang; // only main lang match
            }
        });
        this.itexts = itexts_tmp;
        this.secondary_lang = secondary_lang_tmp;
        this.primary_lang = primary_lang_tmp;
    },
    enable: function(lang) {
      if (lang === 'none') {
        this.active = "none";
      } else if (this.itexts[lang]) {
        this.itexts[lang].fetch();
        this.active = lang;
      }
    }
};



// class to parse all itextlists under a media element
// and create a menu
// would need to be implemented inside Browser
var ItextCollection = function (video, div_id) {
    this.init(video, div_id);
};
ItextCollection.prototype = {
    video: null,
    div_id: null,
    itextlist: {},
    
    init: function (vid, div) {
        this.video = vid;
        this.div_id = div;
        this.load();
        // set up display divs for each category
        for (var i in this.itextlist) {
            jQuery("#" + this.div_id).append("<div class='itext_" + i + "'></div>");
        }
    },
    load: function () {
        // go through each itextlist, parse it and add it to itextlist{}
        var tracks_tmp = this.video.find('itextlist');
        var itextlist_tmp={};
       
        tracks_tmp.each(function (i) {
            // create the itextlist track
            var ilist = new ItextList(jQuery(this));
            itextlist_tmp[ilist.category] = {};
            itextlist_tmp[ilist.category] = ilist;
        });
        this.itextlist = itextlist_tmp;
    },
    itextMenu: function (baseEl, elstring) {
        var appendText = '<div class="itextMenu" role="presentation">\n';
        appendText += '<ul class="catMenu" role="presentation">\n';
        for (var i in this.itextlist) {
          var subm = this.itextlist[i];
          var submenu = subm.name || categoryName(i);
          appendText += '<li role="menuitem" aria-haspopup="true" tabindex="0"> &lt; &nbsp;' + submenu + '\n';
          appendText += '<ul class="langMenu" role="menu" >\n';
          // add a default of "none" element to menu
          appendText += '<li role="menuitemradio" tabindex="0"';
          if (subm.active === 'none') {
            appendText += ' aria-checked="true"';
          } else {
            appendText += ' aria-checked="false"';
          }
          appendText += '><a href="#" onclick="'+elstring+'.itexts.itextlist[\'' + i + '\'].enable(\'none\');jQuery(\'.catMenu\').css(\'visibility\', \'hidden\');return false;">None</a></li>\n';
          for (var j in subm.itexts) {
            var elt = subm.itexts[j];
            appendText += '<li role="menuitemradio" tabindex="0"';
            if (subm.active === j) {
              appendText += ' aria-checked="true"';
            } else {
              appendText += ' aria-checked="false"';
            }
            appendText += '><a href="#" onclick="'+elstring+'.itexts.itextlist[\'' + i + '\'].enable(\'' + j + '\');jQuery(\'.catMenu\').css(\'visibility\', \'hidden\');return false;">' + elt.langName + '</a></li>\n';
          }
          appendText += '</ul>\n</li>\n';
        }
        appendText += '</ul></div>\n';
        jQuery(baseEl).append(appendText);
        var videoHeight = jQuery(this.video).css("height").substr(0, jQuery(this.video).css("height").length - 2);
        jQuery(".langMenu").css("height", "240px");
        jQuery(".catMenu").css("visibility", "hidden");
    },
    show: function (currentTime) {
      // add to correct content container
      var mc_width = jQuery('.mc').css("width").substr(0, jQuery('.mc').css("width").length - 2);

      // get content per category, if active, and display it
      var content = null;
      for (var i in this.itextlist) {
        var li = this.itextlist[i];

        // get content for active tracks only
        if (li.active != "none") {
          content = li.itexts[li.active].currentText(currentTime);

          // update content & styling of itext div if necessary
          if (content) {
            if (jQuery("#" + this.div_id + " > .itext_" + i).html() !== content) {

              // update content and make it visible
              jQuery("#" + this.div_id + " > .itext_" + i).html(content);
              jQuery("#" + this.div_id + " > .itext_" + i).css("visibility", "visible");

              // update styling dependent on text length
              if (i === "CUE") {
                jQuery("#" + this.div_id + " > .itext_" + i + " > .text").prepend("Chapter: ");
              }
              if (i === "TAD")    {
                jQuery("#" + this.div_id + " > .itext_TAD").css("max-width", (mc_width) + "px");
              }
              if (i === "LRC") {
                jQuery("#" + this.div_id + " > .itext_LRC").css("max-width", mc_width + "px");
                // somehow the setting of "left" encourages the correct width to be calculated
                // if I don't do that, the width calculation in text_half_length is too short on some elements
                jQuery("#" + this.div_id + " > .itext_LRC").css("left",5);
                var text_half_length = jQuery("#" + this.div_id + " > .itext_LRC > .text").css("width").substr(0, jQuery("#" + this.div_id + " > .itext_LRC > .text").css("width").length - 2) / 2;
                jQuery("#" + this.div_id + " > .itext_LRC").css("left", ((mc_width / 2) - text_half_length - 7) + "px");
              }
              if (i === "CC" ||
                  i === "SUB" ||
                  i === "KTV" ||
                  i === "TRX" ||
                  i === "LIN") {
                // anyone with a better idea for how to place the captions bottom center, please speak up
                jQuery("#" + this.div_id + " > .itext_" + i).css("max-width", mc_width + "px");
                // somehow the setting of "left" encourages the correct width to be calculated
                // if I don't do that, the width calculation in text_half_length is too short on some elements
                jQuery("#" + this.div_id + " > .itext_" + i).css("left",5);
                var text_half_length = jQuery("#" + this.div_id + " > .itext_" + i + " > .text").css("width").substr(0, jQuery("#" + this.div_id + " > .itext_" + i + " > .text").css("width").length - 2) / 2;
                jQuery("#" + this.div_id + " > .itext_" + i).css("left", ((mc_width / 2) - text_half_length - 7) + "px");
              }
            }
          } else {
            // remove content
            jQuery("#" + this.div_id + " > .itext_" + i).css("visibility", "hidden");
          }
        } else {
          // remove content
          jQuery("#" + this.div_id + " > .itext_" + i).css("visibility", "hidden");          
        }
      }
    }
};

