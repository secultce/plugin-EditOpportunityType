<?php

namespace EditOpportunityType;

use EditOpportunityType\Controllers\EditOpportunityType;
use MapasCulturais\App;

class Plugin extends \MapasCulturais\Plugin
{
    function _init()
    {
        $app = App::i();
        $app->hook('template(opportunity.edit.registration-config):after', function () use($app) {
            $queryResult = $app->getEm()->getConnection()->fetchAll('SELECT DISTINCT "type" "type" FROM evaluation_method_configuration');

            $opportunityTypes = array_map(function ($row) use ($app) {
                $evaluationMethods = $app->getRegisteredEvaluationMethodBySlug($row['type']);
                return [$evaluationMethods->slug, $evaluationMethods->name];
            }, $queryResult);

            $currentType = $this->data["entity"]->evaluationMethodConfiguration->getType();

            $app->view->enqueueStyle('app', 'editOpportunityType', 'EditOpportunityType/css/main.css');
            $app->view->part('select-opportunity-type', [
                'opportunityTypes' => $opportunityTypes,
                'currentType' => $currentType
            ]);

            $app->view->enqueueScript('app', 'editOpportunityType', 'EditOpportunityType/js/editOpportunityType.js');
        });

    }

    function register()
    {
        $app = App::i();
        $app->registerController('alterar-tipo-de-oportunidade', EditOpportunityType::class);
    }
}
