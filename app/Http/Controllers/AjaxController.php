<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\note;
use App\Models\generale;
use Illuminate\Support\Facades\Auth;
use DB;

class AjaxController extends Controller
{

	private $id_user;


	public function __construct(){
	}

	public function ins_frt(Request $request) {
		
		$nome_frt=$request->input('nome_frt');
		$natoil_frt=$request->input('natoil_frt');
		$codfisc_frt=$request->input('codfisc_frt');
		$tel_frt=$request->input('tel_frt');
		$sesso_frt=$request->input('sesso_frt');
		$sind_frt=$request->input('sind_frt');
		$ente_frt=$request->input('ente_frt');


		$today=date("Y-m-d");
		$operatore=Auth::user()->email;
		
		$info = DB::table('online.db')
		->select('id')
		->where('N_TESSERA','=',$operatore)
		->first();
		
		$id_operatore=0;
		if($info) $id_operatore=$info->id;
		
		
		$frt=new generale;
		$frt->data_update=$today;
		$frt->utente=$operatore;
		$frt->id_oper_user=$id_operatore;
		$frt->id_oper_oper=$id_operatore;
		$frt->nome=$nome_frt;
		$frt->natoil=$natoil_frt;
		$frt->codfisc=$codfisc_frt;
		$frt->telefono=$tel_frt;
		$frt->sesso=$sesso_frt;
		$frt->sindacato=$sind_frt;
		$frt->tb_fo="t4_lazi_a";
		$frt->tb_user="t4_lazi_a";
		$frt->semestre=0;
		$frt->dati_grezzi="Delega FRT da RM_Office";
		$frt->ente_origine=$ente_frt;
		
		
		$frt->save();	
		
		$risp=array();
		$risp['esito']="OK";
		return json_encode($risp);		
	}

	public function save_note(Request $request) {
		$nome_edit=$request->input('nome_edit');
		$datanasc_edit=$request->input('datanasc_edit');
		$ente_edit=$request->input('ente_edit');
		$note_edit=$request->input('note');
		$id_user=Auth::user()->id;
		$note=new note;
		$note->id_user=$id_user;
		$note->nome=$nome_edit;
		$note->datanasc=$datanasc_edit;
		$note->ente=$ente_edit;
		$note->note=$note_edit;
		$note->save();	
		$risp=array();
		$risp['esito']="OK";
		return json_encode($risp);		
	}
	
	public function cerca_fo(Request $request) {
		$value=$request->input('value');
		$risp = DB::table('anagrafe.t4_lazi_a')
		->select('id_anagr','nome',DB::raw("DATE_FORMAT(datanasc,'%d-%m-%Y') as datanasc"),'codfisc','loc','pro','c1')
		->where('nome',"like","%$value%")
		->offset(0)->limit(30)
		->get();		
		return json_encode($risp);		
	}		
}
