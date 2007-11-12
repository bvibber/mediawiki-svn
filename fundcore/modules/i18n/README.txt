README.txt
==========

********************************************************************
This is i18n package, development version , and works with Drupal 5.x
********************************************************************
WARNING: DO READ THE INSTALL FILE
********************************************************************
Updated documentation will be kept on-line at http://drupal.org/node/67817
********************************************************************

This is not a single module anymore but a collection of modules providing complementary features
  i18n --> basic module which will add language to nodes, vocabularies and terms
  translation --> module to add translation relationships
  
********************************************************************
These modules provide support for multilingual Drupal sites:
    * Multilingual content, some basic translation interface, and links between translated versions
    * Translation of the user interface for registered and anonymous users (with locale)
    * Detection of the brower language
    * Keeps the language settings accross consecutive requests using URL rewriting.
    * Provides a block for language switching -i18n.module- and one for translations -translation.module-
Multilingual content:
=====================
Multilingual content means providing content translated to different languages or language specific content, which is not the same as interface translation. Interface translation is done through Drupal's localization system. 
This module supports:
  - Multilingual nodes
  - Multilingual taxonomy vocabularies and terms
  - Translations for nodes and terms

When you navigate the site using multiple languages, the pages will just show terms and nodes for the chosen language plus the ones that haven't a definde language. 
When editing a node, you must click on 'Preview' after changing language for the right vocabularies and terms to be shown.

The multi language support is expected to work for all node types, and node listings

Taxonomy translation:
====================
You can create vocabularies and terms with or without language. 
- If you set language for a vocabulary/term, that term will just show up for pages in that language
- If you set language for a vocabulary, all the terms in that vocabulary will be assigned that language.
- When editing nodes, if you change the language for a node, you have to click on 'Preview' to have the right vocabularies/terms for that language. Otherwise, the language/taxonomy data for that node could be inconsistent.
  
About URL aliasing with language codes -requires path module
============================================================
Incoming URL's are now translated following these steps:
1. First, a translation is searched for path with language code: 'en/mypage'
2. If not found, language code is removed, and path translation is searched again: 'mypage'

Thus, you can define aliases with or without language codes in them.

This language code will be taken from browser if enabled 'Browser language detection, or will be the default otherwise.

To have aliases for a translated node/page, you have to define each of them. I.e.:
  en/mycustompath -> node/34 (which is suppossed to be the english version)
  es/mycustompath -> node/35 (which should be the spanish version)

For outgoing URL's, the language code will be added automatically.

About language dependent variables:
==================================
Some site-wide variables, like 'site_name', 'site_slogan', user e-mail contents... have language dependent content.
Since I don't like the solution of runing them through the localization system, because this means when you change the 'master' text, you have to re-translate it for every language, I've added this new feature which makes possible to have a list of variables -defined in the config file- which will be kept separated for each language.
This part is an add-on, and you can use it or not.

About language dependent tables 
===============================
Language dependent tables are not needed anymore for multilingual content.
This is kept for backwards compatibility, experimentation and may be some use in the future.
* This can be used to have per-language data for modules not language-aware, like language statistics... you can experiment...

Known problems, compatibility issues
====================================
These modules should be compatible with all Drupal core modules.

Sample sites, using this module - e-mail me to be listed here
==========================================================
  http://www.reyero.net
  http://www.para.ro
  http://www.ctac.ca
  http://grasshopperarts.com
  http://funkycode.com
  http://www.newoceans.nl

Additional Support
=================
For support, please create a support request for this module's project: 
  http://drupal.org/project/i18n

If you need professional support, contact me by e-mail: freelance at reyero dot net

====================================================================
Jose A. Reyero, drupal at reyero dot net, http://www.reyero.net

Feedback is welcomed.