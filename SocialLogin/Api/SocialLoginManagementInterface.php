<?php

namespace Codilar\SocialLogin\Api;

use Codilar\SocialLogin\Model\SocialLogin as Model;

/**
 * interface SocialLoginManagementInterface
 *
 * @description SocialLoginManagementInterface
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2021 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * all CURD Operations on SocialLogin Payload
 */
interface SocialLoginManagementInterface
{
    /**
     * @param $id
     * @return Model
     */
    public function getDataBYId($id);

    /**
     * @param $model
     * @return Model
     */
    public function save($model);

    /**
     * @param $model
     * @return Model
     */
    public function afterSave($model);


    /**
     * @param $model
     * @return Model
     */
    public function delete($model);

    /**
     * @param $value
     * @param null $field
     * @return Model
     */
    public function load($value, $field = null);

    /**
     * @return $model
     */
    public function create();

    /**
     * @param int $id
     * @return Model
     */
    public function deleteById($id);

    /**
     * @return Model
     */
    public function getCollection();
}
