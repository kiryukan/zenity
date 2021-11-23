<?php


namespace AppBundle\RessourceProvider\Interfaces;


interface IJsonUpdatable
{
    /**
     * transform a valid json into $this
     * @param $json String
     */
    public function updateFromJson($entity,$json);
}