<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inggris;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Client;

class HomeController extends Controller
{
    public function showHome(){
        $suralist = $this->daftarSurah();
        return view('home', compact('suralist'));
    }

    public function cari(Request $request){
        $time_start = microtime(true);
        $query = $request->kata_kunci;
        $kata_kunci = $request->kata_kunci;
        $lower = 30;

        $result = shell_exec('python "D:\Azhari\Telkom\Semester 8\Project TA\konkordansi\public\script\lcskatafrasa.py" ' . base64_encode(json_encode($query)) . ' ' . base64_encode(json_encode($lower)));

        // Decode the result
        $hasil = json_decode($result, true);

        $time_end = microtime(true);
        $time = $time_end - $time_start;

        return view('search-result', compact('hasil', 'kata_kunci', 'time', 'lower'));
    }

    public function applyCari(Request $request, $kata_kunci){
        $time_start = microtime(true);
        $query = $kata_kunci;
        $lower = $request->lower;

        $result = shell_exec('python "D:\Azhari\Telkom\Semester 8\Project TA\konkordansi\public\script\lcskatafrasa.py" ' . base64_encode(json_encode($query)) . ' ' . base64_encode(json_encode($lower)));

        // Decode the result
        $hasil = json_decode($result, true);

        $time_end = microtime(true);
        $time = $time_end - $time_start;

        return view('search-result', compact('hasil', 'kata_kunci', 'time', 'lower'));
    }

    public function daftarSurah(){
        $surah = DB::table('suralist')->select('id','name')->get();
        return $surah;
    }

    public function getAyaFromSura()
    {
        $surah = Input::get('surah');
        $ayat = DB::table('qline')->where('SId',$surah)->get();
        echo "<option disabled selected>Ayat</option>";
        foreach ($ayat as $k){
            echo "<option value=\"".$k->VID."\">".$k->VID."</option>\n";
        }
    }

    public function submitAyat(Request $request){
        $surah = $request->surah;
        $ayat = $request->ayat;
        $ayatList = DB::table('qline')->where('SId',$surah)->get();
        $ayatCount = count($ayatList);

        $hasilAyat = DB::table('qline')
            ->where('SId',$surah)
            ->where('VID', $ayat)
            ->join('en_sahih', function ($join){
                $join->on('en_sahih.sura', '=', 'qline.SId')->on('en_sahih.aya', '=', 'qline.VID');
            })
            ->first();
        $hasilAyatperKata = DB::table('qword')->where('SId',$surah)->where('VId', $ayat)->get();
        $suralist = $this->daftarSurah();

        return view('home', compact('suralist', 'hasilAyat', 'surah', 'ayatCount', 'hasilAyatperKata'));
    }

    public function lemma(Request $request, $id){
//        $hasil = DB::table('qword')
//            ->where('Root',$data->Root)
//            ->join('qline', function ($join){
//                $join->on('qline.SId', '=', 'qword.SId')->on('qline.VID', '=', 'qword.VId');
//            })
//            ->join('en_sahih', function ($join){
//                $join->on('en_sahih.sura', '=', 'qword.SId')->on('en_sahih.aya', '=', 'qword.VId');
//            })
//            ->select('qword.*', 'qline.DataEn', 'qline.XlatAr', 'en_sahih.text')
//            ->get();
        $time_start = microtime(true);
        $data = DB::table('qword')->where('Id',$id)->first();
        $query = $data->Translation;

        $isLower = isset($request->lower);
        if ($isLower === false){
            $lower = 45;
        }
        else{
            $lower = $request->lower;
        }

        //Menjalankan script lcs python lewat cmd
        $result = shell_exec('python "D:\Azhari\Telkom\Semester 8\Project TA\konkordansi\public\script\lcslemma.py" ' . base64_encode(json_encode($query)) . ' ' . base64_encode(json_encode($lower)));

        // Decode the result
        $hasil = json_decode($result, true);
        $time_end = microtime(true);
        $time = $time_end - $time_start;


        return view('search-result-auto', compact('hasil', 'data', 'time', 'lower'));
    }

    public function lcs(){
        $source = 'Dan mereka berkata: "Hendaklah kamu menjadi penganut agama Yahudi atau Nasrani, niscaya kamu mendapat petunjuk". Katakanlah: "Tidak, melainkan (kami mengikuti) agama Ibrahim yang lurus. Dan bukanlah dia (Ibrahim) dari golongan orang musyrik".';
        $query = 'orang-orang musrik';

        //Menjalankan script lcs python lewat cmd
        $result = shell_exec('python "D:\Azhari\Telkom\Semester 8\Project TA\konkordansi\public\script\lcs.py" ' . base64_encode(json_encode($source)) . ' ' . base64_encode(json_encode($query)));

        // Decode the result
        $resultData = json_decode($result, true);

        var_dump($resultData['lcs']);
    }

    public function kataFrasa(Request $request, $id){
        $time_start = microtime(true);
        $data = DB::table('qword')->where('Id',$id)->first();
        $query = $data->Translation;

        $isLower = isset($request->lower);
        if ($isLower === false){
            $lower = 45;
        }
        else{
            $lower = $request->lower;
        }

        //Menjalankan script lcs python lewat cmd
        $result = shell_exec('python "D:\Azhari\Telkom\Semester 8\Project TA\konkordansi\public\script\lcskatafrasa.py" ' . base64_encode(json_encode($query)) . ' ' . base64_encode(json_encode($lower)));

        // Decode the result
        $hasil2 = json_decode($result, true);
        $time_end = microtime(true);
        $time = $time_end - $time_start;

        return view('search-result-auto', compact('hasil2', 'data', 'time', 'lower'));
    }

    public function sinonim($id){
        $data = DB::table('qword')->where('Id',$id)->first();
        $query = Input::get('query');
        $syns = Input::get('syns');

        $hasil3 = DB::table('qword')
            ->where('Translation', 'like', '%' . $query . '%')
            ->join('qline', function ($join){
                $join->on('qline.SId', '=', 'qword.SId')->on('qline.VID', '=', 'qword.VId');
            })
            ->select('qword.*', 'qline.DataEn', 'qline.XlatAr', 'qline.text')
            ->get();

        return view('search-result-auto', compact('hasil3', 'syns', 'data'));
    }

    public function generateSyn($id){
        $data = DB::table('qword')->where('Id',$id)->first();
        $query = $data->Translation;

        $result = shell_exec('python "D:\Azhari\Telkom\Semester 8\Project TA\konkordansi\public\script\lcssyn.py" ' . base64_encode(json_encode($query)));

        $syns = json_decode($result, true);

//        var_dump($syns);
        return view('search-result-auto', compact('syns', 'data'));
    }

    public function getResultSyn(){
        $query = Input::get('query');
        $hasil3 = DB::table('qword')
            ->where('Translation', 'like', '%' . $query . '%')
            ->get();
        echo json_encode($hasil3);
    }

    public function sendArray(){
        $time_start = microtime(true);

        //Menjalankan script lcs python lewat cmd
        $query = 'bringers of good tidings';
        $result = shell_exec('python "D:\Azhari\Telkom\Semester 8\Project TA\konkordansi\public\script\testarray.py" ' . base64_encode(json_encode($query)));

        // Decode the result
        $resultData = json_decode($result, true);
        $time_end = microtime(true);
        $time = $time_end - $time_start;

        var_dump($resultData);
    }
}
