<?php
/**
 * Image Editor Class for Image Manipulation through GD
 *
 * @since 3.5.0
 * @package WordPress
 * @subpackage Image_Editor
 * @uses WP_Image_Editor Extends class
 */

function wp_imagecreatetruecolor($width, $height) {
	$img = imagecreatetruecolor($width, $height);
	if ( is_resource($img) && function_exists('imagealphablending') && function_exists('imagesavealpha') ) {
		imagealphablending($img, false);
		imagesavealpha($img, true);
	}
	return $img;
}

function wp_constrain_dimensions( $current_width, $current_height, $max_width=0, $max_height=0 ) {
	if ( !$max_width and !$max_height )
		return array( $current_width, $current_height );

	$width_ratio = $height_ratio = 1.0;
	$did_width = $did_height = false;

	if ( $max_width > 0 && $current_width > 0 && $current_width > $max_width ) {
		$width_ratio = $max_width / $current_width;
		$did_width = true;
	}

	if ( $max_height > 0 && $current_height > 0 && $current_height > $max_height ) {
		$height_ratio = $max_height / $current_height;
		$did_height = true;
	}

	// Calculate the larger/smaller ratios
	$smaller_ratio = min( $width_ratio, $height_ratio );
	$larger_ratio  = max( $width_ratio, $height_ratio );

	if ( intval( $current_width * $larger_ratio ) > $max_width || intval( $current_height * $larger_ratio ) > $max_height )
 		// The larger ratio is too big. It would result in an overflow.
		$ratio = $smaller_ratio;
	else
		// The larger ratio fits, and is likely to be a more "snug" fit.
		$ratio = $larger_ratio;

	$w = intval( $current_width  * $ratio );
	$h = intval( $current_height * $ratio );

	// Sometimes, due to rounding, we'll end up with a result like this: 465x700 in a 177x177 box is 117x176... a pixel short
	// We also have issues with recursive calls resulting in an ever-changing result. Constraining to the result of a constraint should yield the original result.
	// Thus we look for dimensions that are one pixel shy of the max value and bump them up
	if ( $did_width && $w == $max_width - 1 )
		$w = $max_width; // Round it up
	if ( $did_height && $h == $max_height - 1 )
		$h = $max_height; // Round it up

	return array( $w, $h );
}

function image_resize_dimensions($orig_w, $orig_h, $dest_w, $dest_h, $crop = false) {

	if ($orig_w <= 0 || $orig_h <= 0)
		return false;
	// at least one of dest_w or dest_h must be specific
	if ($dest_w <= 0 && $dest_h <= 0)
		return false;

	if ( $crop ) {
		// crop the largest possible portion of the original image that we can size to $dest_w x $dest_h
		$aspect_ratio = $orig_w / $orig_h;
		$new_w = min($dest_w, $orig_w);
		$new_h = min($dest_h, $orig_h);

		if ( !$new_w ) {
			$new_w = intval($new_h * $aspect_ratio);
		}

		if ( !$new_h ) {
			$new_h = intval($new_w / $aspect_ratio);
		}

		$size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

		$crop_w = round($new_w / $size_ratio);
		$crop_h = round($new_h / $size_ratio);

		$s_x = floor( ($orig_w - $crop_w) / 2 );
		$s_y = floor( ($orig_h - $crop_h) / 2 );
	} else {
		// don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
		$crop_w = $orig_w;
		$crop_h = $orig_h;

		$s_x = 0;
		$s_y = 0;

		list( $new_w, $new_h ) = wp_constrain_dimensions( $orig_w, $orig_h, $dest_w, $dest_h );
	}

	// if the resulting image would be the same size or larger we don't want to resize it
	if ( $new_w >= $orig_w && $new_h >= $orig_h )
		return false;

	// the return array matches the parameters to imagecopyresampled()
	// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
	return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );

}

class ImageEditor extends Image_Editor {

	protected $image = false; // GD Resource

	function __destruct() {
		if ( $this->image ) {
			// we don't need the original in memory anymore
			imagedestroy( $this->image );
		}
	}

	/**
	 * Checks to see if current environment supports GD.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @return boolean
	 */
	public static function test( $args = array() ) {
		if ( ! extension_loaded('gd') || ! function_exists('gd_info') )
			return false;

		// On some setups GD library does not provide imagerotate() - Ticket #11536
		if ( isset( $args['methods'] ) &&
			 in_array( 'rotate', $args['methods'] ) &&
			 ! function_exists('imagerotate') ){

				return false;
		}

		return true;
	}

