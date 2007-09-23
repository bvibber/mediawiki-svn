This extension allows for a template to be transcluded as a new page, using Special:TemplateLink.
To do that, wrap the template in a <templatelink> tag.

Example:
<templatelink>test|param1=value1|param2=value2</templatelink>

links to a special page that will display "Test" (variation configurable in i18n) as title and

{{test|param1=value1|param2=value2}}

as content.

You can also specify the title of the link in a new line:
<templatelink>
test|param1=value1|param2=value2
Click on this!
</templatelink>

Leading and ending newlines are ignored. First line is the template, last line is the link title, 
everything in between is currently ignored (future uses?).
