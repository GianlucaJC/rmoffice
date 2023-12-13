<?php
//test
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use DB;

class mainController extends Controller
{

private $id_user;


public function __construct()
	{
		//echo "------------------------------------------------------------->pw: ".bcrypt('damiani');
		
		$this->middleware('auth')->except(['index']);

		$this->middleware(function ($request, $next) {			
			$id=Auth::user()->id;
			$user = User::from('users as u')
			->where('u.id','=',$id)
			->get();
			
			if (isset($user[0])) {
				$this->id_user=$id;
			}
			return $next($request);
		});		
		
	}	


	
	public function main_view(Request $request) {
		$elem_sele=$request->input('elem_sele');
		$per_page=$request->input('per_page');
		if (strlen($per_page)==0) $per_page=500;
		$ref_ordine=$request->input('ref_ordine');


		$filtro_sele=0;
		if (request()->has("filtro_sele")) $filtro_sele=request()->input("filtro_sele");
		if ($filtro_sele=="on") $filtro_sele=1;		

		$solo_contatti=0;
		if (request()->has("solo_contatti")) $solo_contatti=request()->input("solo_contatti");
		if ($solo_contatti=="on") $solo_contatti=1;		

		$view_null=0;
		if (request()->has("view_null")) $view_null=request()->input("view_null");
		if ($view_null=="on") $view_null=1;		
		
		$tipo_ord=0;
		if (request()->has("tipo_ord")) $tipo_ord=request()->input("tipo_ord");
		if ($tipo_ord=="on") $tipo_ord=1;				
		
		$t_ord="ASC";
		if ($tipo_ord==1) $t_ord="DESC";
		
		
		$campo_ord="nome";
		if ($ref_ordine==6) $campo_ord="nome";
		if ($ref_ordine==7) $campo_ord="comunenasc";
		if ($ref_ordine==8) $campo_ord="datanasc";
		if ($ref_ordine==9) $campo_ord="codfisc";
		if ($ref_ordine==10) $campo_ord="loc";
		if ($ref_ordine==11) $campo_ord="pro";
		//if ($ref_ordine==12) $campo_ord="c1";
		if ($ref_ordine==13) $campo_ord="sindacato";
		if ($ref_ordine==14) $campo_ord="denom";
		if ($ref_ordine==15) $campo_ord="ente";
		
		$users=user::select('id','name')->orderBy('name')->get();
		$tb="t4_lazi_a";
		
		if ($solo_contatti=="1")
			$only_contact=$this->only_contact();
		else
			$only_contact=array();
		
		$tabulato = DB::table('anagrafe.'.$tb)
		//->where('attivi','=','S') -->aggiungere condizione semestre
		->orWhereNotNull('c3')
		->when($view_null=="1", function ($tabulato) use ($campo_ord) {
			return $tabulato->whereRaw("LENGTH($campo_ord) > 0");
		})
		->when($solo_contatti=="1", function ($tabulato) use ($only_contact) {
			return $tabulato->whereIn('id_anagr',$only_contact);
		})
		->orderBy('c3','asc')
		->orderBy($campo_ord,$t_ord)
		->paginate($per_page)
		->withQueryString();
		

		//per inviare altri parametri in $_GET oltre la paginazione
		$tabulato->appends(['ref_ordine' => $ref_ordine, 'view_null'=>$view_null, 'tipo_ord'=>$tipo_ord, 'per_page'=>$per_page, 'elem_sele'=>$elem_sele, 'filtro_sele'=>$filtro_sele]);
		$frt=$this->frt($tabulato);
		$note=$this->note($tabulato);
		$user_frt=$this->user_frt();
		return view('all_views/main',compact('tb','tabulato','ref_ordine','view_null','campo_ord','tipo_ord','frt','user_frt','note','per_page','solo_contatti','elem_sele','filtro_sele'));
	}

	public function only_contact() {
		$info = DB::table('rm_office.note as n')
        ->join("anagrafe.t4_lazi_a as t",function($join){
            $join->on("n.nome","=","t.nome")
                ->on("n.datanasc","=","t.datanasc")
				->on("n.ente","=","t.ente");
        })
		->select('t.id_anagr')
		->get();
		$arr=array();
		foreach($info as $a) {
			$arr[]=$a->id_anagr;
		}
		return $arr;
	}

	public function user_frt() {
		$info = DB::table('online.db')->select('n_tessera','utentefillea')->get();
		$user=array();
		foreach($info as $utente) {
			$user[$utente->n_tessera]=$utente->utentefillea;
		}
		return $user;
	}
	public function note($tabulato) {
		$note=array();
		
		foreach ($tabulato as $tab)	{
			$nome=$tab->NOME;
			$datanasc=$tab->DATANASC;
			$ente=$tab->ENTE;
			$id_anagr=$tab->ID_anagr;
			
			$info = DB::table('rm_office.note')
			->select('id_user','note',DB::raw("DATE_FORMAT(updated_at,'%d-%m-%Y') as data"))
			->where('nome','=',$nome)
			->where('datanasc','=',$datanasc)
			->where('ente','=',$ente)
			->orderBy("updated_at","desc")
			->get();
			
			$sca=0;
			
			foreach ($info as $extra)	{
				$note[$id_anagr][$sca]['id_user']=$extra->id_user;
				$note[$id_anagr][$sca]['note']=$extra->note;
				$note[$id_anagr][$sca]['data']=$extra->data;
				$sca++;
			}
		}
		
		return $note;
	}	
	
	public function frt($tabulato) {
		$frt=array();
		foreach ($tabulato as $tab)	{
			$sca=0;
			$nome=$tab->NOME;
			$datanasc=substr($tab->DATANASC,0,10);
			$info = DB::table('frt.generale')
			->select('utente','data_update',DB::raw("DATE_FORMAT(data_update,'%d-%m-%Y') as data_update_it"))
			->where('nome','=',$nome)
			->where('natoil','=',$datanasc)
			->orderBy("data_update","desc")
			->get();
			foreach ($info as $extra)	{
				$frt[$tab->ID_anagr][$sca]['utente']=strtoupper($extra->utente);
				$frt[$tab->ID_anagr][$sca]['data_update']=$extra->data_update_it;
				$sca++;
			}
			
			
		}
		return $frt;
	}
}