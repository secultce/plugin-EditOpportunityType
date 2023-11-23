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
            if($this->data['entity']->evaluationMethodConfiguration->getType()->id !== 'documentary') {
                return;
            }

            if(!$app->user->isUserAdmin($app->getUser())) {
                return;
            }

            if($this->data['entity']->publishedRegistrations) {
                return;
            }

            $queryResult = $app->getEm()->getConnection()->fetchAll('SELECT DISTINCT "type" "type" FROM evaluation_method_configuration');

            $opportunityTypes = array_map(function ($row) use ($app) {
                $evaluationMethods = $app->getRegisteredEvaluationMethodBySlug($row['type']);
                return [$evaluationMethods->slug, $evaluationMethods->name];
            }, $queryResult);

            $currentType = $this->data["entity"]->evaluationMethodConfiguration->getType();
            $opportunityId = $this->data["entity"]->id;

            $app->view->enqueueStyle('app', 'editOpportunityType', 'EditOpportunityType/css/main.css');
            $app->view->part('select-opportunity-type', [
                'opportunityTypes' => $opportunityTypes,
                'currentType' => $currentType,
                'opportunityId' => $opportunityId,
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
