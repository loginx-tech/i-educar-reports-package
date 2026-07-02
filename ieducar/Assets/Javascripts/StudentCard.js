$j('#modelo').change(onModeloClick);
$j('#cor_de_fundo').closest('tr').hide();
$j('#rota_transporte').closest('tr').hide();

function onModeloClick(){
  if($j('#modelo').val() == 1){
    $j('#imprimir_serie').closest('tr').show();
    $j('#cor_de_fundo').closest('tr').show();
  }else{
    $j('#imprimir_serie').closest('tr').hide();
    $j('#cor_de_fundo').closest('tr').hide();
  }

  if($j('#modelo').val() == 4){
    $j('#rota_transporte').closest('tr').show();
    $j('#rota_transporte').makeRequired();
    $j('#ref_cod_serie').makeUnrequired();
    $j('#ref_cod_turma').makeUnrequired();
  }else{
    $j('#rota_transporte').closest('tr').hide();
    $j('#rota_transporte').makeUnrequired();
    $j('#ref_cod_serie').makeRequired();
    $j('#ref_cod_turma').makeRequired();
  }
}

$j('#modelo').trigger('change');

function atualizaRotas() {
  var campoRota = document.getElementById('rota_transporte');
  var ano = $j('#ano').val();

  if (!campoRota || !ano) {
    return;
  }

  var url = getResourceUrlBuilder.buildUrl('/module/Api/Rota', 'rotas', {
    ano: ano
  });

  getResources({
    url: url,
    dataType: 'json',
    success: function (data) {
      campoRota.length = 1;
      campoRota.options[0].text = 'Selecione';
      campoRota.options[0].value = '';

      $j.each(data.options, function (id, nome) {
        campoRota.options[campoRota.options.length] = new Option(nome, id, false, false);
      });
    }
  });
}

$j('#ano').change(atualizaRotas);

$j('#validade').css('width', '229px').mask("99/9999", {placeholder: "__/____"});
