<?php

trait UniformDistributionTrait
{
    /**
     * @inheritdoc
     */
    protected function query()
    {
        if ($this->args['modelo'] == 2) {
            return $this->template2query();
        }

        if ($this->args['modelo'] == 3) {
            return $this->template3query();
        }

        return $this->template1query();
    }

    protected function template1query()
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

    protected function template2query()
    {
        $escola = $this->args['escola'] ?: 0;
        $curso = $this->args['curso'] ?: 0;
        $serie = $this->args['serie'] ?: 0;
        $turma = $this->args['turma'] ?: 0;
        $situacao = $this->args['situacao'] ?: 0;
        $tipoKit = $this->args['tipo_kit'] ?: 0;
        $ano = $this->args['ano'] ?: date('Y');

        return "
            SELECT DISTINCT ON (p.nome, t.nm_turma, e.name, a.cod_aluno, m.cod_matricula, e.ano_letivo, e.tipo_kit)
                a.cod_aluno AS codigo_aluno,
                e.ano_letivo as ano,
                p.nome AS escola,
                t.nm_turma AS turma,
                t.cod_turma AS codigo_turma,
                e.name AS aluno,
                e.tamanho_kit as tamanho,
                CASE f.sexo
                    WHEN 'F' THEN 'Feminino'
                    WHEN 'M' THEN 'Masculino'
                    ELSE 'Não informado'
                END AS sexo,
                vs.texto_situacao AS situacao,
                CASE
                    WHEN e.tipo_kit = 1 THEN 'Inverno'
                    WHEN e.tipo_kit = 2 THEN 'Verão'
                    ELSE 'Desconhecido'
                END AS tipo_kit,
                TO_CHAR(e.data_entrega, 'DD-MM-YYYY') AS data_entrega,
                e.responsavel as responsavel_entrega
            FROM public.edutopia_uniformes AS e
            INNER JOIN pmieducar.matricula AS m ON e.aluno_cod = m.cod_matricula AND m.ativo = 1
            INNER JOIN pmieducar.matricula_turma mt ON mt.ref_cod_turma = e.turma_cod AND mt.ref_cod_matricula = m.cod_matricula
            INNER JOIN relatorio.view_situacao vs ON (
                vs.cod_matricula = m.cod_matricula
                AND vs.cod_turma = mt.ref_cod_turma
                AND vs.sequencial = mt.sequencial
                AND vs.cod_situacao = $situacao
            )
            INNER JOIN pmieducar.aluno a ON a.cod_aluno = m.ref_cod_aluno
            INNER JOIN cadastro.fisica f ON f.idpes = a.ref_idpes
            INNER JOIN pmieducar.turma AS t ON e.turma_cod = t.cod_turma
            LEFT JOIN pmieducar.escola AS esco ON esco.cod_escola = m.ref_ref_cod_escola
            LEFT JOIN cadastro.pessoa AS p ON p.idpes = esco.ref_idpes
            WHERE true
                AND e.ano_letivo = $ano
                AND CASE WHEN $escola = 0 THEN true ELSE esco.cod_escola = $escola END
                AND CASE WHEN $curso = 0 THEN true ELSE m.ref_cod_curso = $curso END
                AND CASE WHEN $serie = 0 THEN true ELSE m.ref_ref_cod_serie = $serie END
                AND CASE WHEN $turma = 0 THEN true ELSE mt.ref_cod_turma = $turma END
                AND CASE WHEN $tipoKit = 0 THEN true ELSE e.tipo_kit = $tipoKit END
            ORDER BY p.nome, t.nm_turma, e.name, a.cod_aluno, m.cod_matricula, e.ano_letivo, e.tipo_kit
        ";
    }

    protected function template3query()
    {
        $escola = $this->args['escola'] ?: 0;
        $curso = $this->args['curso'] ?: 0;
        $serie = $this->args['serie'] ?: 0;
        $turma = $this->args['turma'] ?: 0;
        $situacao = $this->args['situacao'] ?: 0;
        $tipoKit = $this->args['tipo_kit'] ?: 0;
        $ano = $this->args['ano'] ?: date('Y');

        return "
            SELECT
                a.cod_aluno AS codigo_aluno,
                eu.ano_letivo as ano,
                p.nome AS escola,
                t.nm_turma AS turma,
                t.cod_turma AS codigo_turma,
                eu.name AS aluno,
                eu.tamanho_kit as tamanho,
                CASE f.sexo
                    WHEN 'F' THEN 'Feminino'
                    WHEN 'M' THEN 'Masculino'
                    ELSE 'Não informado'
                END AS sexo,
                vs.texto_situacao AS situacao,
                CASE
                    WHEN eu.tipo_kit = 1 THEN 'Inverno'
                    WHEN eu.tipo_kit = 2 THEN 'Verão'
                    WHEN eu.tipo_kit = 3 THEN 'Inverno / Verão'
                    ELSE 'Desconhecido'
                END AS tipo_kit,
                TO_CHAR(eu.data_entrega, 'DD-MM-YYYY') AS data_entrega,
                eu.responsavel as responsavel_entrega
            FROM pmieducar.matricula m
            INNER JOIN pmieducar.matricula_turma mt ON mt.ref_cod_matricula = m.cod_matricula
            INNER JOIN relatorio.view_situacao vs ON (
                vs.cod_matricula = m.cod_matricula
                AND vs.cod_turma = mt.ref_cod_turma
                AND vs.sequencial = mt.sequencial
                AND vs.cod_situacao = $situacao
            )
            INNER JOIN pmieducar.aluno a ON a.cod_aluno = m.ref_cod_aluno
            INNER JOIN cadastro.fisica f ON f.idpes = a.ref_idpes
            INNER JOIN pmieducar.turma AS t ON mt.ref_cod_turma = t.cod_turma
            INNER JOIN (
                SELECT
                    DISTINCT ON (ref_cod_aluno, ano_letivo)
                    edutopia_uniformes.*
                    FROM edutopia_uniformes
                    ORDER BY ref_cod_aluno, ano_letivo, created_at DESC
            ) eu ON eu.ref_cod_aluno = a.cod_aluno AND eu.ano_letivo = m.ano
            LEFT JOIN pmieducar.escola AS esco ON esco.cod_escola = m.ref_ref_cod_escola
            LEFT JOIN cadastro.pessoa AS p ON p.idpes = esco.ref_idpes
            WHERE true
                AND eu.ano_letivo = $ano
                AND CASE WHEN $escola = 0 THEN true ELSE esco.cod_escola = $escola END
                AND CASE WHEN $curso = 0 THEN true ELSE m.ref_cod_curso = $curso END
                AND CASE WHEN $serie = 0 THEN true ELSE m.ref_ref_cod_serie = $serie END
                AND CASE WHEN $turma = 0 THEN true ELSE mt.ref_cod_turma = $turma END
            ORDER BY p.nome, t.nm_turma, eu.name, a.cod_aluno, m.cod_matricula, eu.ano_letivo, eu.tipo_kit
        ";
    }
}
