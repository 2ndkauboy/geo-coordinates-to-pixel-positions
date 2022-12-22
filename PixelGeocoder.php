<?php

use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;

class PixelGeocoder {
	public $proj;
	public $src_proj;
	public $dst_proj;

	public $src_boundaries = [
		'xmin' => 0,
		'xmax' => 0,
		'ymin' => 0,
		'ymax' => 0,
	];

	public $image_boundaries = [
		'xmin' => 0,
		'xmax' => 0,
		'ymin' => 0,
		'ymax' => 0,
	];

	public function __construct( $src_proj_type = 'EPSG:4326', $dst_proj_type = 'EPSG:3857' ) {
		$this->proj     = new Proj4php();
		$this->src_proj = new Proj( $src_proj_type, $this->proj );
		$this->dst_proj = new Proj( $dst_proj_type, $this->proj );
	}

	public function setDstBoundaries( $points, $swap_x = false, $swap_y = false ) {
		$dst_points_x = [];
		$dst_points_y = [];

		foreach ( $points as $point ) {
			$dst_point      = $this->transformGPStoMapProjection( $point[0], $point[1] );
			$dst_points_x[] = $dst_point[0];
			$dst_points_y[] = $dst_point[1];
		}

		$this->src_boundaries = [
			'xmin' => $swap_x ? max( $dst_points_x ) : min( $dst_points_x ),
			'xmax' => $swap_x ? min( $dst_points_x ) : max( $dst_points_x ),
			'ymin' => $swap_y ? max( $dst_points_y ) : min( $dst_points_y ),
			'ymax' => $swap_y ? min( $dst_points_y ) : max( $dst_points_y ),
		];
	}

	public function transformGPStoMapProjection( $lng, $lat ) {
		$src_point = new Point( $lng, $lat, $this->src_proj );
		$dst_point = $this->proj->transform( $this->dst_proj, $src_point );

		return $dst_point->toArray();
	}

	public function calculateCoordinatesToPixel( $lng, $lat ) {
		return [
			( $lng - $this->src_boundaries['xmin'] ) / ( $this->src_boundaries['xmax'] - $this->src_boundaries['xmin'] ) * ( $this->image_boundaries['xmax'] - $this->image_boundaries['xmin'] ),
			( $lat - $this->src_boundaries['ymin'] ) / ( $this->src_boundaries['ymax'] - $this->src_boundaries['ymin'] ) * ( $this->image_boundaries['ymax'] - $this->image_boundaries['ymin'] ),
		];
	}
}
