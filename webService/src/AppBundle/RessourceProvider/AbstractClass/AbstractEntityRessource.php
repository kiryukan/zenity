<?php

namespace AppBundle\RessourceProvider\AbstractClass;


use AppBundle\RessourceProvider\Interfaces\IJsonUpdatable;
use AppBundle\RessourceProvider\Traits\EntityStatisticsMetadataTrait;
use AppBundle\RessourceProvider\Traits\RessourceFromEntityTrait;

abstract class AbstractEntityRessource extends AbstractRessource implements IJsonUpdatable
{
    Use EntityStatisticsMetadataTrait;
    Use RessourceFromEntityTrait;
}