<?php

namespace EditOpportunityType\Controllers;

class EditOpportunityType extends \MapasCulturais\Controller
{
    /**
     * @return void
     */
    function POST_index()
    {
        $app = \MapasCulturais\App::i();

        $this->requireAuthentication();
        $app->user->isUserAdmin($app->user);

        /**
         * @var \MapasCulturais\Entities\Opportunity $opp
         */
        $opp = $app->repo('Opportunity')->find($this->data['opportunityId']);

        /*
         * Esse plugin inicialmente só permitirá alterar avaliações de documental para técnica
         */
        if($opp->registrationTo < new \DateTime()) {
            $this->errorJson(['message' => 'O tipo de avaliação dessa oportunidade não pode ser alterado porque o período de inscrições já finalizou.'], 400);
            return;
        }

        if($opp->evaluationMethodConfiguration->type != 'documentary') {
            $this->errorJson(['message' => 'O tipo de avaliação dessa oportunidade não pode ser alterado por não ser documental.'], 400);
            return;
        }

        if($this->data['newOpportunityType'] != 'technical') {
            $this->errorJson(['message' => 'Tipo de avaliação informado é inválido.'], 400);
            return;
        }

        $opp->evaluationMethodConfiguration->type = $this->data['newOpportunityType'];
        $opp->evaluationMethodConfiguration->save(true);

        $this->json(['message' => 'Tipo da avaliação alterada com sucesso!']);
    }
}
