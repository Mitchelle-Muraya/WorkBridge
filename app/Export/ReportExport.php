<?php

namespace App\Exports;

use App\Models\Job;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Job::select('id', 'title', 'status', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Title', 'Status', 'Created At'];
    }
}
