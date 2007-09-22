This extension allows for a template to be transcluded as a new page, using Special:TemplateLink.
The template parameter separator "|" has to be replaced with "::" as to not confuse the parser.

Example:
[[Special:TemplateLink/test::param1=value1::param2=value2]]

links to a special page that will display "Test" (variation configurable in i18n) as title and

{{test|param1=value1|param2=value2}}

as content.
