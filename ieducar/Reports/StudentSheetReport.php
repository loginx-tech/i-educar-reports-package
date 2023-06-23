<?php

use iEducar\Reports\JsonDataSource;

class StudentSheetReport extends Portabilis_Report_ReportCore
{
    use JsonDataSource;

    /**
     * @inheritdoc
     */
    public function templateName()
    {
        $modelos = [
            1 => 'student-sheet',
            2 => 'student-sheet-2'
        ];

        return $modelos[$this->args['modelo']];
    }

    /**
     * @inheritdoc
     */
    public function requiredArgs()
    {
        $this->addRequiredArg('instituicao');
        $this->addRequiredArg('escola');
        $this->addRequiredArg('modelo');
    }

    public function getJsonData()
    {
        try {
            return [
                'main' => (new QueryStudentSheet2)->get($this->args),
                'header' => Portabilis_Utils_Database::fetchPreparedQuery($this->getSqlHeaderReport())
            ];
        } catch (\Throwable $th) {
            dd($th);
        }
        if ($this->args['modelo'] == 2) {
            return [
                'main' => (new QueryStudentSheet2)->get($this->args),
                'header' => Portabilis_Utils_Database::fetchPreparedQuery($this->getSqlHeaderReport())
            ];
        }

        return [
            'main' => (new QueryStudentSheet)->get($this->args),
            'header' => Portabilis_Utils_Database::fetchPreparedQuery($this->getSqlHeaderReport())
        ];
    }
}
