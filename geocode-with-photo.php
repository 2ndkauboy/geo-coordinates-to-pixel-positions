<?php

require_once 'vendor/autoload.php';
require_once 'PixelGeocoder.php';

// Init PixelGeocoder using WGS84 and Mercato projection.
$pixel_geocoder = new PixelGeocoder( 'EPSG:4326', 'EPSG:3857' );
// Set boundaries for the map.
$pixel_geocoder->image_boundaries = [
	'xmin' => 0,
	'xmax' => 2400,
	'ymin' => 0,
	'ymax' => 1800,
];

$map_edges = [
	[ 13.0467623, 52.5594922 ], // West.
	[ 13.1993623, 52.6484712 ], // North.
	[ 13.5841963, 52.4416892 ], // East.
	[ 13.2766553, 52.4069153 ], // South.
];

$pixel_geocoder->setDstBoundaries(
	$map_edges,
	false,
	true
);

// Calculate the coordinates.
$bb_gate_lat	 = 13.3777041;
$bb_gate_lng	 = 52.5162746;
$bb_gate_dst_arr = $pixel_geocoder->transformGPStoMapProjection( $bb_gate_lat, $bb_gate_lng );
$bb_gate_coords  = $pixel_geocoder->calculateCoordinatesToPixel( $bb_gate_dst_arr[0], $bb_gate_dst_arr[1] );

//var_dump( bb_gate_coords );
/**
 * array(2) {
 *   [0]=>
 *   float(1477.8750879177708)
 *   [1]=>
 *   float(986.3143837577029)
 * }
 */

$marker_markup = '
	<a xlink:title="%1$s" target="_parent" class="marker" id="%2$s" xlink:href="/%3$s/" transform="translate(%4$s,%5$s)">
		<path fill="#c10926" fill-rule="evenodd" d="m -0.266,-28.261 a 4.504,4.504 0 0 0 3.204,-1.343 4.613,4.613 0 0 0 1.327,-3.242 4.615,4.615 0 0 0 -1.327,-3.244 4.508,4.508 0 0 0 -3.204,-1.343 4.512,4.512 0 0 0 -3.206,1.343 4.619,4.619 0 0 0 -1.327,3.244 c 0,1.215 0.478,2.382 1.327,3.242 a 4.51,4.51 0 0 0 3.206,1.343 m -0.613,27.98 -8.895,-28.49 h 0.013 a 10.555,10.555 0 0 1 -0.818,-4.074 c 0,-2.77 1.086,-5.425 3.02,-7.381 a 10.251,10.251 0 0 1 7.294,-3.056 c 2.735,0 5.358,1.099 7.293,3.056 a 10.502,10.502 0 0 1 3.021,7.38 c 0,1.414 -0.284,2.798 -0.819,4.076 h 0.012 z" clip-rule="evenodd"/>
	</a>';

$markers = [
	[
		'name'  => 'brandenburg-gate',
		'title' => 'Brandenburg Gate',
		'x'	 => $bb_gate_coords[0],
		'y'	 => $bb_gate_coords[1],
		'url'   => 'https://en.wikipedia.org/wiki/Brandenburg_Gate',
	]
];

$circle_markup = '<circle cx="%1$s" cy="%2$s" r="40" stroke="cyan" stroke-width="5" fill="transparent" />';

$map_edges_markers = [];

foreach ( $map_edges as $map_edge ) {
	$map_edge_dst_arr	= $pixel_geocoder->transformGPStoMapProjection( $map_edge[0], $map_edge[1] );
	$map_edge_coords	 = $pixel_geocoder->calculateCoordinatesToPixel( $map_edge_dst_arr[0], $map_edge_dst_arr[1] );
	$map_edges_markers[] = $map_edge_coords;
}

?>
<style>
.image-map {
	position: relative;
	width: 600px;
	height: 450px;
}

.image-map-background,
.dynamic-map {
	max-width: 100%;
	height: auto;
}

.dynamic-map {
	position: absolute;
	top: 0;
	left: 0;
}

.marker path {
	transform: scale(5);
}
</style>
<div class="image-map">
	<img class="image-map-background" src="./Berlin-Germany-Flickr-NASA-Goddard-Photo-and-Video1.jpg" alt="Berlin NASA image"/>
	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="dynamic-map" width="2400" height="1800" viewBox="0 0 2400 1800">
		<?php foreach ( $markers as $marker ) : ?>
			<?php
			printf(
				$marker_markup,
				$marker['title'],
				$marker['name'],
				$marker['url'],
				$marker['x'],
				$marker['y']
			);
			?>
		<?php endforeach; ?>
		<?php foreach ( $map_edges_markers as $map_edges_marker ) : ?>
			<?php
			printf(
				$circle_markup,
				$map_edges_marker[0],
				$map_edges_marker[1]
			);
			?>
		<?php endforeach; ?>
	</svg>
</div>
