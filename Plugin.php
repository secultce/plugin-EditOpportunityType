<?php

namespace EditOpportunityType;

use EditOpportunityType\Controllers\EditOpportunityType;
use MapasCulturais\App;

class Plugin extends \MapasCulturais\Plugin
{
    function _init()
    {
        $app = App::i();

        $app->hook('template(opportunity.edit.evaluations-config):begin', function () use($app) {
            /**
             * @var $opportunity MapasCulturais\Entities\Opportunity
             */
            $opportunity = $this->data['entity'];
            if(
                $opportunity->evaluationMethodConfiguration->getType()->id !== 'documentary'
                || !$opportunity->canUser('@control')
                || $opportunity->publishedRegistrations
            ) {
                return;
            }

            $queryResult = $app->em
                ->createQuery('SELECT DISTINCT emc._type FROM MapasCulturais\Entities\EvaluationMethodConfiguration emc')
                ->getArrayResult();

            $opportunityTypes = array_map(function ($row) use ($app) {
                $evaluationMethods = $app->getRegisteredEvaluationMethodBySlug($row['_type']);
                return [$evaluationMethods->slug, $evaluationMethods->name];
            }, $queryResult);

            $currentType = $opportunity->evaluationMethodConfiguration->getType();
            $opportunityId = $opportunity->id;

            $app->view->enqueueStyle('app', 'editOpportunityType', 'EditOpportunityType/css/main.css');

            $app->view->part('select-opportunity-type', [
                'opportunityTypes' => $opportunityTypes,
                'currentType' => $currentType,
                'opportunityId' => $opportunityId,
            ]);

            $app->view->enqueueScript('app', 'editOpportunityType', 'EditOpportunityType/js/editOpportunityType.js');
        });

    }

    /**
     * @throws \Exception
     */
    function register()
    {
        $app = App::i();
        $app->registerController('alterar-tipo-de-oportunidade', EditOpportunityType::class);
    }
}
