$(document).ready(() => {
    $('#editTypeConfirm').on('click', () => {
      const confirmation = confirm('Tem certeza que deseja alterar o tipo de avaliação?');
      if(confirmation) {
          alert('Alteração realizada!');
          return;
      }

      alert('Operação cancelada!');
    })
})