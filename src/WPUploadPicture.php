<?php

declare( strict_types = 1 );
namespace WaughJ\WPUploadPicture;

use WaughJ\FileLoader\MissingFileException;
use WaughJ\HTMLPicture\HTMLPicture;
use WaughJ\HTMLImage\HTMLImage;
use WaughJ\HTMLPicture\HTMLPictureSource;
use WaughJ\WPUploadImage\WPMissingMediaException;
use WaughJ\WPUploadImage\WPUploadImage;
use function WaughJ\WPGetImageSizes\WPGetImageSizes;
use function WaughJ\TestHashItem\TestHashItemArray;
use function WaughJ\TestHashItem\TestHashItemExists;

class WPUploadPicture extends HTMLPicture
{
	public function __construct( int $id, array $attributes = [] )
	{
		$src_attributes = TestHashItemArray( $attributes, 'source-attributes', [] );
		$show_version = $attributes[ 'show-version' ] ?? true;
		unset( $attributes[ 'show-version' ] );

		$missing_files = [];
		try
		{
			$sources = self::generateSources( $id, $src_attributes, $show_version );
		}
		catch ( MissingFileException $e )
		{
			$missing_files = $e->getFilename();
			$sources = $e->getFallbackContent();
		}

		if ( empty( $sources ) )
		{
			throw new WPMissingMediaException( $id );
		}

		$fallback_image = new HTMLImage( $sources[ 0 ]->getSrcSet(), null, TestHashItemArray( $attributes, 'img-attributes', [] ) );
		$picture_attributes = TestHashItemArray( $attributes, 'picture-attributes', [] );
		unset( $attributes[ 'img-attributes' ], $attributes[ 'source-attributes' ], $attributes[ 'picture-attributes' ] );
		$attributes = array_merge( $picture_attributes, $attributes );

		if ( !empty( $missing_files ) )
		{
			throw new MissingFileException( $missing_files, new HTMLPicture( $fallback_image, $sources, $attributes ) );
		}

		parent::__construct( $fallback_image, $sources, $attributes );
	}

	private static function generateSources( int $id, array $attributes, bool $show_version ) : array
	{
		$missing_files = [];
		$sources = [];
		$image_sizes = WPGetImageSizes();
		$number_of_image_sizes = count( $image_sizes );
		$min_width = null;
		for ( $i = 0; $i < $number_of_image_sizes; $i++ )
		{
			$size = $image_sizes[ $i ];
			$image_size_obj = wp_get_attachment_image_src( $id, $size->getSlug() );
			if ( $image_size_obj === null )
			{
				continue;
			}
			try
			{
				$url = WPUploadImage::getFormattedURL( $image_size_obj, $show_version );
			}
			catch ( MissingFileException $e )
			{
				$missing_files[] = $e->getFilename();
				$url = $e->getFallbackContent();
			}
			$max_width = $image_size_obj[ 1 ];
			$media = ( $i === $number_of_image_sizes - 1 && $i > 0 )
				? "(min-width:{$min_width}px)"
				: "(max-width:{$max_width}px)";
			$sources[] = new HTMLPictureSource( $url, $media, $attributes );
			$min_width = $max_width + 1;
		}

		if ( !empty( $missing_files ) )
		{
			throw new MissingFileException( $missing_files, $sources );
		}

		return $sources;
	}
}
