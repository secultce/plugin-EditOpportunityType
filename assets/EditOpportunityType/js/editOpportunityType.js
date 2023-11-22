$(document).ready(() => {
    $('#editTypeConfirm').on('click', () => {
      const newOpportunityTypeShowValue = $('#opportunityType option:selected')[0].innerText;
      $('#newOpportunityTypeShow')[0].innerText = newOpportunityTypeShowValue;
    })

    $('#dialog-confirm-change button.js-close').on('click', e => {
        if(!e.target.value) {
            alert('Operação cancelada!');
            return;
        }


    })
})
