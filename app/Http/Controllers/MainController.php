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
		//echo "------------------------------------------------------------->pw: ".bcrypt('123456');
		
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
		$ref_ordine=$request->input('ref_ordine');
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
		
		$tabulato = DB::table('anagrafe.'.$tb)
		->where('attivi','=','S')
		->when($view_null=="1", function ($tabulato) use ($campo_ord) {
			return $tabulato->whereRaw("LENGTH($campo_ord) > 0");
		})
		->orderBy($campo_ord,$t_ord)
		->paginate(10)
		->withQueryString();
		
		//per inviare altri parametri in $_GET oltre la paginazione
		$tabulato->appends(['ref_ordine' => $ref_ordine, 'view_null'=>$view_null, 'tipo_ord'=>$tipo_ord]);
		
		return view('all_views/main',compact('tb','tabulato','ref_ordine','view_null','campo_ord','tipo_ord'));
	}


}
