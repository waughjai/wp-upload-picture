WP Upload Picture
=========================

Class for autogenerating picture tag HTML for WordPress media image with just the ID. Works just like WPUploadImage, but without the need for giving a $sizes string ( since the whole point o’ a picture tag is to be responsive ) & creates an HTMLPicture object ’stead o’ just an HTMLImage object.

Error handling works just as it does for WPUploadImage. Look up its documentation for mo’ information.

## Changelog

### 0.5.0
* Implement WPUploadImage's WPMissingMediaIDException Error Handling

### 0.4.0
* Update Error Handling

### 0.3.0
* Add Ability to Easily Cancel Showing Versioning

### 0.2.0
* Make Compatible with New HTMLPicture Interface

### 0.1.0
* Initial Release
