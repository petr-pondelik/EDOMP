<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;

		$adminRouter = new RouteList('Teacher');
		$adminRouter[] = new Route('/teacher/<presenter>/<action>[/<id>]', [
            'presenter' => 'Homepage',
            'action' => 'default'
        ]);

		bdump($adminRouter);

		$frontRouter = new RouteList('Front');
		$frontRouter[] = new Route('/<presenter>/<action>[/<id>]', [
            'presenter' => 'Homepage',
            'action' => 'default'
        ]);

		$router[] = $adminRouter;
		$router[] = $frontRouter;
		return $router;
	}
}
