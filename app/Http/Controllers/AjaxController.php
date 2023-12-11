<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use DB;

class AjaxController extends Controller
{

	private $id_user;


	public function __construct(){
	}
	
	public function cerca_fo(Request $request) {
		$value=$request->input('value');
		$risp = DB::table('anagrafe.t4_lazi_a')
		->select('nome',DB::raw("DATE_FORMAT(datanasc,'%d-%m-%Y') as datanasc"),'codfisc','loc','pro','c1')
		->where('nome',"like","%$value%")
		->offset(0)->limit(30)
		->get();		
		return json_encode($risp);		
	}		
}
