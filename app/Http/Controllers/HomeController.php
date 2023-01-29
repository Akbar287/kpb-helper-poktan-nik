<?php

namespace App\Http\Controllers;

use App\Exports\AlokasiExport;
use Illuminate\Http\Request;
use App\Imports\AlokasiImport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
// use Maatwebsite\Excel\Excel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $rows = new Collection([]);
        
        return view('home', compact('rows'));
    }

    public function store (Request $request) {
        if($request->has('data')) {
            
            $path1 = 'temp/' . $request->file('data')->hashName();
            $name = (explode('.', $path1));

            if(count($name) == 1) {
                $name[] = 'xls';
            }

            $name = (implode('.', $name));
            $request->file('data')->storeAs('temp', $name);
            $path=storage_path('app').'/temp/'.$name;

            $fileName = ($request->file('data')->getClientOriginalName());
            $rows = Excel::toArray(new AlokasiImport, $path);

            $header = $rows[0][0];
            unset($rows[0][0]);
            $rows_transpose = array_merge($rows[0]);
            $rows = $this->transpose($rows_transpose);

            $kd_desa = [];
            $row_kd_desa = [];
            $kd_poktan = [];
            $global_scope = [];

            for($i=0; $i < count($rows[1]); $i++) {
                if(!in_array(number_format($rows[1][$i],0,'.',''), $kd_desa)) {
                    $kd_desa[] = number_format($rows[1][$i], 0,'.','');
                    $row_kd_desa[] = $i;
                }
            }

            $temp_poktan = [];
            $nik_poktan = [];
            
            for($i = 0;$i < count($kd_desa); $i++) {
                $temp_poktan = [];
                $nik_poktan = [];
                
                for ($j=0; $j < count($rows_transpose); $j++) {
                    if(number_format($rows_transpose[$j][1], 0,'.','') == $kd_desa[$i]) {
                        if(!in_array($rows_transpose[$j][5], $temp_poktan)) {
                            $temp_poktan[] = $rows_transpose[$j][5];
                            $nik_poktan[] = $rows_transpose[$j][7];
                            $kd_poktan[] = $j;
                        }
                    }
                }

                $global_scope[] = [
                    "kd_desa" => $kd_desa[$i],
                    "kd_poktan" => $temp_poktan,
                    "nik_poktan" => $nik_poktan
                ];
            }
            unset($temp_poktan);
            unset($nik_poktan);

            $temp_slice =[];
            for($i=0; $i < count($global_scope); $i++) {
                for($j = 0; $j < count($global_scope[$i]["kd_poktan"]); $j++) {
                    $temp_slice[] = [
                        "kd_desa" => $global_scope[$i]["kd_desa"],
                        "kd_poktan" => $global_scope[$i]["kd_poktan"][$j],
                        "nik_poktan" => $global_scope[$i]["nik_poktan"][$j],
                    ];
                }
            }

            unset($global_scope);
            

            // Perlu di revisi
            $nama_poktan = [];
            for($i=0; $i < count($rows_transpose); $i++) {
                for ($j = 0; $j < count($temp_slice); $j++) {
                    if(number_format($rows_transpose[$i][1], 0,'.','') == $temp_slice[$j]["kd_desa"] && $rows_transpose[$i][5] == $temp_slice[$j]["kd_poktan"]) {
                        $rows_transpose[$i][28] = $rows_transpose[$i][5];
                        $rows_transpose[$i][5] = $temp_slice[$j]["nik_poktan"];
                    }
                }
            }
            
            unset($temp_slice);

            $header[5] = "NIK Poktan";
            $header[28] = "Nama Poktan";

            //Tambah Header
            array_unshift($rows_transpose, $header);

            $final_to_download = Excel::download(new AlokasiExport($rows_transpose), $fileName, \Maatwebsite\Excel\Excel::XLS);
            Storage::delete('temp/' . $name);
            return $final_to_download;
        }
    }

    function transpose($array_one) {
        $array_two = [];
        foreach ($array_one as $key => $item) {
            foreach ($item as $subkey => $subitem) {
                $array_two[$subkey][$key] = $subitem;
            }
        }
        return $array_two;
    }
}
