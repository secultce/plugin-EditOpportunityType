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

        if($opp->evaluationMethodConfiguration->type == 'documentary') {
            $opp->evaluationMethodConfiguration->type = $this->data['newOpportunityType'];

            $opp->evaluationMethodConfiguration->save(true);
        };

    }
}
