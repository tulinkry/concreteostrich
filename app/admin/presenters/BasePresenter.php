<?php

namespace AdminModule\Presenters;

use Nette;
use Tulinkry\Application\UI;
use Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends UI\AdminPresenter
{
	/** @var Model\EventModel @inject */
	public $events;
}
