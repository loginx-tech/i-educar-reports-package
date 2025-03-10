// subescreve parametro escola_id definido em SimpleSearchAluno.js, listando alunos de todas escolas
simpleSearchAlunoOptions.params.escola_id = 0;
var modelosPadroes = ['1','2'];
exibirCheckCargaFrequentada();
$j("#nm_secretario").closest('tr').hide();
$j("#nm_diretor").closest('tr').hide();
$j("#ano_ini").closest('tr').hide();
$j("#ano_fim").closest('tr').hide();
$j("#cursoaluno").closest('tr').hide();
$j("#apenas_ultimo_registro").closest('tr').hide();

$j("#ano_transferencia").closest('tr').hide();
$j("#cursos_transferencia").closest('tr').hide();

$j("#imprime_diretor_secretario").on('click', function(){
	if($j('#imprime_diretor_secretario').prop('checked')){
		$j("#nm_secretario").closest('tr').show();
		$j("#nm_diretor").closest('tr').show();
	}else{
		$j("#nm_secretario").closest('tr').hide();
		$j("#nm_diretor").closest('tr').hide();
	}
});

window.setTimeout(ocultar, 10); //espera 10 milesegundos para ocultar os campos
function ocultar(){
	$j('#ref_cod_curso').closest('tr').hide();
	$j('#ref_cod_serie').closest('tr').hide();
	$j('#ref_cod_turma').closest('tr').hide();
	$j('#ano').closest('tr').hide();
	ref_cod_turma.value = 0;
	ref_cod_serie.value = 0;
	ref_cod_curso.value = 0;
}

$j('#lote').click(function(){
    if ($j('#lote').prop('checked')){
      $j('#ref_cod_curso').closest('tr').show();
	  $j('#ref_cod_serie').closest('tr').show();
	  $j('#ref_cod_turma').closest('tr').show();
	  $j('#ano').closest('tr').show();
	  $j('#ref_cod_turma').val('');
	  $j('#ref_cod_curso').val('');
	  $j('#ref_cod_serie').val('');
	  $j('#aluno').closest('tr').hide();
	  $j('#aluno_id').val(0);
	  $j('#aluno').val('');
    }
    else{
      $j('#ref_cod_curso').closest('tr').hide();
	  $j('#ref_cod_serie').closest('tr').hide();
	  $j('#ref_cod_turma').closest('tr').hide();
	  $j('#ano').closest('tr').hide();
	  ref_cod_turma.value = 0;
	  ref_cod_serie.value = 0;
	  ref_cod_curso.value = 0;
	  $j('#aluno').closest('tr').show();
    }
});

$j("#modelo").on('click', function(){
    var template = $j("#modelo").val();
    if (template == 2) {
        $j("#ano_ini").closest('tr').show();
        $j("#ano_fim").closest('tr').show();
        $j("#apenas_ultimo_registro").closest('tr').show();
        $j("#cursoaluno").closest('tr').show();
    } else {
        $j("#ano_ini").closest('tr').hide();
        $j("#ano_fim").closest('tr').hide();
        $j("#apenas_ultimo_registro").closest('tr').hide();
        $j("#cursoaluno").closest('tr').hide();
    }

    if (template == 5 || template == 4 || template == 3) {
      $j("#ano_transferencia").closest('tr').show();
      $j("#cursos_transferencia").closest('tr').show();
    } else {
        $j("#ano_transferencia").closest('tr').hide();
        $j("#cursos_transferencia").closest('tr').hide();
    }
});

function exibirCheckCargaFrequentada() {
  const exibir = $j.inArray($j('#modelo').val(), modelosPadroes) != -1;
  $j('#tr_emitir_carga_horaria_frequentada').hide();
  if (exibir) {
    $j('#tr_emitir_carga_horaria_frequentada').show();
  }
}

$j('#modelo').change(function () {
  exibirCheckCargaFrequentada();
});

$j(function($){
  let toggleBolsaFamiliaVisibility = () => {
    let $container = $('#exibir_informacao_bolsa_familia').closest('tr');
    if (['1', '2'].include($('#modelo').val())) {
      $container.show();
    } else {
      $container.hide();
    }
  };

  $('#modelo').on('change', toggleBolsaFamiliaVisibility);

  toggleBolsaFamiliaVisibility();
});

