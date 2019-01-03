<?php

declare( strict_types = 1 );
namespace WaughJ\WPUploadPicture
{
	use WaughJ\HTMLPicture\HTMLPicture;
	use WaughJ\HTMLImage\HTMLImage;
	use WaughJ\HTMLPicture\HTMLPictureSource;
	use function WaughJ\WPGetImageSizes\WPGetImageSizes;
	use function WaughJ\TestHashItem\TestHashItemArray;

	class WPUploadPicture extends HTMLPicture
	{
		public function __construct( int $id, array $attributes = [] )
		{
			$sources = self::getSources( $id, TestHashItemArray( $attributes, 'source-attributes', [] ) );
			$fallback_image = new HTMLImage( $sources[ 0 ]->getSrcSet(), null, TestHashItemArray( $attributes, 'img-attributes', [] ) );
			$picture_attributes = TestHashItemArray( $attributes, 'picture-attributes', [] );
			unset( $attributes[ 'img-attributes' ], $attributes[ 'source-attributes' ], $attributes[ 'picture-attributes' ] );
			$attributes = array_merge( $picture_attributes, $attributes );
			parent::__construct( $fallback_image, $sources, $attributes );
		}

		private static function getSources( int $id, array $attributes ) : array
		{
			$sources = [];
			$image_sizes = WPGetImageSizes();
			$number_of_image_sizes = count( $image_sizes );
			$min_width = null;
			for ( $i = 0; $i < $number_of_image_sizes; $i++ )
			{
				$size = $image_sizes[ $i ];
				$image_size_obj = wp_get_attachment_image_src( $id, $size->getSlug() );
				$url = $image_size_obj[ 0 ];
				$max_width = $image_size_obj[ 1 ];
				$media = ( $i === $number_of_image_sizes - 1 && $i > 0 )
					? "(min-width:{$min_width}px)"
					: "(max-width:{$max_width}px)";
				$sources[] = new HTMLPictureSource( $url, $media, $attributes );
				$min_width = $max_width + 1;
			}
			return $sources;
		}
	}
}
