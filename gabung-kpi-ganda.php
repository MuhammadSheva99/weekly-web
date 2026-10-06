<?php
// Gabungkan KPI ganda (nama KPI + divisi sama).
//   php gabung-kpi-ganda.php          -> SIMULASI saja, tidak mengubah data
//   php gabung-kpi-ganda.php --apply  -> jalankan penggabungan
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$apply = in_array('--apply', $argv, true);
echo ($apply ? "MODE: PENGGABUNGAN (data diubah)" : "MODE: SIMULASI (data tidak diubah)") . PHP_EOL . PHP_EOL;

// Semua kolom yang menunjuk ke kpi_master.id
$db = Illuminate\Support\Facades\DB::getDatabaseName();
$refs = [];
foreach (Illuminate\Support\Facades\DB::select(
    "SELECT TABLE_NAME AS t, COLUMN_NAME AS c FROM information_schema.KEY_COLUMN_USAGE
     WHERE TABLE_SCHEMA = ? AND REFERENCED_TABLE_SCHEMA = ? AND REFERENCED_TABLE_NAME = 'kpi_master'",
    [$db, $db]
) as $r) {
    $refs[$r->t . '.' . $r->c] = [$r->t, $r->c];
}
foreach ([['target_bulanan', 'kpi_id'], ['kpi_performance', 'kpi_id']] as [$t, $c]) {
    if (Illuminate\Support\Facades\Schema::hasColumn($t, $c)) {
        $refs[$t . '.' . $c] = [$t, $c];
    }
}

$db_ = Illuminate\Support\Facades\DB::class;
$groups = $db_::table('kpi_master')->select('divisi_id', 'nama_kpi')
    ->groupBy('divisi_id', 'nama_kpi')->havingRaw('COUNT(*) > 1')->get();

if ($groups->isEmpty()) {
    echo "Tidak ada KPI ganda." . PHP_EOL;
    exit;
}

$digabung = 0;
foreach ($groups as $g) {
    $divisiNama = $db_::table('divisi')->where('id', $g->divisi_id)->value('nama');
    echo "== {$g->nama_kpi} ({$divisiNama})" . PHP_EOL;

    $rows = $db_::table('kpi_master')
        ->where('divisi_id', $g->divisi_id)->where('nama_kpi', $g->nama_kpi)->get()
        ->map(function ($r) use ($db_) {
            $r->n = $db_::table('target_bulanan')->where('kpi_id', $r->id)->count();
            return $r;
        })->sortBy([['n', 'desc'], ['id', 'asc']])->values();

    $keeper = $rows->first();
    echo "   DIPERTAHANKAN: {$keeper->id} ({$keeper->n} target)" . PHP_EOL;

    foreach ($rows->slice(1) as $dup) {
        echo "   GANDA        : {$dup->id} ({$dup->n} target)" . PHP_EOL;

        $konflik = $db_::table('target_bulanan as a')
            ->join('target_bulanan as b', function ($j) {
                $j->on('a.user_id', '=', 'b.user_id')->on('a.periode', '=', 'b.periode');
            })
            ->where('a.kpi_id', $dup->id)->where('b.kpi_id', $keeper->id)->count();

        if ($konflik > 0) {
            echo "   -> DILEWATI: {$konflik} target bentrok (orang & periode sama sudah ada di KPI yang dipertahankan). Perlu dicek manual." . PHP_EOL;
            continue;
        }

        foreach ($refs as [$t, $c]) {
            $jml = $db_::table($t)->where($c, $dup->id)->count();
            if ($jml > 0) {
                echo "   -> pindahkan {$jml} baris di {$t}.{$c}" . PHP_EOL;
            }
        }

        if ($apply) {
            try {
                $db_::transaction(function () use ($refs, $dup, $keeper, $db_) {
                    foreach ($refs as [$t, $c]) {
                        $db_::table($t)->where($c, $dup->id)->update([$c => $keeper->id]);
                    }
                    $db_::table('kpi_master')->where('id', $dup->id)->delete();
                });
                echo "   -> SELESAI: KPI ganda digabung dan dihapus." . PHP_EOL;
                $digabung++;
            } catch (Throwable $e) {
                echo "   -> GAGAL (tidak ada yang berubah): " . $e->getMessage() . PHP_EOL;
            }
        }
    }
    echo PHP_EOL;
}

echo $apply ? "Total KPI ganda yang digabung: {$digabung}" . PHP_EOL
            : "Ini simulasi. Kalau hasilnya sudah sesuai, jalankan: php gabung-kpi-ganda.php --apply" . PHP_EOL;