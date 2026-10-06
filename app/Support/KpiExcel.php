<?php

namespace App\Support;

use Carbon\Carbon;
use ZipArchive;

/**
 * Membuat file .xlsx tabel KPI satu orang tanpa library tambahan (hanya ext-zip).
 * Dipakai tombol "Download Excel" di halaman detail KPI (HRD & Atasan).
 */
class KpiExcel
{
    private const BULAN = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    public static function namaFile($d, Carbon $periode): string
    {
        $nama = preg_replace('/[^A-Za-z0-9]+/', '_', (string) ($d->user->nama ?? 'karyawan'));

        return 'KPI_'.trim($nama, '_').'_'.$periode->format('Y-m').'.xlsx';
    }

    /** Tulis file ke folder sementara, kembalikan path-nya. */
    public static function buat($d, Carbon $periode): string
    {
        $path = tempnam(sys_get_temp_dir(), 'kpi');
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="KPI" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>');
        $zip->addFromString('xl/styles.xml', self::styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', self::sheet($d, $periode));
        $zip->close();

        return $path;
    }

    // Indeks style: 0 biasa, 1 judul, 2 label tebal, 3 header tabel, 4 teks sel, 5 angka sel,
    // 6 teks rata kanan, 7 total tebal (teks), 8 total tebal (angka), 9 teks tengah
    private static function styles(): string
    {
        $border = '<border><left style="thin"><color rgb="FFBBBBBB"/></left><right style="thin"><color rgb="FFBBBBBB"/></right>'
            .'<top style="thin"><color rgb="FFBBBBBB"/></top><bottom style="thin"><color rgb="FFBBBBBB"/></bottom><diagonal/></border>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<numFmts count="1"><numFmt numFmtId="164" formatCode="0.00"/></numFmts>'
            .'<fonts count="3"><font><sz val="11"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="11"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="14"/><name val="Calibri"/></font></fonts>'
            .'<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFE5E7EB"/><bgColor indexed="64"/></patternFill></fill></fills>'
            .'<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border>'.$border.'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="10">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
            .'<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="top"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="top"/></xf>'
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right"/></xf>'
            .'<xf numFmtId="164" fontId="1" fillId="2" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top"/></xf>'
            .'</cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>';
    }

    private static function sheet($d, Carbon $periode): string
    {
        $rows = [];
        $r = 1;
        $add = function (array $cells, ?int $height = null) use (&$rows, &$r) {
            $rows[] = ['r' => $r, 'cells' => $cells, 'h' => $height];
            $r++;
        };

        $add([['A', 'KPI '.($d->user->nama ?? '-'), 1]]);
        $add([['B', 'Divisi', 2], ['C', $d->user->divisi->nama ?? '-', 0]]);
        $add([['B', 'Periode', 2], ['C', self::BULAN[$periode->month].' '.$periode->year, 0]]);
        $add([['B', 'Skor Total', 2], ['C', $d->skor_total !== null ? rtrim(rtrim(number_format((float) $d->skor_total, 2, ',', '.'), '0'), ',') : '-', 0]]);
        $add([['B', 'Status', 2], ['C', ($d->status ?? '-').($d->grade ? ' (Grade '.$d->grade.')' : ''), 0]]);
        $add([]);

        $heads = ['No.', 'Key Result Area', 'Indikator', 'Pola', 'Bobot (%)', 'Target', 'Realisasi', 'Pencapaian (%)', 'Skor', 'Grade'];
        $add(array_map(fn ($h, $i) => [chr(65 + $i), $h, 3], $heads, array_keys($heads)), 30);

        $kraSebelumnya = null;
        foreach ($d->rows as $i => $x) {
            $kra = $x->kpi->key_result_area;
            $add([
                ['A', $i + 1, 9],
                ['B', $kra !== $kraSebelumnya ? (string) $kra : '', 4],
                ['C', (string) $x->kpi->nama_kpi.($x->kpi->target_fleksibel ? ' (Fleksibel)' : ''), 4],
                ['D', ucfirst(strtolower((string) $x->kpi->pola)), 9],
                ['E', (float) $x->bobot, 5],
                ['F', (string) KpiFormat::tampil($x->target, $x->kpi->satuan), 6],
                ['G', $x->realisasi !== null ? (string) KpiFormat::tampil($x->realisasi, $x->kpi->satuan) : 'Belum diisi', 6],
                ['H', $x->pencapaian !== null ? (float) $x->pencapaian : '-', $x->pencapaian !== null ? 5 : 6],
                ['I', $x->kontribusi !== null ? (float) $x->kontribusi : '-', $x->kontribusi !== null ? 5 : 6],
                ['J', $x->grade ?? '-', 9],
            ]);
            $kraSebelumnya = $kra;
        }

        if ($d->rows->isNotEmpty()) {
            $add([
                ['A', '', 7], ['B', '', 7], ['C', '', 7], ['D', 'Total', 7],
                ['E', (float) $d->bobot_total, 8],
                ['F', '', 7], ['G', '', 7], ['H', '', 7],
                ['I', $d->skor_total !== null ? (float) $d->skor_total : '-', $d->skor_total !== null ? 8 : 7],
                ['J', $d->grade ?? '-', 7],
            ]);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>'
            .'<cols><col min="1" max="1" width="6" customWidth="1"/><col min="2" max="2" width="26" customWidth="1"/>'
            .'<col min="3" max="3" width="42" customWidth="1"/><col min="4" max="4" width="12" customWidth="1"/>'
            .'<col min="5" max="5" width="11" customWidth="1"/><col min="6" max="7" width="18" customWidth="1"/>'
            .'<col min="8" max="8" width="15" customWidth="1"/><col min="9" max="9" width="10" customWidth="1"/>'
            .'<col min="10" max="10" width="9" customWidth="1"/></cols><sheetData>';

        foreach ($rows as $row) {
            $xml .= '<row r="'.$row['r'].'"'.($row['h'] ? ' ht="'.$row['h'].'" customHeight="1"' : '').'>';
            foreach ($row['cells'] as [$col, $val, $style]) {
                $ref = $col.$row['r'];
                if (is_int($val) || is_float($val)) {
                    $xml .= '<c r="'.$ref.'" s="'.$style.'"><v>'.$val.'</v></c>';
                } else {
                    $xml .= '<c r="'.$ref.'" s="'.$style.'" t="inlineStr"><is><t xml:space="preserve">'.self::esc((string) $val).'</t></is></c>';
                }
            }
            $xml .= '</row>';
        }

        return $xml.'</sheetData><pageSetup orientation="landscape" fitToWidth="1" fitToHeight="0"/></worksheet>';
    }

    private static function esc(string $s): string
    {
        $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $s);

        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}