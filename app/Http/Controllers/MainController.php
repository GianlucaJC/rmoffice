<?php
//test
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use DB;

class mainController extends Controller
{

private $id_user;
private $passaggi;

public function __construct()
	{
		
		$passaggi=$this->calc_sto_tab();
		$this->passaggi=$passaggi;
		//echo "------------------------------------------------------------->pw: ".bcrypt('120011');
		
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

	public function utenti($tipo_richiesta) {
		$users = User::from('users as u')->get();
		$utenti=array();
		foreach($users as $user) {
			$utenti[$user->id]['name']=$user->name;
			$utenti[$user->id]['tessera']=$user->email;
		}
		return $utenti;
	}
	
	public function main_view(Request $request) {
		$rilasci="";
		if (request()->has("rilasci")) {
			$ril=$request->input('rilasci');
			if (is_array($ril)) $rilasci=implode(";",$ril);
			else $rilasci=$ril;
		}	
		
		
		$zona="";
		if (request()->has("zona")) {
			$zx=$request->input('zona');
			if (is_array($zx)) $zona=implode(";",$zx);
			else $zona=$zx;
		}	
		$filtro_sind="";
		if (request()->has("filtro_sind")) {
			$zx=$request->input('filtro_sind');
			if (is_array($zx)) $filtro_sind=implode(";",$zx);
			else $filtro_sind=$zx;
		}

		$filtro_ente=$request->input('filtro_ente');
		$filtro_tel=$request->input('filtro_tel');
		$filtro_giac=$request->input('filtro_giac');
		$filtro_iban=$request->input('filtro_iban');
		$nome_speed=$request->input('nome_speed');
		$elem_sele=$request->input('elem_sele');
		$per_page=$request->input('per_page');
		if (strlen($per_page)==0) $per_page=50;
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
		
		$tipo_ord="";
		$tipo_ord=request()->input("tipo_ord");
		$t_ord="ASC";
		if ($tipo_ord=="on") $t_ord="DESC";
		
		
		$campo_ord="nome";
		if ($ref_ordine==3) $campo_ord="iban";
		if ($ref_ordine==4) $campo_ord="giacenza";
		if ($ref_ordine==5) $campo_ord="codice";
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
		if ($ref_ordine==16) $campo_ord="zona";

		$tb="t4_lazi_a";
		
		if ($solo_contatti==1)
			$only_contact=$this->only_contact();
		else
			$only_contact=array();
		$only_select=explode(";",$elem_sele);
		
		$only_contact=array_filter($only_contact);
		$only_select=array_filter($only_select);
		if (count($only_contact)==0) $solo_contatti=0;
		if (count($only_select)==0) $filtro_sele=0;
		

		$cerca_nome="";$cerca_speed=0;
		if (request()->has("nome_speed")) {
			$cerca_speed=1;
			$cerca_nome=request()->input("cerca_nome");
		
			$cPage=1;
			Paginator::currentPageResolver(function () use ($cPage) {
				return $cPage;
			});
					
		}
		
		$cond="1";$filtro_p=0;
		
		if ($solo_contatti==0 && $filtro_sele==0 && $cerca_speed==0) {
			if (strlen($rilasci)!=0) {
				$entr=false;
				$arr_r=explode(";",$rilasci);
				for ($sca=0;$sca<=count($arr_r)-1;$sca++) {
					if ($arr_r[$sca]=="all") continue;
					if ($entr==false) {
						$cond.=" and (";
					}	
					$sind_cur=substr($arr_r[$sca],7,1);
					$periodo_tab=intval(substr($arr_r[$sca],0,2));
					if ($sca>0 && $entr==true) $cond.=" or ";
					if (strlen($sind_cur)>0) {
						$filtro_p=1;
						$cond.="(substr(sind_mens$sind_cur,$periodo_tab,1)<>'*' and length(sind_mens$sind_cur)>0) ";
					}	
					$entr=true;
				}
				if ($entr==true) $cond.=") ";
			}
		}

		$filtro_base=true;
		if ($solo_contatti==1 || $filtro_sele==1 || $cerca_speed==1) {
			$filtro_base=false;
		}

		if ($filtro_base==true) {
			
			$arr_z=explode(";",$zona);
			if (count($arr_z)==1 && $arr_z[0]!="all") {
				if (strlen($zona)!=0) $cond.=" and (`zona` in ('".implode(",",$arr_z)."')) ";
			}
			

			$arr_s=explode(";",$filtro_sind);
			if (strlen($filtro_sind)!=0) {
				if (count($arr_s)>0 && $arr_s[0]!="all") {
					$cond.=" and (";
					for ($sca=0;$sca<=count($arr_s)-1;$sca++) {
						$sin=$arr_s[$sca];
						if ($sca>0) $cond.=" or ";
						if ($sin=="ns")
							$cond.=" sindacato=' ' or sindacato='' ";
						else	
							$cond.=" sindacato='$sin' ";
					}
					$cond.=") ";
				}
			}
			
			
			
			/*
			if (strlen($filtro_sind)!=0 && $filtro_sind!="all") {
				if ($filtro_sind=="ns") 
					$cond.=" and (length(sindacato)=0 or sindacato=' ') ";
				else
					$cond.=" and (sindacato='$filtro_sind') ";
			}
			*/			


			if (strlen($filtro_ente)!=0 && $filtro_ente!="all") {
				$cond.=" and (ente='$filtro_ente') ";
			}
			if ($filtro_tel=="1") 
				$cond.=" and 
						((c1 is not null and length(c1)<>0) or
						(tel_ce is not null and length(tel_ce)<>0) or
						(tel_sin is not null and length(tel_sin)<>0) or
						(tel_gps is not null and length(tel_gps)<>0) or
						(tel_altro is not null and length(tel_altro)<>0)
						)";

			if ($filtro_tel=="0") 
				$cond.=" and (not
						((c1 is not null and length(c1)<>0) or
						(tel_ce is not null and length(tel_ce)<>0) or
						(tel_sin is not null and length(tel_sin)<>0) or
						(tel_gps is not null and length(tel_gps)<>0) or
						(tel_altro is not null and length(tel_altro)<>0)
						))";

			if ($filtro_giac=="1")
				$cond.=" and (length(giacenza)>0 and giacenza is not null) ";
			if ($filtro_giac=="0")
				$cond.=" and (giacenza is null or length(giacenza)=0) ";

			if ($filtro_iban=="1")
				$cond.=" and (length(iban)>0 and iban is not null) ";
			if ($filtro_iban=="0")
				$cond.=" and (iban is null or length(iban)=0) ";
				
				
		}
		
		if ($filtro_base==true && $filtro_p==0) $cond.=" and (c3 is not null) ";
		
		if ($solo_contatti==1 && $filtro_sele==0 && $cerca_speed==0) 
			$cond.=" and (`id_anagr` in (".implode(",",$only_contact).")) ";
		if ($filtro_sele==1 && $cerca_speed==0) 
			$cond.=" and (`id_anagr` in (".implode(",",$only_select).")) ";
		
		if ($cerca_speed==1) $cond.=" and (`id_anagr`=$cerca_nome) ";
		
		
		if ($view_null=="1") $cond.=" and (LENGTH($campo_ord) > 0) ";
	

		$tabulato = DB::table('anagrafe.'.$tb)
		->whereRaw($cond)
		->orderBy('c3','asc')
		->orderBy($campo_ord,$t_ord)
		->paginate($per_page)
		->withQueryString();		

		//per inviare altri parametri in $_GET oltre la paginazione
		$tabulato->appends(['ref_ordine' => $ref_ordine, 'view_null'=>$view_null, 'tipo_ord'=>$tipo_ord, 'per_page'=>$per_page, 'elem_sele'=>$elem_sele, 'filtro_sele'=>$filtro_sele, 'rilasci'=>$rilasci, 'zona'=>$zona, 'filtro_base'=>$filtro_base,'filtro_sind'=>$filtro_sind, 'filtro_ente'=>$filtro_ente,'filtro_tel'=>$filtro_tel,'filtro_giac'=>$filtro_giac,'filtro_iban'=>$filtro_iban,'solo_contatti'=>$solo_contatti]);
		
		
		$frt=$this->frt($tabulato);
		$note=$this->note($tabulato);
		$fgo=$this->info_fgo($tabulato);
		$zone=$this->zone();
		
		$user_frt=$this->user_frt();
		$passaggi=$this->passaggi;
		
		$utenti=$this->utenti("all");
		return view('all_views/main',compact('tb','tabulato','ref_ordine','view_null','campo_ord','tipo_ord','frt','user_frt','note','per_page','solo_contatti','elem_sele','filtro_sele','cerca_nome','utenti','fgo','passaggi','rilasci','zona','zone','filtro_base','filtro_sind','filtro_ente','filtro_tel','filtro_giac','filtro_iban'));
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
		$frt=array();$altrove=array();
		
		foreach ($tabulato as $tab)	{
			$sca=0;
			$nome=$tab->NOME;
			$datanasc=substr($tab->DATANASC,0,10);
			$info = DB::table('frt.generale')
			->select('utente','tb_user','data_update',DB::raw("DATE_FORMAT(data_update,'%d-%m-%Y') as data_update_it"))
			->where('nome','=',$nome)
			->where('natoil','=',$datanasc)
			->orderBy("data_update","desc")
			->get();
			$rm=0;$entr=0;
			foreach ($info as $extra)	{				
				$entr=1;
				if (strtolower($extra->tb_user)=="t4_lazi_a") $rm=1;
				$frt[$tab->ID_anagr][$sca]['utente']=strtoupper($extra->utente);
				$frt[$tab->ID_anagr][$sca]['data_update']=$extra->data_update_it;
				$sca++;
			}
			$al=0;
			if ($rm==0 && $entr==1) $al=1;
			$altrove[$tab->ID_anagr]=$al;

		}
		$resp=array();
		$resp['dati']=$frt;
		$resp['altrove']=$altrove;

		
		return $resp;
	}
	
