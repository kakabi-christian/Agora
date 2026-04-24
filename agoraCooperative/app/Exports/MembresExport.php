<?php

namespace App\Exports;

use App\Models\Membre;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MembresExport
{
    /**
     * Générer le fichier Excel de la liste des membres
     */
    public function export()
    {
        $membres = Membre::with('profil')
            ->orderBy('date_inscription', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Titre du document
        $sheet->setCellValue('A1', 'LISTE DES MEMBRES - AGORA COOPÉRATIVE');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Date de génération
        $sheet->setCellValue('A2', 'Généré le : '.now()->format('d/m/Y à H:i'));
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // En-têtes des colonnes
        $headers = [
            'A4' => 'Code Membre',
            'B4' => 'Nom',
            'C4' => 'Prénom',
            'D4' => 'Email',
            'E4' => 'Téléphone',
            'F4' => 'Ville',
            'G4' => 'Rôle',
            'H4' => 'Statut',
            'I4' => 'Date Inscription',
            'J4' => 'Compétences',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style des en-têtes
        $sheet->getStyle('A4:J4')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Données des membres
        $row = 5;
        foreach ($membres as $membre) {
            $sheet->setCellValue('A'.$row, $membre->code_membre);
            $sheet->setCellValue('B'.$row, $membre->nom);
            $sheet->setCellValue('C'.$row, $membre->prenom);
            $sheet->setCellValue('D'.$row, $membre->email);
            $sheet->setCellValue('E'.$row, $membre->telephone ?? 'N/A');
            $sheet->setCellValue('F'.$row, $membre->ville ?? 'N/A');
            $sheet->setCellValue('G'.$row, ucfirst($membre->role));
            $sheet->setCellValue('H'.$row, $membre->est_actif ? 'Actif' : 'Inactif');
            $sheet->setCellValue('I'.$row, $membre->date_inscription ? $membre->date_inscription->format('d/m/Y') : 'N/A');
            $sheet->setCellValue('J'.$row, $membre->profil ? $membre->profil->competences : 'N/A');

            // Style des lignes de données
            $sheet->getStyle('A'.$row.':J'.$row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Couleur alternée pour les lignes
            if ($row % 2 == 0) {
                $sheet->getStyle('A'.$row.':J'.$row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F2F2F2'],
                    ],
                ]);
            }

            $row++;
        }

        // Ajuster la largeur des colonnes
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(30);

        // Statistiques en bas
        $statsRow = $row + 2;
        $sheet->setCellValue('A'.$statsRow, 'STATISTIQUES');
        $sheet->mergeCells('A'.$statsRow.':B'.$statsRow);
        $sheet->getStyle('A'.$statsRow)->getFont()->setBold(true);

        $statsRow++;
        $sheet->setCellValue('A'.$statsRow, 'Total membres :');
        $sheet->setCellValue('B'.$statsRow, $membres->count());

        $statsRow++;
        $sheet->setCellValue('A'.$statsRow, 'Membres actifs :');
        $sheet->setCellValue('B'.$statsRow, $membres->where('est_actif', true)->count());

        $statsRow++;
        $sheet->setCellValue('A'.$statsRow, 'Membres inactifs :');
        $sheet->setCellValue('B'.$statsRow, $membres->where('est_actif', false)->count());

        $statsRow++;
        $sheet->setCellValue('A'.$statsRow, 'Administrateurs :');
        $sheet->setCellValue('B'.$statsRow, $membres->where('role', 'administrateur')->count());

        return $spreadsheet;
    }

    /**
     * Télécharger le fichier Excel
     */
    public function download()
    {
        $spreadsheet = $this->export();

        $writer = new Xlsx($spreadsheet);
        $filename = 'liste-membres-'.now()->format('Y-m-d-His').'.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);

        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
