<?php

namespace App\Http\Controllers;

use App\pesertakegiatan;
use DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class PesertaKegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\pesertaKegiatan  $pesertaKegiatan
     * @return \Illuminate\Http\Response
     */
    public function show(pesertaKegiatan $pesertaKegiatan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\pesertaKegiatan  $pesertaKegiatan
     * @return \Illuminate\Http\Response
     */
    public function edit(pesertaKegiatan $pesertaKegiatan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\pesertaKegiatan  $pesertaKegiatan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, pesertaKegiatan $pesertaKegiatan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\pesertaKegiatan  $pesertaKegiatan
     * @return \Illuminate\Http\Response
     */
    public function destroy(pesertaKegiatan $pesertaKegiatan)
    {
        //
    }

    public function showpeserta($id){
        $users=DB::table('peserta_kegiatans')
         ->join('users','users.email','=','peserta_kegiatans.email_user')
         ->select('peserta_kegiatans.*','users.namaDepan as nama')
         ->Distinct()
         ->where('peserta_kegiatans.idKegiatan','=',$id)
         ->get();
        
        $detil=DB::table('kegiatans')
            ->select('kegiatans.id','kegiatans.mulai','kegiatans.selesai','kegiatans.budget','kegiatans.leader','kegiatans.nama','kegiatans.public','kegiatans.doneguided')
            ->where('kegiatans.id','=',$id)
            ->get();

        $pesertas = DB::table('kegiatans')
            ->join('peserta_kegiatans','peserta_kegiatans.idKegiatan','=','kegiatans.id')
            ->join('users','users.email','=','peserta_kegiatans.email_user')
            ->select(DB::raw('count(peserta_kegiatans.email_user) as jumlah,kegiatans.id as id'))
            ->where('kegiatans.id','=',$id)
            ->where('peserta_kegiatans.applyAsGuide','=',0)
            ->groupBy('kegiatans.id')
            ->get();
        
        $guides = DB::table('kegiatans')
            ->join('peserta_kegiatans','peserta_kegiatans.idKegiatan','=','kegiatans.id')
            ->join('users','users.email','=','peserta_kegiatans.email_user')    
            ->select(DB::raw('count(peserta_kegiatans.email_user) as jumlah,kegiatans.id as id'))
            ->where('kegiatans.id','=',$id)
            ->where('peserta_kegiatans.applyAsGuide','=',1)
            ->groupBy('kegiatans.id')
            ->get();
        
        $lokasis = DB::table('bertempats')
            ->join('lokasis','bertempats.idLokasi','=','lokasis.id')
            ->select('lokasis.nama as nama','lokasis.id as id')
            ->where('bertempats.idKegiatan','=',$id)
            ->get();

        $pesertas2=DB::table('peserta_kegiatans')
            ->select('email_user')
            ->where('idKegiatan','=',$id)
            ->get();


         if (Auth::check()){
            if (Auth::user()->email==$detil[0]->leader){
                return view('post.postapplicant',compact('users','detil','pesertas','guides','lokasis'));
            }
            else{
                $logedin=Auth::user()->email;
                foreach ($pesertas2 as $ygikut) {
                    if ($ygikut->email_user == $logedin){
                        return view('post.postpubapplicant',compact('users','detil','pesertas','guides','lokasis','logedin'));             
                    }   
                }
                $logedin='no';
                return view('post.postpubapplicant',compact('users','detil','pesertas','guides','lokasis','logedin'));       
            }
         }
          $logedin='no';
        return view('post.postpubapplicant',compact('users','detil','pesertas','guides','lokasis','logedin'));
         
    }

    public function daftarturis($id,$username){
        $data=DB::table('peserta_kegiatans')
        ->join('kegiatans','kegiatans.id','=','peserta_kegiatans.idKegiatan')
        ->select('peserta_kegiatans.idUser','kegiatans.leader')
        ->Distinct()
        ->where('idKegiatan','=',$id)
        ->get();

        foreach ($data as $holder) {
            if ($holder->idUser==$username or $holder->leader == $username){
                return redirect('/post/'.$id);
            }
        }

        $pesertakegiatan = new pesertakegiatan();
        $pesertakegiatan->idUser=$username;
        $pesertakegiatan->idKegiatan=$id;
        $pesertakegiatan->isVerified=0;
        $pesertakegiatan->applyAsGuide=1;
        $pesertakegiatan->save();

        return redirect('/post/'.$id);  

    }

    public function daftarguide($id,$username){ //username = id user
        $data=DB::table('peserta_kegiatans')
        ->join('kegiatans','kegiatans.id','=','peserta_kegiatans.idKegiatan')
        ->select('peserta_kegiatans.idUser','kegiatans.leader')
        ->Distinct()
        ->where('idKegiatan','=',$id)
        ->get();

        foreach ($data as $holder) {
            if ($holder->idUser==$username or $holder->leader == $username){
                return redirect('/post/'.$id);
            }
        }

        $pesertakegiatan = new pesertakegiatan();
        $pesertakegiatan->idUser=$username;
        $pesertakegiatan->idKegiatan=$id;
        $pesertakegiatan->isVerified=0;
        $pesertakegiatan->applyAsGuide=1;
        $pesertakegiatan->save();

        return redirect('/post/'.$id);        
    }

    public function batalikut($id,$user){
        $delete= pesertakegiatan::where('idUser','=',$user)->where('idKegiatan','=',$id)->delete();

        return redirect('/post/'.$id);
    }

    public function editpeserta($id,$option,$user){
        if ($option=='acc'){
            //accept
            pesertakegiatan::where('idUser','=',$user)->where('idKegiatan','=',$id)->update(['isVerified'=>1]);
        }
        else if($option=='wa'){
            //delete
          $delete= pesertakegiatan::where('idUser','=',$user)->where('idKegiatan','=',$id)->delete();
        }
        else if($option=='kick'){
            //pindah status
            pesertakegiatan::where('idUser','=',$user)->where('idKegiatan','=',$id)->update(['isVerified'=>0]);
        }
        return redirect('/post/user/'.$id); 
    }
}
