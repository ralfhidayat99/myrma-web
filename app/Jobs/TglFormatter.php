<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TglFormatter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }

    function hari_format($d)
    {
        $date = substr($d, 0, 10);
        $h = date('D', strtotime($date));

        switch ($h) {
            case 'Sun':
                $hari_ini = "Minggu";
                break;

            case 'Mon':
                $hari_ini = "Senin";
                break;

            case 'Tue':
                $hari_ini = "Selasa";
                break;

            case 'Wed':
                $hari_ini = "Rabu";
                break;

            case 'Thu':
                $hari_ini = "Kamis";
                break;

            case 'Fri':
                $hari_ini = "Jum'at";
                break;

            case 'Sat':
                $hari_ini = "Sabtu";
                break;

            default:
                $hari_ini = "Tidak di ketahui";
                break;
        }

        return  $hari_ini . ", ";
    }

    function tgl_format($tanggal)
    {
        $tomorow = date('Y-m-d', strtotime('+1 day'));
        $bulan = array(
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $tgl = substr($tanggal, 0, 10);
        // $waktu = substr($tanggal, 11, 5);
        $exp = explode('-', $tgl);

        if ($tgl == $tomorow) {
            return "Besok";
        } else if ($tgl == date('Y-m-d')) {
            return "Hari Ini";
        } else {
            return $this->hari_format($tanggal) . $exp[2] . ' ' . $bulan[(int)$exp[1]] . ' ';
        }
    }
}
