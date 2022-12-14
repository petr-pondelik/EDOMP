<?php

namespace App\Router;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * Class RouterFactory
 * @package App\Router
 */
final class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter(): Nette\Application\IRouter
	{
		$router = new RouteList;

		$teacherRouter = new RouteList('Teacher');
        $teacherRouter[] = new Route('/teacher/<presenter>/<action>[/<id>]', [
            'presenter' => 'Homepage',
            'action' => 'default'
        ]);

		bdump($teacherRouter);

		$studentRouter = new RouteList('Student');
        $studentRouter[] = new Route('/<presenter>/<action>[/<id>]', [
            'presenter' => 'Homepage',
            'action' => 'default'
        ]);

		$router[] = $teacherRouter;
		$router[] = $studentRouter;

		return $router;
	}
}
