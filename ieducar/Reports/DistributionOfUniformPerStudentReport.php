<?php

use iEducar\Reports\JsonDataSource;

class DistributionOfUniformPerStudentReport extends Portabilis_Report_ReportCore
{
    use JsonDataSource;

    /**
     * @inheritdoc
     */
    public function templateName()
    {
        return 'distribution-of-uniform-per-student';
    }

    /**
     * @inheritdoc
     */
    public function requiredArgs()
    {
        $this->addRequiredArg('instituicao');
        $this->addRequiredArg('escola');
    }

    /**
     * Retorna o SQL para buscar os dados do relatório principal.
     *
     * @return string
     *
     * @throws Exception
     */
    public function getSqlMainReport()
    {
        $escola = $this->args['escola'] ?: 0;
        $aluno = $this->args['aluno'] ?: 0;
        $curso = $this->args['curso'] ?: 0;
        $serie = $this->args['serie'] ?: 0;
        $turma = $this->args['turma'] ?: 0;
        $ano = $this->args['ano'] ?: date('Y');

        return "
SELECT aluno.cod_aluno AS \"codigo_aluno\",
       to_char(uniform_distributions.distribution_date, 'dd/mm/yyyy') AS \"data\",
       cadastro.pessoa.nome AS \"aluno\",
       uniform_distributions.type = 'Solicitado' AS \"solicitado\",
       uniform_distributions.type = 'Entregue' AS \"entregue\",
       CASE
            WHEN complete_kit THEN 'Kit Completo'
            WHEN winter_kit THEN 'Kit Inverno'
            WHEN summer_kit THEN 'Kit Verão'
       END AS kit_type,
       kit_size,
       COALESCE(uniform_distributions.complete_kit, false) AS \"recebeu_kit\",
       COALESCE(uniform_distributions.coat_pants_qty::int, '0') AS \"qt_agasalho\",
       COALESCE(uniform_distributions.shirt_short_qty::int, '0') AS \"qt_camiseta_curta\",
       COALESCE(uniform_distributions.shirt_long_qty::int, '0') AS \"qt_camiseta_longa\",
       COALESCE(uniform_distributions.socks_qty::int, '0') AS \"qt_meias\",
       COALESCE(uniform_distributions.shorts_tactel_qty::int, '0') AS \"qt_bermudas_tectels\",
       COALESCE(uniform_distributions.shorts_coton_qty::int, '0') AS \"qt_bermudas_coton\",
       COALESCE(uniform_distributions.sneakers_qty::int, '0') AS \"qt_tenis\",
       COALESCE(uniform_distributions.coat_pants_tm, '') AS \"tm_agasalho\",
       COALESCE(uniform_distributions.shirt_short_tm, '') AS \"tm_camiseta_curta\",
       COALESCE(uniform_distributions.shirt_long_tm, '') AS \"tm_camiseta_longa\",
       COALESCE(uniform_distributions.socks_tm, '') AS \"tm_meias\",
       COALESCE(uniform_distributions.shorts_tactel_tm, '') AS \"tm_bermudas_tectels\",
       COALESCE(uniform_distributions.shorts_coton_tm, '') AS \"tm_bermudas_coton\",
       COALESCE(uniform_distributions.sneakers_tm, '') AS \"tm_tenis\",
        relatorio.get_nome_escola(uniform_distributions.school_id) AS \"escola_uniforme\",
       relatorio.get_nome_escola(escola.cod_escola) AS \"escola\",
       uniform_distributions.year AS \"ano\"
FROM pmieducar.escola
INNER JOIN pmieducar.escola_ano_letivo ON (escola_ano_letivo.ref_cod_escola = escola.cod_escola)
INNER JOIN pmieducar.escola_curso ON (escola_curso.ref_cod_escola = escola.cod_escola)
INNER JOIN pmieducar.escola_serie ON (escola_serie.ref_cod_escola = escola.cod_escola)
INNER JOIN pmieducar.curso ON (curso.cod_curso = escola_curso.ref_cod_curso AND curso.ativo = 1)
INNER JOIN pmieducar.serie ON (serie.cod_serie = escola_serie.ref_cod_serie AND serie.ativo = 1)
INNER JOIN pmieducar.turma ON (turma.ref_ref_cod_escola = escola.cod_escola
			   AND turma.ref_ref_cod_serie = serie.cod_serie
			   AND turma.ano = escola_ano_letivo.ano)
INNER JOIN pmieducar.matricula ON (matricula.ref_ref_cod_escola = escola.cod_escola
			       AND matricula.ref_cod_curso = curso.cod_curso
			       AND matricula.ref_ref_cod_serie = serie.cod_serie
			       AND matricula.ano = escola_ano_letivo.ano)
INNER JOIN pmieducar.matricula_turma ON (matricula_turma.ref_cod_turma = turma.cod_turma
				     AND matricula_turma.ref_cod_matricula = matricula.cod_matricula
				     AND matricula_turma.sequencial = relatorio.get_max_sequencial_matricula(matricula_turma.ref_cod_matricula))
INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = matricula.ref_cod_aluno)
INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
INNER JOIN public.uniform_distributions ON (uniform_distributions.student_id = aluno.cod_aluno
					   AND uniform_distributions.year = escola_ano_letivo.ano
                       AND uniform_distributions.school_id = escola.cod_escola)
WHERE escola.cod_escola = {$escola}
  AND matricula.aprovado = 3
  AND (CASE WHEN {$aluno} = 0 THEN TRUE ELSE {$aluno} = aluno.cod_aluno END)
  AND (CASE WHEN {$curso} = 0 THEN TRUE ELSE {$curso} = curso.cod_curso END)
  AND (CASE WHEN {$serie} = 0 THEN TRUE ELSE {$serie} = serie.cod_serie END)
  AND (CASE WHEN {$turma} = 0 THEN TRUE ELSE {$turma} = turma.cod_turma END)
  AND (CASE WHEN {$ano} = 0 THEN TRUE ELSE {$ano} = uniform_distributions.year END)
GROUP BY aluno,
         codigo_aluno,
         uniform_distributions.distribution_date,
         escola,
         uniform_distributions.year,
         uniform_distributions.type,
         recebeu_kit,
         complete_kit,
         winter_kit,
         summer_kit,
         kit_size,
         qt_agasalho,
         qt_camiseta_curta,
         qt_camiseta_longa,
         qt_meias,
         qt_bermudas_tectels,
         qt_bermudas_coton,
         qt_tenis,
         tm_agasalho,
         tm_camiseta_curta,
         tm_camiseta_longa,
         tm_meias,
         tm_bermudas_tectels,
         tm_bermudas_coton,
         tm_tenis,
         escola,
         escola_uniforme
ORDER BY aluno
        ";
    }
}
