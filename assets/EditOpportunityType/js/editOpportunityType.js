$(document).ready(() => {
    $('#editTypeConfirm').on('click', () => {
        $('#newOpportunityTypeShow').text($('#opportunityType option:selected')[0].innerText)
    })

    $('#dialog-confirm-change button.js-close').on('click', e => {
        if(e.target.value) {
            const newOpportunityType = $('#opportunityType').val();
            const opportunityId = $('#opportunityId').val()

            fetch(MapasCulturais.baseURL + 'alterar-tipo-de-oportunidade', {
                method: 'POST',
                body: new URLSearchParams({newOpportunityType, opportunityId}).toString(),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(response => response.json())
                .then(json => {
                    if(json.error) {
                        MapasCulturais.Messages.error(json.data.message)
                    } else {
                        MapasCulturais.Messages.success(json.message)
                        document.location.reload()
                    }
                })
                .catch(e => MapasCulturais.Messages.error('Erro inesperado'))
        }
    })

    $('#opportunityType').on('change', () => {
        $('#editTypeConfirm').style.pointerEvents = 'initial'
    })

})
