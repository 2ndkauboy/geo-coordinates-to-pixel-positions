<?php

require 'vendor/autoload.php';

use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;

// Initialize the PROJ library.
$proj = new Proj4php();

// Set the source and destination projection.
$src_projection = new Proj( 'EPSG:4326', $proj );
$dst_projection = new Proj( 'EPSG:3857', $proj );

// Geocode "Berlin, Germany".
$src_berlin = new Point( 13.3777041, 52.5162746, $src_projection );
print_r( $src_berlin->toArray() );
/**
 * Array
 * (
 *     [0] => 52.5162746
 *     [1] => 13.3777041
 *     [2] => 0
 * )
 */

$dst_berlin = $proj->transform( $dst_projection, $src_berlin );
print_r( $dst_berlin->toArray() );
/**
 * Array
 * (
 *     [0] => 1489199.2083951
 *     [1] => 6894018.2850289
 *     [2] => 0
 * )
 */

/**
 * Calculate boundaries
 */
$swap_x = false;
$swap_y = true;

$dst_points_x = [];
$dst_points_y = [];

$src_points = [
	[ 15.043380, 47.269133 ],
	[ 5.865010, 55.057722 ],
];

foreach ( $src_points as $point ) {
	$src_point      = new Point( $point[0], $point[1], $src_projection );
	$dst_point      = $proj->transform( $dst_projection, $src_point );
	$dst_point_arr  = $dst_point->toArray();
	$dst_points_x[] = $dst_point_arr[0];
	$dst_points_y[] = $dst_point_arr[1];
}

$src_boundaries = [
	'xmin' => $swap_x ? max( $dst_points_x ) : min( $dst_points_x ),
	'xmax' => $swap_x ? min( $dst_points_x ) : max( $dst_points_x ),
	'ymin' => $swap_y ? max( $dst_points_y ) : min( $dst_points_y ),
	'ymax' => $swap_y ? min( $dst_points_y ) : max( $dst_points_y ),
];

var_dump( $src_boundaries );
/**
 * array(4) {
 *   ["xmin"]=>
 *   float(653037.4250227585)
 *   ["xmax"]=>
 *   float(1668369.9214457471)
 *   ["ymin"]=>
 *   float(5986273.409259587)
 *   ["ymax"]=>
 *   float(7373214.063855921)
 * }
 */

$image_boundaries = [
	'xmin' => 0,
	'xmax' => 585.506,
	'ymin' => 0,
	'ymax' => 791.999,
];

$dst_berlin_arr = $dst_berlin->toArray();
$lng = $dst_berlin_arr[0];
$lat = $dst_berlin_arr[1];

$x_pos = ( $lng - $src_boundaries['xmin'] ) / ( $src_boundaries['xmax'] - $src_boundaries['xmin'] ) * ( $image_boundaries['xmax'] - $image_boundaries['xmin'] );

$y_pos = ( $lat - $src_boundaries['ymin'] ) / ( $src_boundaries['ymax'] - $src_boundaries['ymin'] ) * ( $image_boundaries['ymax'] - $image_boundaries['ymin'] );

var_dump( [ $x_pos, $y_pos ] );
/**
 * array(2) {
 *   [0]=>
 *   float(482.18464676347816)
 *   [1]=>
 *   float(273.64009871474894)
 * }
 */

// <circle cx="482.18464676347816" cy="273.64009871474894" r="5" stroke="red" stroke-width="2" fill="transparent" />