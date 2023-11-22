<?php

namespace EditOpportunityType\Controllers;

class EditOpportunityType extends \MapasCulturais\Controller
{
    function GET_index()
    {
        $app = \MapasCulturais\App::i();
        $this->render('index');
    }
}
