<?php

class QueryStudentSheet2 extends QueryBridge
{
    /**
     * @inheritdoc
     */
    protected function query()
    {
        return <<<'SQL'
            SELECT
                instituicao.ref_sigla_uf AS uf_instituicao,
                instituicao.cidade AS cidade_instituicao,
                upper(pessoa.nome) AS nome_aluno,
                aluno.cod_aluno,
                aluno.aluno_estado_id AS cod_estado,
                cities.name AS cidade_nascimento,
                states.name AS state_nascimento,
                relatorio.get_nacionalidade(fisica.nacionalidade) AS nacionalidade,
                to_char(fisica.data_nasc, 'dd/mm/yyyy') AS data_nascimento,
                raca.nm_raca,
                CASE fisica.sexo
                    WHEN 'F' THEN 'Feminino'
                    WHEN 'M' THEN 'Masculino'
                    ELSE ''
                END AS sexo,
                fisica.sus,
                fisica.nis_pis_pasep AS nis,
                religions.name AS religiao,
                fisica.cpf,
                documento.certidao_nascimento,
                documento.num_tit_eleitor,
                relatorio.get_mae_aluno(cod_aluno) AS nome_mae,
                relatorio.get_pai_aluno(cod_aluno) AS nome_pai,
                CASE
                    WHEN url_laudo_medico->0->>'url' IS NULL THEN 'NÃO'
                    ELSE 'SIM'
                END AS possui_laudo,
                (
                    SELECT replace(textcat_all(deficiencia.nm_deficiencia), ' <br>', ', ')
                    FROM cadastro.fisica_deficiencia
                    JOIN cadastro.deficiencia ON deficiencia.cod_deficiencia = fisica_deficiencia.ref_cod_deficiencia
                    WHERE fisica_deficiencia.ref_idpes = aluno.ref_idpes
                ) AS deficiencias,
                (
                    SELECT
                        replace(
                            textcat_all('(' || fone_pessoa.ddd || ') ' || fone_pessoa.fone),
                            ' <br>', ', '
                        )
                    FROM cadastro.fone_pessoa
                    WHERE fone_pessoa.idpes = aluno.ref_idpes
                    AND tipo < 4
                    LIMIT 3
                ) AS telefones_aluno,
                (
                    SELECT
                        replace(
                            textcat_all('(' || fone_pessoa.ddd || ') ' || fone_pessoa.fone),
                            ' <br>', ', '
                        )
                    FROM cadastro.fone_pessoa
                    WHERE fone_pessoa.idpes = fisica.idpes_mae
                    AND tipo < 4
                    LIMIT 3
                ) AS telefones_mae,
                (
                    SELECT
                        replace(
                            textcat_all('(' || fone_pessoa.ddd || ') ' || fone_pessoa.fone),
                            ' <br>', ', '
                        )
                    FROM cadastro.fone_pessoa
                    WHERE fone_pessoa.idpes = fisica.idpes_pai
                    AND tipo < 4
                    LIMIT 3
                ) AS telefones_pai,
                (
                    SELECT
                        replace(
                            textcat_all('(' || fone_pessoa.ddd || ') ' || fone_pessoa.fone),
                            ' <br>', ', '
                        )
                    FROM cadastro.fone_pessoa
                    WHERE fone_pessoa.idpes = fisica.idpes_responsavel
                    AND tipo < 4
                    LIMIT 3
                ) AS telefones_responsavel,
                addresses.address AS endereco,
                addresses.neighborhood AS bairro,
                addresses.complement AS complemento,
                addresses.number AS numero,
                addresses.city AS cidade,
                addresses.state_abbreviation AS estado,
                addresses.postal_code AS cep,
                matricula.ano,
                serie.nm_serie,
                turma.nm_turma,
	            turma_turno.nome AS periodo,
                CASE matricula.aprovado
                    WHEN 1 THEN 'Aprovado'
                    WHEN 2 THEN 'Reprovado'
                    WHEN 3 THEN 'Cursando'
                    WHEN 4 THEN 'Transferido'
                    WHEN 5 THEN 'Reclassificado'
                    WHEN 6 THEN 'Abandono'
                    WHEN 12 THEN 'Aprovado com dependência'
                    WHEN 13 THEN 'Aprovado pelo conselho'
                    WHEN 14 THEN 'Reprovado por faltas'
                    WHEN 15 THEN 'Falecido'
                    ELSE 'Cursando'
                END AS situacao
            FROM pmieducar.matricula
            JOIN pmieducar.matricula_turma ON true
                AND matricula_turma.ref_cod_matricula = matricula.cod_matricula
                AND matricula_turma.ativo = 1
            JOIN pmieducar.turma ON turma.cod_turma = matricula_turma.ref_cod_turma
            LEFT JOIN pmieducar.turma_turno ON turma_turno.id = turma.turma_turno_id
            JOIN pmieducar.serie ON serie.cod_serie = matricula.ref_ref_cod_serie
            JOIN pmieducar.aluno ON aluno.cod_aluno = matricula.ref_cod_aluno
            JOIN cadastro.pessoa ON pessoa.idpes = aluno.ref_idpes
            JOIN cadastro.fisica ON fisica.idpes = aluno.ref_idpes
            JOIN pmieducar.instituicao ON instituicao.cod_instituicao = 1
            LEFT JOIN public.person_has_place ON person_has_place.person_id = aluno.ref_idpes
            LEFT JOIN public.addresses ON addresses.id = person_has_place.place_id
            LEFT JOIN cadastro.fisica_raca ON fisica_raca.ref_idpes = fisica.idpes
            LEFT JOIN cadastro.raca ON raca.cod_raca = fisica_raca.ref_cod_raca
            LEFT JOIN public.cities ON cities.id = fisica.idmun_nascimento
            LEFT JOIN public.states ON states.id = cities.state_id
            LEFT JOIN pmieducar.religions ON religions.id = fisica.ref_cod_religiao
            LEFT JOIN cadastro.documento ON documento.idpes = aluno.ref_idpes
            WHERE true
            AND matricula.ativo = 1
            AND matricula.ref_cod_curso IN (18,13,14,19)
            AND EXISTS (
                SELECT *
                FROM pmieducar.matricula_turma mt
                JOIN pmieducar.matricula m ON m.cod_matricula = mt.ref_cod_matricula
                WHERE true
                AND mt.ativo = 1
                AND m.ativo = 1
                AND mt.ref_cod_turma = $P{turma}
                AND m.ref_cod_aluno = matricula.ref_cod_aluno
                AND CASE WHEN $P{matricula} = 0 THEN true ELSE m.cod_matricula = $P{matricula} END
            )
            ORDER BY
                pessoa.nome,
                matricula.ano,
                nm_serie,
                nm_turma;
SQL;
    }
}
