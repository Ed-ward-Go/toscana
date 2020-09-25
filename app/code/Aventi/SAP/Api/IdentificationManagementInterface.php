<?php


namespace Aventi\SAP\Api;

/**
 * Interface IdentificationManagementInterface
 *
 * @package Aventi\SAP\Api
 */
interface IdentificationManagementInterface
{

    /**
     * PUT for identification api
     * @param string $param
     * @return string
     */
    public function putIdentification($param);
}