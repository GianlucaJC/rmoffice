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
		
		/*
		echo "F0030:".bcrypt('202310SC')."<hr>";
		echo "F0418:".bcrypt('456890XX')."<hr>";
		echo "F0573:".bcrypt('160369DA')."<hr>";
		echo "F0579".bcrypt('30387')."<hr>";
		echo "F0830".bcrypt('53315')."<hr>";
		echo "F0838".bcrypt('44720')."<hr>";
		echo "F0910".bcrypt('77653')."<hr>";
		echo "F3114".bcrypt('567890LA')."<hr>";
		echo "F3398".bcrypt('RM987654')."<hr>";
		echo "F0147 	Bagalà Genni".bcrypt('BA970484')."<hr>";
		echo "F0336 	Liotino Marco".bcrypt('LI192021')."<hr>";
		echo "F0482 	BURATTI ANDREA".bcrypt('BA135792')."<hr>";
		echo "F0829 	Proietti Bruno".bcrypt('140623')."<hr>";
		echo "F0830 	De Vecchis Alioscia".bcrypt('53315')."<hr>";
		echo "F0831 	TESTI GIULIO".bcrypt('87602')."<hr>";
		echo "F0832 	Nicoletti Antonio".bcrypt('47564')."<hr>";
		echo "F0834 	Orfiz Luis Manuel".bcrypt('46245')."<hr>";
		echo "F0840 	Nika Agim".bcrypt('03701')."<hr>";
		echo "F0841 	Ferrari fabio".bcrypt('AB123456')."<hr>";
		echo "F0908 	Wibabara Eric".bcrypt('64702')."<hr>";
		echo "F0909 	Broccatelli Claudio".bcrypt('47087')."<hr>";
		echo "F0911 	Enache Maricel".bcrypt('48407')."<hr>";
		echo "F0919 	Paudice Mauro".bcrypt('51273')."<hr>";
		echo "F3388 	ANDREONI MARCO".bcrypt('AM130477')."<hr>";
		echo "F3485 	SERJANAJ XHESILDO".bcrypt('JJ121996')."<hr>";
		echo "F0425 	DI MARCO SIMONE".bcrypt('NI121721')."<hr>";
		*/
		$this->middleware('auth')->except(['index']);
		
		$this->middleware(function ($request, $next) {			
			$id=Auth::user()->id;
			$user = User::from('users as u')
			->select("id","email")
			->where('u.id','=',$id)
			->get();
			$userid="?";
			
			if (isset($user[0])) {
				$this->id_user=$id;
				$userid=$user[0]->email;
			}
			
			
			$t_att=DB::table('online.db')
			->select("id")
			->where('N_TESSERA','=',$userid)
			->where('attiva','=',1)
			->count();
		
		if ($t_att==0) {
	
				echo "<br>";
				echo "<center>";
				echo "<h2>Utente non autorizzato!</h2>";
				echo "</center>";
				exit;
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
		$ril_ce=$this->rilasci_ente('C');
		$ril_ec=$this->rilasci_ente('A');
		
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
		$denom_speed=$request->input('denom_speed');
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

		$solo_miei_contatti=0;
		if (request()->has("solo_miei_contatti")) $solo_miei_contatti=request()->input("solo_miei_contatti");
		if ($solo_miei_contatti=="on") $solo_miei_contatti=1;	

		$incroci=0;
		if (request()->has("incroci")) $incroci=request()->input("incroci");
		if ($incroci=="on") $incroci=1;	


		$solo_frt=0;
		if (request()->has("solo_frt")) $solo_frt=request()->input("solo_frt");
	

		$solo_fillea=0;
		if (request()->has("solo_fillea")) $solo_fillea=request()->input("solo_fillea");
		if ($solo_fillea=="on") $solo_fillea=1;	

		$solo_non_contatti=0;
		if (request()->has("solo_non_contatti")) $solo_non_contatti=request()->input("solo_non_contatti");
		if ($solo_non_contatti=="on") $solo_non_contatti=1;		

		$solo_servizi=0;
		if (request()->has("solo_servizi")) $solo_servizi=request()->input("solo_servizi");
		if ($solo_servizi=="on") $solo_servizi=1;

		$view_null=0;
		if (request()->has("view_null")) $view_null=request()->input("view_null");
		if ($view_null=="on") $view_null=1;		

		$ente_altrove=0;
		if (request()->has("ente_altrove")) $ente_altrove=request()->input("ente_altrove");
		if ($ente_altrove=="on") $ente_altrove=1;	

		
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
		
		$campo_ord="rm.$campo_ord";

		$tb="t4_lazi_a";
		
		if ($solo_miei_contatti==1)
			$only_my_contact=$this->only_contact(1);
		else
			$only_my_contact=array();


		if ($solo_contatti==1)
			$only_contact=$this->only_contact(0);
		else
			$only_contact=array();
		
		if ($solo_non_contatti==1)
			$only_no_contact=$this->only_contact(0);
		else
			$only_no_contact=array();

		$only_select=explode(";",$elem_sele);
		
		$only_contact=array_filter($only_contact);
		$only_no_contact=array_filter($only_no_contact);
		$only_select=array_filter($only_select);
		if (count($only_contact)==0) $solo_contatti=0;
		if (count($only_my_contact)==0) $solo_miei_contatti=0;
		if (count($only_no_contact)==0) $solo_non_contatti=0;
		if (count($only_select)==0) $filtro_sele=0;
		
		

		$cerca_nome="";$cerca_speed=0;$cerca_denom="";
		if (request()->has("nome_speed") || request()->has("denom_speed")) {
			$cerca_speed=1;
			if (request()->has("cerca_nome")) 
				$cerca_nome=request()->input("cerca_nome");
			if (request()->has("denom_speed")) 
				 $cerca_denom=request()->input("cerca_denom");
		
			$cPage=1;
			Paginator::currentPageResolver(function () use ($cPage) {
				return $cPage;
			});
					
		}
		
		$cond="1";$filtro_p=0;
		
		if ($solo_contatti==0 && $solo_miei_contatti==0 && $solo_non_contatti==0 && $filtro_sele==0 && $cerca_speed==0 && $solo_frt==0 && $solo_fillea==0) {
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
						$cond.="(substr(`rm`.sind_mens$sind_cur,$periodo_tab,1)<>'*' and length(`rm`.sind_mens$sind_cur)>0) ";
					}	
					$entr=true;
				}
				if ($entr==true) $cond.=") ";
			}
		}

		$filtro_base=true;
		if ($solo_contatti==1 || $solo_miei_contatti==1 ||  $filtro_sele==1 || $cerca_speed==1) {
			$filtro_base=false;
		}

		if ($filtro_base==true) {
			
			$arr_z=explode(";",$zona);
			if (count($arr_z)==1 && $arr_z[0]!="all") {
				if (strlen($zona)!=0) $cond.=" and (`rm`.`zona` in ('".implode(",",$arr_z)."')) ";
			}
			

			$arr_s=explode(";",$filtro_sind);
			if (strlen($filtro_sind)!=0) {
				if (count($arr_s)>0 && $arr_s[0]!="all") {
					$cond.=" and (";
					for ($sca=0;$sca<=count($arr_s)-1;$sca++) {
						$sin=$arr_s[$sca];
						if ($sca>0) $cond.=" or ";
						if ($sin=="ns")
							$cond.=" `rm`.sindacato=' ' or `rm`.sindacato='' ";
						else	
							$cond.=" `rm`.sindacato='$sin' ";
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
				$cond.=" and (`rm`.ente='$filtro_ente') ";
			}
			if ($filtro_tel=="1") 
				$cond.=" and 
						((`rm`.c1 is not null and length(`rm`.c1)<>0) or
						(`rm`.tel_ce is not null and length(`rm`.tel_ce)<>0) or
						(`rm`.tel_sin is not null and length(`rm`.tel_sin)<>0) or
						(`rm`.tel_gps is not null and length(`rm`.tel_gps)<>0) or
						(`rm`.tel_altro is not null and length(`rm`.tel_altro)<>0)
						)";

			if ($filtro_tel=="0") 
				$cond.=" and (not
						((`rm`.c1 is not null and length(`rm`.c1)<>0) or
						(`rm`.tel_ce is not null and length(`rm`.tel_ce)<>0) or
						(`rm`.tel_sin is not null and length(`rm`.tel_sin)<>0) or
						(`rm`.tel_gps is not null and length(`rm`.tel_gps)<>0) or
						(`rm`.tel_altro is not null and length(`rm`.tel_altro)<>0)
						))";

			if ($filtro_giac=="1")
				$cond.=" and (length(`rm`.giacenza)>0 and `rm`.giacenza is not null and `rm`.giacenza<>'0') ";
			if ($filtro_giac=="0")
				$cond.=" and (`rm`.giacenza is null or length(`rm`.giacenza)=0 or `rm`.giacenza='0') ";

			if ($filtro_iban=="1")
				$cond.=" and (length(`rm`.iban)>0 and `rm`.iban is not null) ";
			if ($filtro_iban=="0")
				$cond.=" and (`rm`.iban is null or length(`rm`.iban)=0) ";
				
				
		}
		

		if ($filtro_base==true && $filtro_p==0) $cond.=" and (`rm`.c3 is not null) ";
		
		if ($solo_contatti==1 && $filtro_sele==0 && $cerca_speed==0) 
			$cond.=" and (`rm`.`id_anagr` in (".implode(",",$only_contact).")) ";
		
		if ($solo_miei_contatti==1 && $filtro_sele==0 && $cerca_speed==0) 
			$cond.=" and (`rm`.`id_anagr` in (".implode(",",$only_my_contact).")) ";

		if ($solo_non_contatti==1 && $filtro_sele==0 && $cerca_speed==0) 
			$cond.=" and (`rm`.`id_anagr` not in (".implode(",",$only_no_contact).")) ";

		if ($filtro_sele==1 && $cerca_speed==0) 
			$cond.=" and (`rm`.`id_anagr` in (".implode(",",$only_select).")) ";
		
		if ($cerca_speed==1 && strlen($cerca_nome)!=0) $cond.=" and (`rm`.`id_anagr`=$cerca_nome) ";
		if ($cerca_speed==1 && strlen($cerca_denom)!=0) $cond.=" and (`rm`.`denom`='$cerca_denom') ";
		
		
		if ($view_null=="1") $cond.=" and (LENGTH($campo_ord) > 0) ";
		
		if ($solo_frt=="2") $cond.=" and frt.tb_user='t4_lazi_a' ";
		if ($solo_frt=="3") $cond.=" and frt.tb_user<>'t4_lazi_a' ";
		
		if ($solo_servizi=="1") $cond.=" and (`rm`.fisco=1 or `rm`.inca=1) ";
		
		if ($incroci=="1") $cond.=" and n.IDARC<>'t4_lazi_a' ";
		if (strlen($filtro_ente)!=0 && $filtro_ente!="all" && $ente_altrove=="1") $cond.=" and rm.ente<>rm1.ente ";
		
		$tabulato = DB::table('anagrafe.'.$tb.' as rm')
		->select('rm.*')
		->when($incroci=="1", function($tabulato) {
			return $tabulato->join('anagrafe.nazionale as n','n.codfisc','rm.codfisc');
		})
		->when(strlen($filtro_ente)!=0 && $filtro_ente!="all" && $ente_altrove=="1", function($tabulato) {
			return $tabulato->join('anagrafe.t4_lazi_a as rm1','rm.codfisc','rm1.codfisc');
		})
		->when($solo_frt>0, function($tabulato){
			return $tabulato->join('frt.generale as frt','frt.codfisc','rm.codfisc');
		})
		->whereRaw($cond)
		->when($filtro_p=="0", function ($tabulato) {			
			return $tabulato->orderBy("rm.c3",'asc');
		})
		->orderBy($campo_ord,$t_ord)
		->groupBy('rm.ID_anagr');
		
		/* rawQuery
		$rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $tabulato->toSql()), $tabulato->getBindings());
		echo $rawSql;
		*/
				
		
		$tabulato=$tabulato->paginate($per_page)->withQueryString();
		$num_rec=$tabulato->total();

		
		//per inviare altri parametri in $_GET oltre la paginazione
		$tabulato->appends(['ref_ordine' => $ref_ordine, 'view_null'=>$view_null, 'tipo_ord'=>$tipo_ord, 'per_page'=>$per_page, 'elem_sele'=>$elem_sele, 'filtro_sele'=>$filtro_sele, 'rilasci'=>$rilasci, 'zona'=>$zona, 'filtro_base'=>$filtro_base,'filtro_sind'=>$filtro_sind, 'filtro_ente'=>$filtro_ente,'filtro_tel'=>$filtro_tel,'filtro_giac'=>$filtro_giac,'filtro_iban'=>$filtro_iban,'solo_contatti'=>$solo_contatti,'solo_miei_contatti'=>$solo_miei_contatti,'solo_frt'=>$solo_frt,'solo_non_contatti'=>$solo_non_contatti,'solo_fillea'=>$solo_fillea,'solo_servizi'=>$solo_servizi,'incroci'=>$incroci,'ente_altrove'=>$ente_altrove]);
		
		
		$frt=$this->frt($tabulato);
		$note=$this->note($tabulato);
		$fgo=$this->info_fgo($tabulato);
		$zone=$this->zone();
		$iscr_altrove=$this->iscr_altrove($tabulato);
		$iscr_enti=$this->iscr_enti($tabulato);
		$iscr_altri_rilasci=$this->iscr_altri_rilasci($tabulato,$rilasci);
		$disdette=$this->disdette($tabulato);

		$user_frt=$this->user_frt();
		$passaggi=$this->passaggi;
		//$altrove_fillea=$this->altrove_fillea();
		
		$info_count_notif = DB::table('alert_new_ass')
		->select('id_azienda')->count();

		$aziende_alert=array();
		if ($info_count_notif>0) {
			$aziende_alert = DB::table('alert_new_ass as a')
			->join('anagrafe.t4_lazi_a as t','a.id_azienda','t.c2')
			->select('a.id_azienda','t.denom')
			->groupBy('a.id_azienda')
			->get();
		}
		
		
		
		$utenti=$this->utenti("all");
		$id_user=$this->id_user;
		return view('all_views/main',compact('tb','tabulato','ref_ordine','view_null','campo_ord','tipo_ord','frt','user_frt','note','per_page','solo_contatti','solo_miei_contatti','solo_frt','solo_non_contatti','solo_fillea','solo_servizi','elem_sele','filtro_sele','cerca_nome','cerca_denom','utenti','fgo','passaggi','rilasci','zona','zone','filtro_base','filtro_sind','filtro_ente','filtro_tel','filtro_giac','filtro_iban','iscr_altrove','ril_ce','ril_ec','iscr_enti','iscr_altri_rilasci','num_rec','disdette','info_count_notif','aziende_alert','incroci','id_user','ente_altrove'));
	}


	public function disdette($tabulato) {
		$resp=array();
		foreach ($tabulato as $tab)	{
			$sindacato=$tab->SINDACATO;
			if ($sindacato=="1") continue;
			$id_ref=$tab->ID_anagr;
			$trovato=false;
			for ($s=5;$s>=1;$s--) {
				$sind="SIND_MENS$s";
				$sind_mens=$tab->$sind;
				if (strlen($sind_mens>0)) {
					for ($sca=11;$sca>=0;$sca--) {
						$sindac=substr($sind_mens,$sca,1);
						if ($sindac=="1") {
							$trovato=true;
							break;
						}
					}
				}
				if ($trovato==true) break;
			}
			if ($trovato==true) $resp[]=$id_ref;
		}			
		return $resp;
	}
	
	public function iscr_altri_rilasci($tabulato,$rilasci) {
		$arr_r=explode(";",$rilasci);$condx="";
		for ($sca=0;$sca<=count($arr_r)-1;$sca++) {
			if ($arr_r[$sca]=="all") continue;
			$sind_cur=substr($arr_r[$sca],7,1);
			$periodo_tab=intval(substr($arr_r[$sca],0,2));
			if ($sca>0 && strlen($condx)>0) $condx.=" or ";
			if (strlen($sind_cur)>0) {
				$condx.="(substr(sind_mens$sind_cur,$periodo_tab,1)<>'*' and length(sind_mens$sind_cur)>0) ";
			}				
		}
		$resp=array();
		if (strlen($condx)==0) return $resp;

		
		foreach ($tabulato as $tab)	{
			$id_ref=$tab->ID_anagr;
			$nome=$tab->NOME;
			$datanasc=$tab->DATANASC;
			$ente=$tab->ENTE;
			$sindacato=$tab->SINDACATO;
			$cond=" not ($condx)";
			
			$info = DB::table('anagrafe.t4_lazi_a')
			->select('sindacato','ente')
			->where('nome','=',$nome)
			->where('datanasc','=',$datanasc)
			->whereRaw($cond);
			$info_count=$info->count();
			if ($info_count>0) {
				$info_dati=$info->first();
				$resp[$id_ref]['ente']=$info_dati->ente;
				$resp[$id_ref]['sindacato']=$info_dati->sindacato;
			}
		}
		return $resp;			
	}
	
	public function iscr_enti($tabulato) {
		$resp=array();
		foreach ($tabulato as $tab)	{
			$id_ref=$tab->ID_anagr;
			$nome=$tab->NOME;
			$datanasc=$tab->DATANASC;
			$ente=$tab->ENTE;
			$sindacato=$tab->SINDACATO;
			
			$info = DB::table('anagrafe.t4_lazi_a')
			->select('sindacato','ente')
			->where('nome','=',$nome)
			->where('datanasc','=',$datanasc)
			->where('ente','<>',$ente);
			
			$info_count=$info->count();
			if ($info_count>0) {
				$info_dati=$info->first();
				$resp[$id_ref]['ente']=$info_dati->ente;
				$resp[$id_ref]['sindacato']=$info_dati->sindacato;
			}
		}
		return $resp;
	}	
	
	public function iscr_altrove($tabulato) {
		$resp=array();
		foreach ($tabulato as $tab)	{
			$id_ref=$tab->ID_anagr;
			$nome=$tab->NOME;
			$datanasc=$tab->DATANASC;
			$info = DB::table('anagrafe_regioni.globale')->select('sindacato','provincia','attivi')
			->where('nome','=',$nome)
			->where('datanasc','=',$datanasc)
			->where('IDARC','<>','t4_lazi_a')
			->get();
			
			$sind="";$sca=0;
			foreach($info as $lav) {
				if ($sca==1) $sind.=";";
				$sca=1;
				$sind.=$lav->sindacato."|";
				$sind.=$lav->provincia."|";
				$sind.=$lav->attivi."|";
			}		
			if (strlen($sind)>0) $resp[$id_ref]=$sind;
		}
		return $resp;
	}
	
	public function altrove_fillea() {
		$resp=array();
		$info = DB::table('anagrafe.t4_lazi_a as t')
		->join('anagrafe.nazionale as n','n.codfisc','t.codfisc')
		->select('t.ID_anagr')
		->where('n.IDARC','<>','t4_lazi_a')
		->where('t.c3','=','2') //solo fillea altrove nuovi assunti
		->get();
		return $resp;
	}


	public function only_contact($from) {
		$id_user=$this->id_user;
		$info = DB::table('rm_office.note as n')
        ->join("anagrafe.t4_lazi_a as t",function($join){
            $join->on("n.nome","=","t.nome")
                ->on("n.datanasc","=","t.datanasc")
				->on("n.ente","=","t.ente");
        })
		->select('t.id_anagr')
		->when($from!="0", function($info) use ($id_user){
			return $info->where('n.id_user','=',$id_user);
		})
		->get();
		$arr=array();
		foreach($info as $a) {
			$arr[]=$a->id_anagr;
		}
		return $arr;
	}
	

	public function user_frt() {
		$info = DB::table('online.db')
			->select('db.n_tessera','db.id_prov_associate','db.utentefillea','p.provincia','p.sigla_pr')
			->join('bdf.province as p','db.id_prov_associate','p.id')
			->get();
		$user=array();
		foreach($info as $utente) {
			$user[$utente->n_tessera]['utentefillea']=$utente->utentefillea;
			$user[$utente->n_tessera]['id_prov_associate']=$utente->id_prov_associate;
			$user[$utente->n_tessera]['provincia']=$utente->provincia;
			$user[$utente->n_tessera]['sigla_pr']=$utente->sigla_pr;
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
			->select('id','id_user','note','stato_nota',DB::raw("DATE_FORMAT(updated_at,'%d-%m-%Y') as data"))
			->where('nome','=',$nome)
			->where('datanasc','=',$datanasc)
			->where('ente','=',$ente)
			->where('dele','=',0)
			->orderBy("updated_at","desc")
			->get();
			
			$sca=0;
			
			foreach ($info as $extra)	{
				$note[$id_anagr][$sca]['id_nota']=$extra->id;
				$note[$id_anagr][$sca]['id_user']=$extra->id_user;
				$note[$id_anagr][$sca]['note']=$extra->note;
				$note[$id_anagr][$sca]['stato_nota']=$extra->stato_nota;
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
			$codfisc=$tab->CODFISC;
			//$nome=$tab->NOME;
			//$datanasc=substr($tab->DATANASC,0,10);
			$info = DB::table('frt.generale')
			->select('utente','tb_user','data_update',DB::raw("DATE_FORMAT(data_update,'%d-%m-%Y') as data_update_it"))
			->where('codfisc','=',$codfisc)
			->orderBy("data_update","desc")
			->get();
			$al=0;$entr=0;
			foreach ($info as $extra)	{				
				if (strtolower($extra->tb_user)!="t4_lazi_a") $al=1;
				$frt[$tab->ID_anagr][$sca]['utente']=strtoupper($extra->utente);
				$frt[$tab->ID_anagr][$sca]['data_update']=$extra->data_update_it;
				$frt[$tab->ID_anagr][$sca]['codfisc']=$codfisc;
				$sca++;
			}
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
	
	public function rilasci_ente($id_ente) {
		$arr_per=array();
		$periodi=DB::table('online.fo_argo')
		->select('rilasci_tabulato')
		->where('id_arch','=',"t4_lazi_a")
		->where('code_CE','=',$id_ente)
		->get();
		foreach($periodi as $periodo) {
			$per=$periodo->rilasci_tabulato;
			$arr=explode(";",$per);
			for ($sca=0;$sca<=count($arr)-1;$sca++) {
				$per=$arr[$sca];
				$per=str_replace("/","-",$per);
				$per=substr($per,0,7);
				$arr_per[]=$per;
			}
		}
		return $arr_per;
	}
	
}
