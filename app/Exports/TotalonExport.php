<?php

namespace app\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class TotalonExport implements FromArray, WithEvents, WithTitle
{
    private const CAMPOS = [
        'numero_de_cursos',
        'iniciaron_masculino',
        'iniciaron_femenino',
        'iniciaron_total',
        'desertados_masculino',
        'desertados_femenino',
        'desertados_total',
        'graduados_masculino',
        'graduados_femenino',
        'graduados_total',
    ];

    public function __construct(
        private Collection $filas,
        private object $totales,
        private string $periodo
    ) {}

    public function title(): string
    {
        return 'TOTALON';
    }

    private function total(string $campo): int
    {
        return (int) ($this->totales->{$campo} ?? 0);
    }

    public function array(): array
    {
        $datos = [
            ['FORMATEC - INFORME TOTALON'],
            ["Período: {$this->periodo}"],
            ['Consolidado oficial de grupos finalizados'],
            [
                'CURSO', 'N.º GRUPOS',
                'INICIARON', '', '',
                'DESERTADOS', '', '',
                'GRADUADOS', '', '',
            ],
            [
                '', '',
                'MASCULINO', 'FEMENINO', 'TOTAL',
                'MASCULINO', 'FEMENINO', 'TOTAL',
                'MASCULINO', 'FEMENINO', 'TOTAL',
            ],
        ];

        foreach ($this->filas as $fila) {
            $datos[] = [
                $fila->curso,
                ...array_map(
                    fn ($campo) => (int) ($fila->{$campo} ?? 0),
                    self::CAMPOS
                ),
            ];
        }

        $datos[] = [
            'TOTAL GENERAL',
            ...array_map(
                fn ($campo) => $this->total($campo),
                self::CAMPOS
            ),
        ];

        $datos[] = [
            'Nota: solo se contabilizan grupos con estado FINALIZADO, '
            . 'según su fecha de finalización.',
        ];

        $datos[] = [
            'Los graduados se calculan según el resultado académico '
            . 'GRADUADO; los reprobados y pendientes no se consideran graduados.',
        ];

        $datos[] = [
            'Documento generado el ' . now()->format('d/m/Y H:i'),
        ];

        return $datos;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $hoja = $event->sheet->getDelegate();

                $primeraFila = 6;
                $ultimaFila = 5 + $this->filas->count();
                $filaTotal = $ultimaFila + 1;
                $filaNota = $filaTotal + 1;
                $filaMetodologia = $filaTotal + 2;
                $filaFecha = $filaTotal + 3;

                $azulOscuro = '17324F';
                $azul = '2563EB';
                $naranja = 'EA580C';
                $verde = '15803D';
                $gris = '334155';
                $blanco = 'FFFFFF';

                $hoja->mergeCells('A1:K1');
                $hoja->mergeCells('A2:K2');
                $hoja->mergeCells('A3:K3');

                $hoja->mergeCells('A4:A5');
                $hoja->mergeCells('B4:B5');
                $hoja->mergeCells('C4:E4');
                $hoja->mergeCells('F4:H4');
                $hoja->mergeCells('I4:K4');

                foreach ([
                    $filaNota,
                    $filaMetodologia,
                    $filaFecha,
                ] as $fila) {
                    $hoja->mergeCells("A{$fila}:K{$fila}");
                }

                $hoja->getColumnDimension('A')->setWidth(34);
                $hoja->getColumnDimension('B')->setWidth(15);

                foreach (range('C', 'K') as $columna) {
                    $hoja->getColumnDimension($columna)->setWidth(16);
                }

                $hoja->getRowDimension(1)->setRowHeight(37);
                $hoja->getRowDimension(2)->setRowHeight(26);
                $hoja->getRowDimension(3)->setRowHeight(25);
                $hoja->getRowDimension(4)->setRowHeight(32);
                $hoja->getRowDimension(5)->setRowHeight(30);
                $hoja->getRowDimension($filaTotal)->setRowHeight(32);

                $hoja->getStyle('A1:K1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                        'color' => ['rgb' => $blanco],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $azulOscuro],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $hoja->getStyle('A2:K3')->applyFromArray([
                    'font' => [
                        'size' => 11,
                        'color' => ['rgb' => $azulOscuro],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $this->pintar($hoja, 'A4:B5', $gris);
                $this->pintar($hoja, 'C4:E5', $azul);
                $this->pintar($hoja, 'F4:H5', $naranja);
                $this->pintar($hoja, 'I4:K5', $verde);

                $hoja->getStyle('A4:K5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => $blanco],
                        'size' => 10,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                if ($ultimaFila >= $primeraFila) {
                    for ($fila = $primeraFila; $fila <= $ultimaFila; $fila++) {
                        $hoja->getRowDimension($fila)->setRowHeight(26);

                        if ($fila % 2 === 0) {
                            $hoja->getStyle("A{$fila}:K{$fila}")
                                ->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setRGB('EFF4FA');
                        }
                    }

                    $hoja->getStyle("B6:K{$ultimaFila}")
                        ->getNumberFormat()
                        ->setFormatCode('0');
                }

                $hoja->getStyle("A{$filaTotal}:K{$filaTotal}")
                    ->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 11,
                            'color' => ['rgb' => $azulOscuro],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'D9E8FB'],
                        ],
                    ]);

                $hoja->getStyle("B{$filaTotal}:K{$filaTotal}")
                    ->getNumberFormat()
                    ->setFormatCode('0');

                $hoja->getStyle("A4:K{$filaTotal}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()
                    ->setRGB('C7D4E3');

                $hoja->getStyle("B6:K{$filaTotal}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $hoja->getStyle("A6:A{$filaTotal}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $hoja->getStyle("A{$filaNota}:K{$filaFecha}")
                    ->getFont()
                    ->setSize(10)
                    ->getColor()
                    ->setRGB('64748B');

                $hoja->getStyle("A{$filaNota}:K{$filaFecha}")
                    ->getAlignment()
                    ->setWrapText(true);

                $hoja->getRowDimension($filaNota)->setRowHeight(27);
                $hoja->getRowDimension($filaMetodologia)->setRowHeight(35);
                $hoja->getRowDimension($filaFecha)->setRowHeight(23);

                $hoja->freezePane('C6');

                $hoja->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(PageSetup::PAPERSIZE_A3)
                    ->setFitToWidth(1)
                    ->setFitToHeight(1);

                $hoja->getPageSetup()->setFitToPage(true);

                $hoja->getPageMargins()
                    ->setLeft(0.25)
                    ->setRight(0.25)
                    ->setTop(0.4)
                    ->setBottom(0.4);

                $hoja->getPageSetup()->setPrintArea(
                    "A1:K{$filaFecha}"
                );
            },
        ];
    }

    private function pintar($hoja, string $rango, string $color): void
    {
        $hoja->getStyle($rango)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($color);
    }
}
