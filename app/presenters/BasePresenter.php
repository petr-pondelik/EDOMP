<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.2.19
 * Time: 21:53
 */

namespace App\Presenters;

use App\Model\Managers\BaseManager;
use App\Services\Authorizator;
use Nette\Application\UI\Presenter;

use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;

/**
 * Class BasePresenter
 * @package App\Presenters
 */
class BasePresenter extends Presenter
{
    public function beforeRender()
    {
        parent::beforeRender();
        $this->redrawControl('mathJaxRender');
    }
}