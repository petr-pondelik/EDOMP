<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 11:37
 */

namespace App\CoreModule\Interfaces;

/**
 * Interface IDataBox
 * @package App\CoreModule\Interfaces
 */
interface IDataBox
{
    public function getData();

    /**
     * @param string $key
     * @return mixed
     */
    public function getByKey(string $key);
}