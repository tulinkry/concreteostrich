<?php

namespace Router;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();
		$router[] = new Route('[home]', 'Front:Index:default');
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Index:default');
		return $router;
	}

}