	/**
	 * Checks to see if editor supports the mime-type specified.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param string $mime_type
	 * @return boolean
	 */
	public static function supports_mime_type( $mime_type ) {
		$image_types = imagetypes();
		switch( $mime_type ) {
			case 'image/jpeg':
				return ($image_types & IMG_JPG) != 0;
			case 'image/png':
				return ($image_types & IMG_PNG) != 0;
			case 'image/gif':
				return ($image_types & IMG_GIF) != 0;
		}

		return false;
	}

	/**
	 * Loads image from $this->file into new GD Resource.
	 *
	 * @since 3.5.0
	 * @access protected
	 *
	 * @return boolean|\WP_Error
	 */
	public function load() {
		if ( $this->image )
			return true;

		if ( ! is_file( $this->file ) && ! preg_match( '|^https?://|', $this->file ) )
			throw new customException('File doesn&#8217;t exist?', 'error_loading_image');

		// Set artificially high because GD uses uncompressed images in memory
		@ini_set( 'memory_limit', apply_filters( 'image_memory_limit', MAX_MEMORY_LIMIT ) );
		$this->image = @imagecreatefromstring( file_get_contents( $this->file ) );

		if ( ! is_resource( $this->image ) )
			throw new customException('File is not an image.', 'invalid_image');

		$size = @getimagesize( $this->file );
		if ( ! $size )
			throw new customException('Could not read image size.', 'invalid_image');

		$this->update_size( $size[0], $size[1] );
		$this->mime_type = $size['mime'];

		return true;
	}

	/**
	 * Sets or updates current image size.
	 *
	 * @since 3.5.0
	 * @access protected
	 *
	 * @param int $width
	 * @param int $height
	 */
	protected function update_size( $width = false, $height = false ) {
		if ( ! $width )
			$width = imagesx( $this->image );

		if ( ! $height )
			$height = imagesy( $this->image );

		return parent::update_size( $width, $height );
	}

	/**
	 * Resizes current image.
	 * Wraps _resize, since _resize returns a GD Resource.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param int $max_w
	 * @param int $max_h
	 * @param boolean $crop
	 * @return boolean|WP_Error
	 */
	public function resize( $max_w, $max_h, $crop = false ) {
		if ( ( $this->size['width'] == $max_w ) && ( $this->size['height'] == $max_h ) )
			return true;

		$resized = $this->_resize( $max_w, $max_h, $crop );

		if ( is_resource( $resized ) ) {
			imagedestroy( $this->image );
			$this->image = $resized;
			return true;

		} 
	}

	protected function _resize( $max_w, $max_h, $crop = false ) {
		$dims = image_resize_dimensions( $this->size['width'], $this->size['height'], $max_w, $max_h, $crop );
		if ( ! $dims ) {
			throw new customException('Could not calculate resized image dimensions.', 'error_getting_dimensions');
		}
		list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;

		$resized = wp_imagecreatetruecolor( $dst_w, $dst_h );
		imagecopyresampled( $resized, $this->image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );

		if ( is_resource( $resized ) ) {
			$this->update_size( $dst_w, $dst_h );
			return $resized;
		}

		throw new customException('Image resize failed.', 'image_resize_error');
	}

