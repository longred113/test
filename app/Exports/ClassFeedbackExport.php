<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassFeedbackExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    protected $classFeedbackData;
    protected $averageSatisfaction;

    public function __construct($classFeedbackData, $averageSatisfaction)
    {
        $this->classFeedbackData = $classFeedbackData;
        $this->averageSatisfaction = $averageSatisfaction;
    }

    public function collection()
    {
        return $this->classFeedbackData;
    }

    public function headings(): array
    {
        return [
            [
                'Average Satisfaction',
                $this->averageSatisfaction[0],
            ],
            [
                'Class Feedback Id',
                'Teacher Id',
                'Teacher Name',
                'Class Id',
                'Class Name',
                'Student Id',
                'Student Name',
                'Campus Id',
                'Campus Name',
                'Satisfaction',
                'Date',
                'Comment',
                'product Id',
                'product Name',
            ]
        ];
    }
}
