<?php

use iEducar\Reports\JsonDataSource;

class DistributionOfUniformPerStudentReport extends Portabilis_Report_ReportCore
{
    use JsonDataSource, UniformDistributionTrait {
        UniformDistributionTrait::query as QueryUniformDistribution;
    }

    /**
     * @inheritdoc
     */
    public function templateName()
    {
        return $this->args['modelo'] == 2 ? 'distribution-of-uniform-per-schoolClass' : 'distribution-of-uniform-per-student';
    }

    /**
     * @inheritdoc
     */
    public function requiredArgs()
    {
        $this->addRequiredArg('instituicao');
    }

    public function getJsonData()
    {
        return [
            'main' => Portabilis_Utils_Database::fetchPreparedQuery($this->QueryUniformDistribution()),
            'header' => Portabilis_Utils_Database::fetchPreparedQuery($this->getSqlHeaderReport())
        ];
    }
}
