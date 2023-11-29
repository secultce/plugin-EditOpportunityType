<?php

namespace EditOpportunityType\Controllers;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use MapasCulturais\App;
use MapasCulturais\Entities\EntityRevision;
use MapasCulturais\Entities\EvaluationMethodConfiguration;

class EditOpportunityType extends \MapasCulturais\Controller
{
    /**
     * @return void
     */
    function POST_index()
    {
        $app = App::i();

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

        if($this->saveRevision($opp->evaluationMethodConfiguration)) {
            $this->json(['message' => 'Tipo da avaliação alterada com sucesso!']);
        } else {
            $this->json(['message' => 'Tipo de avaliação alterada, mas não foi possível registrar log.']);
        }
    }

    private function saveRevision(EvaluationMethodConfiguration $evaluationMethodConfiguration): bool
    {
        $app = App::i();
        $conn = $app->em->getConnection();

        try {
            $lastRevsisionDataId = $conn
                ->executeQuery('SELECT id FROM entity_revision_data ORDER BY "timestamp" DESC LIMIT 1')
                ->fetch()['id'];

            $sqlRevisionData = "INSERT INTO
                entity_revision_data (\"id\", \"timestamp\", \"key\", \"value\")
                VALUES (:id, :timestamp, :key, :value)";
            $conn->executeUpdate($sqlRevisionData, [
                'id' => $lastRevsisionDataId + 1,
                'timestamp' => (new \DateTime())->format(DATE_W3C),
                'key' => '_type',
                'value' => $this->data['newOpportunityType'],
            ]);

            $lastEntityRevisionId = $conn
                ->executeQuery('SELECT id FROM entity_revision ORDER BY "create_timestamp" DESC LIMIT 1')
                ->fetch()['id'];
            $sqlEntityRevision = "INSERT INTO
                entity_revision (id, user_id, object_id, object_type, create_timestamp, action, message)
                VALUES (:id, :user_id, :object_id, :object_type, :create_timestamp, :action, :message)";
            $conn->executeUpdate($sqlEntityRevision, [
                'id' => $lastEntityRevisionId+1,
                'user_id' => $app->user->id,
                'object_id' => $evaluationMethodConfiguration->id,
                'object_type' => EvaluationMethodConfiguration::class,
                'create_timestamp' => (new \DateTime())->format(DATE_W3C),
                'action' => EntityRevision::ACTION_MODIFIED,
                'message' => 'Registro modificado.',
            ]);

            $conn->executeUpdate("INSERT INTO entity_revision_revision_data VALUES (:revision_id, :revision_data_id)", [
                'revision_id' => $lastEntityRevisionId + 1,
                'revision_data_id' => $lastRevsisionDataId + 1
            ]);

            return true;
        } catch (UniqueConstraintViolationException $e) {
            if(strpos($e->getMessage(), 'entity_revision_pkey') !== false) {
                $conn->executeUpdate('DELETE FROM entity_revision_data WHERE id = :id', ['id' => $lastRevsisionDataId+1]);
            }

            return false;
        }
    }
}