	/**
	 * Processes current image and saves to disk
	 * multiple sizes from single source.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param array $sizes { {'width'=>int, 'height'=>int, 'crop'=>bool}, ... }
	 * @return array
	 */
	public function multi_resize( $sizes ) {
		$metadata = array();
		$orig_size = $this->size;

		foreach ( $sizes as $size => $size_data ) {
			$image = $this->_resize( $size_data['width'], $size_data['height'], $size_data['crop'] );

			$resized = $this->_save( $image );

			imagedestroy( $image );

			if ( $resized ) {
				unset( $resized['path'] );
				$metadata[$size] = $resized;

			$this->size = $orig_size;
		}

		return $metadata;
	}

	/**
	 * Crops Image.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param string|int $src The source file or Attachment ID.
	 * @param int $src_x The start x position to crop from.
	 * @param int $src_y The start y position to crop from.
	 * @param int $src_w The width to crop.
	 * @param int $src_h The height to crop.
	 * @param int $dst_w Optional. The destination width.
	 * @param int $dst_h Optional. The destination height.
	 * @param boolean $src_abs Optional. If the source crop points are absolute.
	 * @return boolean|WP_Error
	 */
	public function crop( $src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false ) {
		// If destination width/height isn't specified, use same as
		// width/height from source.
		if ( ! $dst_w )
			$dst_w = $src_w;
		if ( ! $dst_h )
			$dst_h = $src_h;

		$dst = wp_imagecreatetruecolor( $dst_w, $dst_h );

		if ( $src_abs ) {
			$src_w -= $src_x;
			$src_h -= $src_y;
		}

		if ( function_exists( 'imageantialias' ) )
			imageantialias( $dst, true );

		imagecopyresampled( $dst, $this->image, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );

		if ( is_resource( $dst ) ) {
			imagedestroy( $this->image );
			$this->image = $dst;
			$this->update_size();
			return true;
		}

		return new WP_Error( 'image_crop_error', __('Image crop failed.'), $this->file );
	}

	/**
	 * Rotates current image counter-clockwise by $angle.
	 * Ported from image-edit.php
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param float $angle
	 * @return boolean|WP_Error
	 */
	public function rotate( $angle ) {
		if ( function_exists('imagerotate') ) {
			$rotated = imagerotate( $this->image, $angle, 0 );

			if ( is_resource( $rotated ) ) {
				imagedestroy( $this->image );
				$this->image = $rotated;
				$this->update_size();
				return true;
			}
		}
		return new WP_Error( 'image_rotate_error', __('Image rotate failed.'), $this->file );
	}

	/**
	 * Flips current image.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param boolean $horz Horizontal Flip
	 * @param boolean $vert Vertical Flip
	 * @returns boolean|WP_Error
	 */
	public function flip( $horz, $vert ) {
		$w = $this->size['width'];
		$h = $this->size['height'];
		$dst = wp_imagecreatetruecolor( $w, $h );

		if ( is_resource( $dst ) ) {
			$sx = $vert ? ($w - 1) : 0;
			$sy = $horz ? ($h - 1) : 0;
			$sw = $vert ? -$w : $w;
			$sh = $horz ? -$h : $h;

			if ( imagecopyresampled( $dst, $this->image, 0, 0, $sx, $sy, $w, $h, $sw, $sh ) ) {
				imagedestroy( $this->image );
				$this->image = $dst;
				return true;
			}
		}
		return new WP_Error( 'image_flip_error', __('Image flip failed.'), $this->file );
	}

	/**
	 * Saves current in-memory image to file.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param string $destfilename
	 * @param string $mime_type
	 * @return array|WP_Error {'path'=>string, 'file'=>string, 'width'=>int, 'height'=>int, 'mime-type'=>string}
	 */
	public function save( $filename = null, $mime_type = null ) {
		$saved = $this->_save( $this->image, $filename, $mime_type );

		if ( ! is_wp_error( $saved ) ) {
			$this->file = $saved['path'];
			$this->mime_type = $saved['mime-type'];
		}

		return $saved;
	}

	protected function _save( $image, $filename = null, $mime_type = null ) {
		list( $filename, $extension, $mime_type ) = $this->get_output_format( $filename, $mime_type );

		if ( ! $filename )
			$filename = $this->generate_filename( null, null, $extension );

		if ( 'image/gif' == $mime_type ) {
			if ( ! $this->make_image( $filename, 'imagegif', array( $image, $filename ) ) )
				return new WP_Error( 'image_save_error', __('Image Editor Save Failed') );
		}
		elseif ( 'image/png' == $mime_type ) {
			// convert from full colors to index colors, like original PNG.
			if ( function_exists('imageistruecolor') && ! imageistruecolor( $image ) )
				imagetruecolortopalette( $image, false, imagecolorstotal( $image ) );

			if ( ! $this->make_image( $filename, 'imagepng', array( $image, $filename ) ) )
				return new WP_Error( 'image_save_error', __('Image Editor Save Failed') );
		}
		elseif ( 'image/jpeg' == $mime_type ) {
			if ( ! $this->make_image( $filename, 'imagejpeg', array( $image, $filename, apply_filters( 'jpeg_quality', $this->quality, 'image_resize' ) ) ) )
				return new WP_Error( 'image_save_error', __('Image Editor Save Failed') );
		}
		else {
			return new WP_Error( 'image_save_error', __('Image Editor Save Failed') );
		}

		// Set correct file permissions
		$stat = stat( dirname( $filename ) );
		$perms = $stat['mode'] & 0000666; //same permissions as parent folder, strip off the executable bits
		@ chmod( $filename, $perms );

		return array(
			'path' => $filename,
			'file' => wp_basename( apply_filters( 'image_make_intermediate_size', $filename ) ),
			'width' => $this->size['width'],
			'height' => $this->size['height'],
			'mime-type'=> $mime_type,
		);
	}

	/**
	 * Returns stream of current image.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param string $mime_type
	 */
	public function stream( $mime_type = null ) {
		list( $filename, $extension, $mime_type ) = $this->get_output_format( null, $mime_type );

		switch ( $mime_type ) {
			case 'image/png':
				header( 'Content-Type: image/png' );
				return imagepng( $this->image );
			case 'image/gif':
				header( 'Content-Type: image/gif' );
				return imagegif( $this->image );
			default:
				header( 'Content-Type: image/jpeg' );
				return imagejpeg( $this->image, null, $this->quality );
		}
	}
}
?>