<?php

namespace EditOpportunityType\Repositories;

class EvaluationMethodConfiguration extends \MapasCulturais\Repository
{
    /**
     * @return \MapasCulturais\Entities\EvaluationMethodConfiguration[]
    */
    public function findAllMethods(): array
    {
        define (DQL, "SELECT UNIQUE type FROM evaluation_method_configuration");

        $q = $this->_em->createQuery(DQL);
        return $q->getResult();
    }
}