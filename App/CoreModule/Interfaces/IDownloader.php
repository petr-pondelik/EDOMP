<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 13.12.19
 * Time: 10:31
 */

namespace App\CoreModule\Interfaces;

use Nette\Application\IResponse;

/**
 * Interface IDownloader
 * @package App\CoreModule\Interfaces
 */
interface IDownloader
{
    public function download(): IResponse;
}