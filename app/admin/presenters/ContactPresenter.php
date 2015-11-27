<?php

namespace AdminModule\Presenters;

use Nette,
	Model;

use Tulinkry;


/**
 * Homepage presenter.
 */
class ContactPresenter extends BasePresenter
{

	protected function createComponentContactForm ( $name )
	{
		if ( array_key_exists("contactEmails", $this -> parameters -> params ) )
		$contacts = $this -> parameters -> params [ "contactEmails" ];
		$form = new Tulinkry\Forms\ContactForm ( $contacts, "Concrete Ostrich" );
		return $form;
	}


}