	public function zone() {
		$info = DB::table('anagrafe.t4_lazi_a as t')
		->select('t.zona')
		->groupBy('t.zona')
		->get();
		$zone=array();
		$zone[]="all";
		foreach($info as $z) {
			$zone[]=$z->zona;
		}
		return $zone;		
	}
	
	public function info_fgo($tabulato) {
		$fgo=array();
		
		foreach ($tabulato as $tab)	{
			$sca=0;
			$id_azienda=$tab->C2;
				
			//function ereditata da nuovi assunti
			if (strlen($id_azienda)==0) continue;
			$azienda=addslashes($id_azienda);
			/*
			$presenza=DB::statement("SELECT count(S.id) q FROM `filleago`.`aziende_segnalazioni` A_S
			INNER JOIN `filleago`.`segnalazioni` S ON A_S.id_segnalazione=S.id
			WHERE A_S.id_azienda='$id_azienda' and (fine_lavori is null or fine_lavori>=CURDATE()) and A_S.tb_fo is not null  
			GROUP BY S.id;");
			*/

			
			$presenza=DB::table('filleago.aziende_segnalazioni as A_S')
			->join("filleago.segnalazioni as S","A_S.id_segnalazione","S.id")
			->select("S.id")
			->where('A_S.id_azienda','=',$id_azienda)
			->where(function ($count) {
				$count->whereRaw("S.fine_lavori is null")
				->orWhereRaw("S.fine_lavori>=curdate()");
			})
			->count();


			
			//controllo esistenza azienda in archivio aziende (distinte) di FGO
			
			$count=DB::table('filleago.aziende')
			->where('p_iva','=',$id_azienda)
			->orWhere('cod_fisc','=',$id_azienda)
			->count();
			if ($count==0) $presenza=0;

			if ($presenza!=0) $fgo[$tab->ID_anagr]=$presenza;
		}	
		return $fgo;
		
	}
	
	public function calc_sto_tab() {
		$info=DB::table('report.infotab')
		->select('storia_tab')
		->where('tb','=',"t4_lazi_a")
		->get();
		if (isset($info)) {
			$passaggi=$info[0]->storia_tab;
			$passaggi=str_replace("/","-",$passaggi);
		} else  return "";
		
		$storia_new=array();						
		if (strlen($passaggi)!=0)  {
			$fl_entr=1;
			$storia=explode(";", $passaggi);		
			for($i = 0; $i < count($storia)-1; $i++){					
			  $p_sel=$storia[$i];
			  $trasf=substr($p_sel,3,4).substr($p_sel,0,2);
			  $storia_new["$trasf"] = "$p_sel";
			}								
			krsort($storia_new);
			
			$passaggi="all"; // ricostruisco la stringa passaggi da array ordinato
			foreach($storia_new as $tab){
				$passaggi.=";";
				$passaggi.="$tab";
			}			
		}		
		$arr=explode(";",$passaggi);
		return $arr;
	}
	
}
