<?php

namespace FrontModule\Presenters;

use Nette,
	Model,
	Controls;
use Nette\Application\UI\Multiplier;


/**
 * Homepage presenter.
 */
class IndexPresenter extends BasePresenter
{
	public function renderDefault ()
	{
		$this -> template -> wallposts = [];//$this -> context -> fb -> getAllPosts ();
		//print_r ( $this -> template -> wallposts );
		$paginator = $this [ "paginator" ] -> getPaginator ();
		$paginator -> itemCount = $this -> context -> posts -> count ( [ "hidden" => false ], [ "datum" => "DESC" ]  );
		$this -> template -> posts = $this -> context -> posts -> limit ( $paginator -> itemsPerPage, $paginator -> offset, [ "hidden" => false ], [ "datum" => "DESC" ] );
	}

	protected function createComponentPosts ()
	{
		$model = $this -> context -> posts;
		return new Multiplier ( function ( $some_id ) use ( $model )
		{
			$control = new Controls\PostControl ( $model, $some_id );
			return $control;
		});
	}

	public function renderWelcome ()
	{
	}


}
