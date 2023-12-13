<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\note;
use Illuminate\Support\Facades\Auth;
use DB;

class AjaxController extends Controller
{

	private $id_user;


	public function __construct(){
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
