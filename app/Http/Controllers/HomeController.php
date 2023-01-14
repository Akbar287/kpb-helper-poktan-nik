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
            $poktan = [];
            $kd_poktan = [];

            for($i=0; $i < count($rows[1]); $i++) {
                if(!in_array(number_format($rows[1][$i],0,'.',''), $kd_desa)) {
                    $kd_desa[] = number_format($rows[1][$i], 0,'.','');
                    $row_kd_desa[] = $i;
                }
            }

            for($i=0; $i < count($rows_transpose); $i++) {
                for($j=0; $j < count($kd_desa); $j++) {
                    if(number_format($rows_transpose[$i][1], 0,'.','') == $kd_desa[$j]) {
                        if(!in_array($rows_transpose[$i][5], $poktan)) {
                            $poktan[] = $rows_transpose[$i][5];
                            $kd_poktan[] = $i;
                        }
                    }
                }
            }

            $nik_poktan = [];

            for($i=0; $i < count($kd_poktan); $i++) {
                $nik_poktan[] = $rows[7][$kd_poktan[$i]];
            }

            $temp = [];
                
            for($i=0; $i < count($kd_poktan); $i++) {

                if(isset($kd_poktan[$i + 1])) {
                    for($j = $kd_poktan[$i]; $j < $kd_poktan[$i + 1]; $j++) {
                        $temp[] = $nik_poktan[$i];
                    }
                } else {
                    for($j = $kd_poktan[$i]; $j < count($rows[7]); $j++) {
                        $temp[] = $nik_poktan[$i];
                    }
                }
            }

            $rows[5] = $temp;

            $rows_transpose = [];
            $rows_transpose = $this->transpose($rows);
            
            $final_rows = [];
            for($i=0; $i < count($rows_transpose) + 1; $i++) {
                if($i == 0) {
                    $final_rows[$i] = $header;
                } else {
                    $final_rows[$i] = $rows_transpose[$i - 1];
                }
            }

            $final_to_download = Excel::download(new AlokasiExport($final_rows), $fileName, \Maatwebsite\Excel\Excel::XLS);
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
