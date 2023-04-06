<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithBackgroundColor;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ClassReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithEvents
{
    protected $classReportData;

    public function __construct($classReportData)
    {
        $this->classReportData = $classReportData;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->classReportData;
    }

    public function headings(): array
    {
        return [
            'Class Report Id',
            'Teacher Id',
            'Teacher Name',
            'Class Id',
            'Class Name',
            'Student Id',
            'Student Name',
            'Campus Id',
            'Campus Name',
            'attendance',
            'Date',
            'Comment',
            'preparation',
            'attitude',
            'participation',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:O1')->getFont()->setName('TimeNewRoman')->setSize(14)->setBold(true);
                $event->sheet->getDelegate()->getStyle('A2:O10')->getFont()->setName('TimeNewRoman');
            },
        ];
    }
}
