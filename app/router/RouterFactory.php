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

		$adminRouter = new RouteList('Admin');
		$adminRouter[] = new Route('admin/<presenter>/<action>[/<id>]', [
            'presenter' => 'Homepage',
            'action' => 'default'
        ]);
		$frontRouter = new RouteList('Front');
		$frontRouter[] = new Route('/<presenter>/<action>[/<id>]', [
            'presenter' => 'Homepage',
            'action' => 'default'
        ]);
		$defaultRouter = new RouteList('Auth');
        $frontRouter[] = new Route('/<presenter>/<action>[/<id>]', [
            'presenter' => 'Sign',
            'action' => 'in'
        ]);
		$router[] = $adminRouter;
		$router[] = $frontRouter;
		$router[] = $defaultRouter;
		return $router;
	}
}
