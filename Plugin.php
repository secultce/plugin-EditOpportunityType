<?php

namespace EditOpportunityType;

use EditOpportunityType\Controllers\EditOpportunityType;
use EditOpportunityType\Repositories\EvaluationMethodConfiguration;
use MapasCulturais\App;

class Plugin extends \MapasCulturais\Plugin
{
    function _init()
    {
        $app = App::i();
        $app->hook('template(opportunity.edit.registration-config):after', function () use($app) {
            $eval = $app->repo('EvaluationMethodConfiguration')->findAll();

            $opportunityTypes = array_map("unserialize",array_unique(array_map("serialize",array_map(function($evaluation) {
                return [$evaluation->getType()->id, $evaluation->getType()->name];
                }, $eval))));

            $id = $this->data["entity"]->evaluationMethodConfiguration->getType();

            $app->view->part('select-opportunity-type', [
                'opportunityTypes' => $opportunityTypes,
                'id' => $id
            ]);

            $app->view->enqueueScript('app', 'editOpportunityType', 'js/editOpportunityType.js');
        });

    }

    function register()
    {
        $app = App::i();
        $app->registerController('alterar-tipo-de-oportunidade', EditOpportunityType::class);
    }
}
