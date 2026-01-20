<?php

class DistributionOfUniformPerStudentController extends Portabilis_Controller_ReportCoreController
{
    /**
     * @var int
     */
    protected $_processoAp = 999224;

    /**
     * @var string
     */
    protected $_titulo = 'Distribução de uniforme por aluno';

    /**
     * @inheritdoc
     */
    protected function _preRender()
    {
        parent::_preRender();

        Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

        $this->breadcrumb('Distribução de uniforme por aluno', [
            'educar_index.php' => 'Escola',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function form()
    {
        $this->inputsHelper()->dynamic(['ano', 'instituicao', 'escola', 'curso', 'serie', 'turma']);
        $this->inputsHelper()->dynamic('ano', ['required' => false]);
        $this->inputsHelper()->dynamic('curso', ['required' => false]);
        $this->inputsHelper()->dynamic('serie', ['required' => false]);
        $this->inputsHelper()->dynamic('turma', ['required' => false]);

        $this->inputsHelper()->select('modelo', [
            'label' => 'Modelo',
            'resources' => [
                1 => 'Modelo 1',
                2 => 'Modelo 2',
                3 => 'Modelo 2026',
            ],
            'required' => false,
            'value' => 1
        ]);

        $resources = [
            1 => 'Aprovado',
            2 => 'Reprovado',
            14 => 'Reprovado por falta',
            3 => 'Cursando',
            4 => 'Transferido',
            5 => 'Reclassificado',
            6 => 'Abandono',
            7 => 'Em exame',
            9 => 'Exceto Transferidos/Abandono',
            10 => 'Todas',
            12 => 'Aprovado com dependência',
            16 => 'Aprovado após exame'
        ];

        $options = [
            'label' => 'Situação do aluno',
            'resources' => $resources,
            'value' => 10
        ];

        $this->inputsHelper()->select('situacao_matricula', $options);

        $this->inputsHelper()->select('tipo_kit', [
            'label' => 'Tipo de kit',
            'resources' => [
                0 => 'Todos',
                1 => 'Inverno',
                2 => 'Verão'
            ],
            'required' => false,
            'value' => 0
        ]);

        $this->inputsHelper()->simpleSearchAluno(null, ['required' => false]);

        $this->loadResourceAssets($this->getDispatcher());
    }

    /**
     * @inheritdoc
     */
    public function beforeValidation()
    {
        $this->report->addArg('ano', (int) $this->getRequest()->ano);
        $this->report->addArg('instituicao', (int) $this->getRequest()->ref_cod_instituicao);
        $this->report->addArg('escola', (int) $this->getRequest()->ref_cod_escola);
        $this->report->addArg('aluno', (int) $this->getRequest()->aluno);
        $this->report->addArg('curso', (int) $this->getRequest()->ref_cod_curso);
        $this->report->addArg('serie', (int) $this->getRequest()->ref_cod_serie);
        $this->report->addArg('turma', (int) $this->getRequest()->ref_cod_turma);
        $this->report->addArg('modelo', (int) $this->getRequest()->modelo);
        $this->report->addArg('situacao', (int) $this->getRequest()->situacao_matricula);
        $this->report->addArg('tipo_kit', (int) $this->getRequest()->tipo_kit);
    }

    /**
     * @return DistributionOfUniformPerStudentReport
     *
     * @throws Exception
     */
    public function report()
    {
        return new DistributionOfUniformPerStudentReport();
    }
}


