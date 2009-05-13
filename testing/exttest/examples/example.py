#!/usr/bin/python


from exttest import Test


tests=[
	Test(	
		name="{{SERVER}} directive",
		code="{{SERVER}} yet another test",
		expect='<a href="http://83.149.110.226" class="external free" title="http://83.149.110.226" rel="nofollow">http://83.149.110.226</a> yet another test'	
	),
	Test(
		name="plain text",
		code="test",
		expect="test"
	),
	Test(
		name="normal wikilink",
		code="[[hello]]",
		expect='<a href="/index.php?title=Hello&amp;action=edit" class="new" title="Hello">hello</a>'
	)
]

