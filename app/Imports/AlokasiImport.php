<?php

namespace App\Imports;

use App\Models\Alokasi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AlokasiImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        return Alokasi::all();
    }

    public function model(array $row)
    {
        return new Alokasi([
            'Nama Penyuluh' => $row[0],
            'Kode Desa' => $row[1],
            'Kode Kios Pengecer' => $row[2],
            'Nama Kios Pengecer' => $row[3],
            'Gapoktan' => $row[4],
            'Nama Poktan' => $row[5],
            'Nama Petani' => $row[6],
            'KTP' => $row[7],
            'Tempat Lahir' => $row[8],
            'Tanggal Lahir' => $row[9],
            'Nama Ibu Kandung' => $row[10],
            'Alamat' => $row[11],
            'Subsektor' => $row[12],
            'Komoditas MT1' => $row[13],
            'Luas Lahan (Ha) MT1' => $row[14],
            'Pupuk Urea (Kg) MT1' => $row[15],
            'Pupuk NPK (Kg) MT1' => $row[16],
            'Pupuk NPK Formula (Kg) MT1' => $row[17],
            'Komoditas MT2' => $row[18],
            'Luas Lahan (Ha) MT2' => $row[19],
            'Pupuk Urea (Kg) MT2' => $row[20],
            'Pupuk NPK (Kg) MT2' => $row[21],
            'Pupuk NPK Formula (Kg) MT2' => $row[22],
            'Komoditas MT3' => $row[23],
            'Luas Lahan (Ha) MT3' => $row[24],
            'Pupuk Urea (Kg) MT3' => $row[25],
            'Pupuk NPK (Kg) MT3' => $row[26],
            'Pupuk NPK Formula (Kg) MT3' => $row[27],
        ]);
    }
}
