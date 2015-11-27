<?php

namespace Controls;

use Tulinkry\Application\UI;

class PostControl extends UI\Control
{

	protected $posts;
	protected $post;

	public function __construct ( $model, $id )
	{
		$this -> posts = $model;
		$this -> post = $model -> item ( $id );
	}

	public function render ()
	{
		$this -> template -> setFile ( __DIR__ . "/postControl.latte" );

		$args = func_get_args();

		$this -> template -> short = [];

		if ( is_array ( $args ) && count ( $args ) > 0 )
		{
			foreach ( $args as $key => $value ) 
			{
				if ( is_array ( $value ) )
					foreach ( $value as $k => $v )
						$this -> template -> short [ $k ] = $v;
				elseif ( is_string ( $value ) )
					$this -> template -> setFile ( __DIR__ ."/" . $value . ".latte" );
			}
		}

		$this -> template -> post = $this -> post;

		$this -> template -> render ();
	}

};