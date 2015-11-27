<?php

namespace FrontModule\Presenters;

use Nette,
	Model;

use Tracy\Dumper;
use Tulinkry;

/**
 * Homepage presenter.
 */
class BandPresenter extends BasePresenter
{

	public function renderDefault ()
	{
		$this -> template -> descriptions = $this -> context -> musicians -> by ( [], [ "surname" => "DESC", "name" => "DESC" ] );
	}



}
