$(document).ready(() => {
    $('#dialog-confirm-change button.js-close').on('click', e => {
        if(e.target.value) {
            const newOpportunityType = $('#opportunityType').val();
            const opportunityId = $('#opportunityId').val();

            fetch(MapasCulturais.baseURL + 'alterar-tipo-de-oportunidade', {
                method: 'POST',
                body: new URLSearchParams({newOpportunityType, opportunityId}).toString(),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data)
                })
                .catch(() => {})

            // $.ajax({
            //     url: MapasCulturais.baseURL + 'alterar-tipo-de-oportunidade',
            //     dataType: 'json',
            //     data: {newOpportunityType},
            //     method: 'POST'
            // })
            //     .done(data => {
            //         console.log(data)
            //     })
        }
    })
})
