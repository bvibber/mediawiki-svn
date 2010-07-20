<?php

class CodeReviewTest extends PHPUnit_Framework_TestCase {
	private function createRepo() {
		$row = new stdClass();
		$row->repo_id = 1;
		$row->repo_name = 'Test';
		$row->repo_path = 'somewhere';
		$row->repo_viewvc = 'http://example.com/view/';
		$row->repo_bugzilla = 'http://example.com/bugzilla/$1';

		return CodeRepository::newFromRow( $row );
	}

	public function testCommentWikiFormatting() {
		$repo = $this->createRepo();
		$formatter = new CodeCommentLinkerWiki( $repo );
		
		$this->assertEquals( '[http://foo http://foo]', $formatter->link( 'http://foo' ) );
		$this->assertEquals( '[http://example.com/bugzilla/123 bug 123]', $formatter->link( 'bug 123' ) );
		$this->assertEquals( '[[Special:Code/Test/456|r456]]', $formatter->link( 'r456' ) );
		// fails, bug 23203 and so on
		//$this->assertEquals( '[http://example.org foo http://example.org foo]', $formatter->link( '[http://example.org foo http://example.org foo]' ) );
		//$this->assertEquals( '[http://foo.bar r123]', $formatter->link( '[http://foo.bar r123]' ) );
	}
}