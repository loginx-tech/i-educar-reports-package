$j("#situacao_matricula").closest('tr').hide();
$j("#tipo_kit").closest('tr').hide();
$j("#aluno").closest('tr').show();

$j("#modelo").on('click', function(){
    if ( $j("#modelo").val() == 1) {
      $j('#ano').makeRequired();
      $j('#ref_cod_escola').makeRequired();
      $j("#situacao_matricula").closest('tr').hide();
      $j("#tipo_kit").closest('tr').hide();
      $j("#aluno").closest('tr').show();
    }

    if ( $j("#modelo").val() == 2) {
      $j('#ano').makeRequired();
      $j('#ref_cod_escola').makeUnrequired();
      $j("#situacao_matricula").closest('tr').show();
      $j("#tipo_kit").closest('tr').show();
      $j("#aluno").closest('tr').hide();
    }

    if ( $j("#modelo").val() == 3) {
      $j('#ano').makeRequired();
      $j('#ref_cod_escola').makeUnrequired();
      $j("#situacao_matricula").closest('tr').show();
      $j("#tipo_kit").closest('tr').hide();
      $j("#aluno").closest('tr').hide();
    }
});

