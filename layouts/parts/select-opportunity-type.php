
<div class="registration-fieldset">
    <h4>Alterar tipo de Avaliação</h4>
    <input type="text" name="opportunityType" list="opportunity-types" value="<?= $id ?>" >
    <button class="btn btn-primary" id="editTypeConfirm">Confirmar alteração</button>
    <datalist id="opportunity-types">
        <?php foreach ($opportunityTypes as $type) { ?>
            <option value="<?= $type[0] ?>"><?= $type[1] ?></option>
        <?php } ?>
    </datalist>
</div>
