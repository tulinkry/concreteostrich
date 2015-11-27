<?php

namespace FrontModule\Presenters;

use Nette,
	Model;


/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{


	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = new Nette\Application\UI\Form;
		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Vložte své uživatelské jméno prosím.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Vložte své heslo prosím.');

		$form->addCheckbox('remember', 'Pamatovat si mě');

		$form->addSubmit('send', 'Přihlásit');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->signInFormSucceeded;
		return $form;
	}


	public function signInFormSucceeded($form, $values)
	{
		if ($values->remember) {
			$this->getUser()->setExpiration('14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('20 minutes', TRUE);
		}

		try {
			$this->getUser()->login($values->username, $values->password);
			$this->restoreRequest($this->backlink);
			$this->redirect(':Admin:Index:default');
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}


	public function actionLogout()
	{
		//$this->getUser()->logout();
		//$this->flashMessage('You have been signed out.');
		//$this->redirect('login');
	}

}
