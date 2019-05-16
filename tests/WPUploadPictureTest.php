<?php

use PHPUnit\Framework\TestCase;
use WaughJ\FileLoader\MissingFileException;
use WaughJ\WPUploadPicture\WPUploadPicture;

require_once( 'MockWordPress.php' );

class WPUploadPictureTest extends TestCase
{
	public function testBasicPicture()
	{
		$picture = new WPUploadPicture( 2, [ 'img-attributes' => [ 'class' => 'something' ], 'source-attributes' => [ 'class' => 'something-else' ], 'picture-attributes' => [ 'class' => 'major' ] ] );
		$this->assertStringContainsString( ' srcset="https://www.example.com/wp-content/uploads/2018/12/photo-300x300.jpg?m=', $picture->getHTML() );
		$this->assertStringContainsString( ' media="(max-width:300px)"', $picture->getHTML() );
		$this->assertStringContainsString( ' media="(min-width:769px)"', $picture->getHTML() );
		$this->assertStringContainsString( ' class="something"', $picture->getFallbackImage()->getHTML() );
		$this->assertStringContainsString( ' class="something-else"', $picture->getHTML() );
		$this->assertStringContainsString( ' class="major"', $picture->getHTML() );
		$this->assertStringNotContainsString( 'img-attributes="', $picture->getHTML() );
		$this->assertStringNotContainsString( 'source-attributes="', $picture->getHTML() );
		$this->assertStringNotContainsString( 'picture-attributes="', $picture->getHTML() );
		$this->assertStringNotContainsString( ' show-version="', $picture->getHTML() );
	}

	public function testNonexistentPicture()
	{
		$picture = new WPUploadPicture( 3632, [ 'img-attributes' => [ 'class' => 'something' ], 'source-attributes' => [ 'class' => 'something-else' ], 'picture-attributes' => [ 'class' => 'major' ] ] );
		$this->assertEquals( '<picture><img src="" alt="" /></picture>', $picture->getHTML() );
	}

	public function testBasicPictureWithoutVersioning()
	{
		$picture = new WPUploadPicture( 2, [ 'img-attributes' => [ 'class' => 'something' ], 'source-attributes' => [ 'class' => 'something-else' ], 'picture-attributes' => [ 'class' => 'major' ], 'show-version' => false ] );
		$this->assertStringContainsString( ' srcset="https://www.example.com/wp-content/uploads/2018/12/photo-300x300.jpg"', $picture->getHTML() );
		$this->assertStringNotContainsString( 'm?=', $picture->getHTML() );
		$this->assertStringNotContainsString( ' show-version="', $picture->getHTML() );
	}

	public function testBasicPictureWithVersioning()
	{
		$picture = new WPUploadPicture( 2, [ 'img-attributes' => [ 'class' => 'something' ], 'source-attributes' => [ 'class' => 'something-else' ], 'picture-attributes' => [ 'class' => 'major' ] ] );
		$this->assertStringContainsString( ' srcset="https://www.example.com/wp-content/uploads/2018/12/photo-300x300.jpg?m=', $picture->getHTML() );
		$this->assertStringNotContainsString( ' show-version="', $picture->getHTML() );
	}

	public function testBasicPictureWithVersioningAndMissingFile()
	{
		try
		{
			$picture = new WPUploadPicture( 3, [ 'img-attributes' => [ 'class' => 'something' ], 'source-attributes' => [ 'class' => 'something-else' ], 'picture-attributes' => [ 'class' => 'major' ] ] );
		}
		catch ( MissingFileException $e )
		{
			$picture = $e->getFallbackContent();
		}
		$this->assertStringContainsString( ' srcset="https://www.example.com/wp-content/uploads/2018/12/jack-300x300.jpg"', $picture->getHTML() );
	}
}
