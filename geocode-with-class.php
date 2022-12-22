<?php

require_once 'vendor/autoload.php';
require_once 'PixelGeocoder.php';

// Init PixelGeocoder using WGS84 and Mercato projection.
$pixel_geocoder = new PixelGeocoder( 'EPSG:4326', 'EPSG:3857' );
// Set boundaries for the map.
$pixel_geocoder->image_boundaries = [
	'xmin' => 0,
	'xmax' => 585.506,
	'ymin' => 0,
	'ymax' => 791.999,
];
$pixel_geocoder->setDstBoundaries(
	[
		[ 15.043380, 47.269133 ],
		[ 5.865010, 55.057722 ],
	],
	false,
	true
);

// Calculate the coordinates.
$berlin_lat     = 13.3777041;
$berlin_lng     = 52.5162746;
$dst_berlin_arr = $pixel_geocoder->transformGPStoMapProjection( $berlin_lat, $berlin_lng );
$image_coords   = $pixel_geocoder->calculateCoordinatesToPixel( $dst_berlin_arr[0], $dst_berlin_arr[1] );

var_dump( $image_coords );
/**
 * array(2) {
 *   [0]=>
 *   float(479.2493080704524)
 *   [1]=>
 *   float(273.55748351793665)
 * }
 */