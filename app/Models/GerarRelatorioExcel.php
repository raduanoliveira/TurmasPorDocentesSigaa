<?php

namespace App\Models;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GerarRelatorioExcel implements FromView, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    public $data;
    public $template;

    public function __construct($data, $template)
    {
        $this->data = $data;
        $this->template = $template;

    }

    public function columnFormats(): array
    {
        return [
            // 'E' => '@',//o @ é o código do texto da planilha
        ];
    }

    public function view(): View
    {
        return view($this->template,['dados'=>$this->data]);
    }

    public function styles(Worksheet $sheet)
    {
        $auxContaTurmas = 0;
        foreach ($this->data as $dado) {
            if(isset($dado->turmas)){
                foreach ($dado->turmas as $contador) {
                    $auxContaTurmas++;
                }
            }
        }
        $coluna = "D";
        $qtlLinhas = count($this->data) + $auxContaTurmas +1;


                //Aplicar cor na primeira linha
                $sheet->getStyle("A1:" . $coluna . "1")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('ffff66');
    
            //Aplicar bold na primeira linha
            $sheet->getStyle("A1:" . $coluna . "1")->getFont()->setBold(true);
    
            //Aplicar borda em toda a tabela
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ];
    
            $sheet->getStyle("A1:" . $coluna . "" . $qtlLinhas)->applyFromArray($styleArray);

    }
}
