<?php

declare( strict_types = 1 );
namespace WaughJ\WPUploadPicture
{
	use WaughJ\HTMLPicture\HTMLPicture;
	use WaughJ\HTMLImage\HTMLImage;
	use WaughJ\HTMLPicture\HTMLPictureSource;
	use function WaughJ\WPGetImageSizes\WPGetImageSizes;

	class WPUploadPicture extends HTMLPicture
	{
		public function __construct( int $id, array $attributes = [] )
		{
			$sources = self::getSources( $id );
			$fallback_image = new HTMLImage( $sources[ 0 ]->getSrcSet() );
			parent::__construct( $fallback_image, $sources, $attributes );
		}

		private static function getSources( int $id ) : array
		{
			$sources = [];
			$image_sizes = WPGetImageSizes();
			foreach ( $image_sizes as $size )
			{
				$image_size_obj = wp_get_attachment_image_src( $id, $size->getSlug() );
				$url = $image_size_obj[ 0 ];
				$max_width = $image_size_obj[ 1 ];
				$media = "(max-width:{$max_width}px)";
				$sources[] = new HTMLPictureSource( $url, $media );
			}
			return $sources;
		}
	}
}
