<?php

use PHPUnit\Framework\TestCase;
use WaughJ\WPUploadPicture\WPUploadPicture;

require_once( 'MockWordPress.php' );

class WPUploadPictureTest extends TestCase
{
	public function testBasicPicture()
	{
		$picture = new WPUploadPicture( 2, [ 'img-attributes' => [ 'class' => 'something' ], 'source-attributes' => [ 'class' => 'something-else' ], 'picture-attributes' => [ 'class' => 'major' ] ] );
		$this->assertContains( ' srcset="https://www.example.com/wp-content/uploads/2018/12/photo-300x300.jpg"', $picture->getHTML() );
		$this->assertContains( ' media="(max-width:300px)"', $picture->getHTML() );
		$this->assertContains( ' media="(min-width:769px)"', $picture->getHTML() );
		$this->assertContains( ' class="something"', $picture->getFallbackImage()->getHTML() );
		$this->assertContains( ' class="something-else"', $picture->getHTML() );
		$this->assertContains( ' class="major"', $picture->getHTML() );
		$this->assertNotContains( 'img-attributes="', $picture->getHTML() );
		$this->assertNotContains( 'source-attributes="', $picture->getHTML() );
		$this->assertNotContains( 'picture-attributes="', $picture->getHTML() );
	}
}
