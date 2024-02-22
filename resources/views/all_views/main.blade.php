@extends('all_views.viewmaster.index')

@section('title', 'RM_Office')

@section('extra_style') 
<!-- x button export -->

  <!-- Select2 -->
  <link rel="stylesheet" href="{{ URL::asset('/') }}plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="{{ URL::asset('/') }}plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">


<link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet">
<!-- -->
@endsection



<style>
.filtri {
  border: 2px outset blue;
  padding:10px;
}
.active_th {
	border: 1px outset blue;
}
</style>

@section('notifiche')
	@if ($info_count_notif>0)
	  <!-- Messages Dropdown Menu -->
	  <li class="nav-item dropdown">
		<a class="nav-link" data-toggle="dropdown" href="#">
		  <i class="far fa-comments"></i>
		  <span class="badge badge-danger navbar-badge">{{$info_count_notif}}</span>
		</a>
		<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
		  
		<?php $num_not=0; ?>
		@foreach($aziende_alert as $azienda_alert) 
		  <?php
			$num_not++; 
			//if ($num_not>10) break;
		  ?>

		   <a href='javascript:void(0)' class="dropdown-item" onclick="$('#a_all').val('{{$azienda_alert->denom}}');$( '#a_all' ).trigger( 'paste' );">

			<!-- Message Start -->
			<div class="media">
			  
			  <div class="media-body">
				<h3 class="dropdown-item-title">
					<i class="fas fa-info-circle"></i>  {{$azienda_alert->denom}}
				</h3>
			  </div>
			</div>
			<!-- Message End -->
		  </a>
		  <div class="dropdown-divider"></div>
		@endforeach  
		
		  <!--
		  <a href="#" class="dropdown-item dropdown-footer">Vedi tutte le aziende interessate</a>
		  !-->
	  
		</div>
	  </li>
	@endif
	
@endsection

@section('content_main')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style='background-color:white'>
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">


        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">

      <div class="container-fluid">

		<form method='post' action="" id='frm_tab' name='frm_tab' autocomplete="off">
		<input name="_token" type="hidden" value="{{ csrf_token() }}" id='token_csrf'>	  

		<div id='div_intest' style='display:none'>
			<input type='hidden' name='ref_ordine' id='ref_ordine' value='{{$ref_ordine}}'>
			<input type="hidden" value="{{url('/')}}" id="url" name="url">
			<input type='hidden' name='cerca_nome' id='cerca_nome'>
			<input type='hidden' name='cerca_denom' id='cerca_denom'>
			
			<input type='hidden' name='elem_sele' id='elem_sele' value='{{$elem_sele}}'>
			<nav class="navbar navbar-expand-lg navbar-light bg-light">
			
			<button onclick="show_extra()" id="btn_extra" type="button" class="btn btn-outline-primary btn-sm">Nascondi extra</button>
			
			
			<button onclick="location.href='main_view'" type="button" class="btn btn-outline-success btn-sm ml-3">Azzera filtri</button>
						
			  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			  </button>



			  <div class="collapse navbar-collapse" id="navbarText">
				<ul class="navbar-nav mr-auto">
				  <li class="nav-item">
					<a class="nav-link" href="#">Tabulati</a>
				  </li>

					<select class="form-select select2" name='rilasci[]' id='rilasci' data-placeholder="Periodi di rilascio dei tabulati" multiple style='width:400px' onchange="$('#frm_tab').submit()" >
				

					<?php
						for ($sca=0;$sca<=count($passaggi)-1;$sca++) {
							$id_per=$passaggi[$sca];
							echo "<option value=$id_per ";
							if (strlen($rilasci!=0)) {
								$arr_r=explode(";",$rilasci);
								if (in_array($id_per,$arr_r)) echo " selected ";
							}
							
							if ($id_per=="all") {
								if (strlen($rilasci)==0) echo " selected ";
								echo ">Nuovi assunti mediante storicità</option>";
							}
							elseif ($id_per=="tab") {
								echo ">Nuovi assunti da confronto tabulato";
								echo "</option>";
							}
							else {
								$vx=substr($id_per,0,7);
								$view_per=$vx;
								if (in_array($vx,$ril_ce)) $view_per.=" (CE)";
								if (in_array($vx,$ril_ec)) $view_per.=" (EC)";
								echo ">".$view_per."</option>";
							}
						}
					?>
					</select>
					<li>
					<li class="nav-item">
						<a class="nav-link" href="#">Zone</a>
					</li>					
	
					<select class="form-select select2" name='zona[]' id='zona' data-placeholder="Filtro zona" multiple style='width:200px' onchange="$('#frm_tab').submit()" >
				
					
					<?php
						for ($sca=0;$sca<=count($zone)-1;$sca++) {
							$zx=$zone[$sca];
							if (strlen($zx)==0) continue;
							echo "<option value='$zx' ";
							if (strlen($zona!=0)) {
								$arr_z=explode(";",$zona);
								if (in_array($zx,$arr_z)) echo " selected ";
							}		
							if (strlen($zona)==0 && $zx=="all") echo " selected ";

							if ($zx=="all") $zx="[Tutte]";
							echo ">$zx</option>";
						}
					?>
					</select>
					
					<?php
						$sindacati=explode(";",$filtro_sind);
					?>
					<li class="nav-item">
						<a class="nav-link" href="#">Sindacato</a>
					</li>						
					<select class="form-select select2" name='filtro_sind[]' id='filtro_sind' data-placeholder="Filtro sindacato" onchange="$('#frm_tab').submit()" style='width:200px' multiple>
						<option value='all'
						@if(in_array("all",$sindacati) || strlen($filtro_sind)==0) selected @endif
						>[Tutti]</option>
						<option value='0'
						@if(in_array("0",$sindacati)) selected @endif
						>Liberi</option>
						<option value='1'
						@if(in_array("1",$sindacati)) selected @endif
						>Fillea CGIL</option>
						<option value='2'
						@if(in_array("2",$sindacati)) selected @endif
						>Filca CISL</option>
						<option value='3'
						@if(in_array("3",$sindacati)) selected @endif
						>Feneal UIL</option>
						<option value='ns'
						@if(in_array("ns",$sindacati)) selected @endif
						>Non Specificati</option>							
						
					</select>
					


				<?php
					$check="";
					if ($incroci=="1") $check="checked";
				?>	
				<div class="col-lg-2 ml-4">
					<div class="form-check form-switch">
					  <input class="form-check-input" type="checkbox" id="incroci" name="incroci" onchange="$('#frm_tab').submit()" {{$check}}>
					  <label class="form-check-label" for="incroci">Incroci 1A</label>
					</div>
				</div>	

				<?php
					$check="";
					if ($solo_servizi=="1") $check="checked";
					
				?>		
				<div class="col-lg-2 ml-2">
					<div class="form-check form-switch">
					  <input class="form-check-input" type="checkbox" id="solo_servizi" name="solo_servizi" onchange="$('#frm_tab').submit()" {{$check}}>
					  <label class="form-check-label" for="solo_servizi">Solo con servizi</label>
					</div>
				</div>				
				
					

				</ul>
				
				<span class="navbar-text">
					Nominativi per pagina 
				</span>				
				
				<select class="form-select form-select-xs ml-2" aria-label=".form-select-lg" style='width:200px' name='per_page' id='per_page' onchange="$('#frm_tab').submit()">
					
					<option value='20'
					@if ($per_page=='20') selected @endif
					>20</option>
					<option value='50'
					@if ($per_page=='50') selected @endif
					>50</option>
					<option value='100'
					@if ($per_page=='100') selected @endif
					>100</option>
					<option value='200'
					@if ($per_page=='200') selected @endif
					>200</option>
					<option value='500' 
					@if ($per_page=='500') selected @endif
					>500</option>
					<option value='1000'
					@if ($per_page=='1000') selected @endif
					>1000</option>
					<option value='2000'
					@if ($per_page=='2000') selected @endif
					>2000</option>
					<option value='3000'
					@if ($per_page=='3000') selected @endif
					>3000</option>					

				</select>
			  </div>
			  

			</nav>
			<div class='row mb-1 mt-1'>
				<div class="col-md-2"> 
					<div class="form-floating mb-3 mb-md-0">
						
						<select class="form-select" name='filtro_ente' id='filtro_ente' data-placeholder="Filtro ente" onchange="$('#frm_tab').submit()" >
							<option value='all'
							@if(strlen($filtro_ente)==0 || $filtro_ente=="all") selected @endif
							>[Tutti]</option>
							<option value='C'
							@if($filtro_ente=="C") selected @endif
							>CassaEdile</option>
							<option value='A'
							@if($filtro_ente=="A") selected @endif
							>Edilcassa</option>
						</select>
						<label for="filtro_ente">Ente</label>
					</div>
			  </div>
			  

				<?php
					$check="";
					if ($ente_altrove=="1") $check="checked";
					
				?>		
				<div class="col-lg-2 ml-4">
					<div class="form-check form-switch">
					  <input class="form-check-input" type="checkbox" id="ente_altrove" name="ente_altrove" onchange="$('#frm_tab').submit()" {{$check}}>
					  <label class="form-check-label" for="ente_altrove">Filtro ente altrove</label>
					</div>
				</div>	
			  

				<div class="col-md-2">
					<div class="form-floating mb-3 mb-md-0">
						
						<select class="form-select" name='filtro_tel' id='filtro_tel' data-placeholder="Filtro telefoni" onchange="$('#frm_tab').submit()" >
							<option value='all'
							@if(strlen($filtro_tel)==0 || $filtro_tel=="all") selected @endif
							>[Tutti]</option>
							<option value='0'
							@if($filtro_tel=="0") selected @endif
							>Senza telefoni</option>
							<option value='1'
							@if($filtro_tel=="1") selected @endif
							>Con telefoni (mostrati solo i validi)</option>
						</select>
						<label for="filtro_tel">Telefoni</label>
					</div>
			  </div>	
			  
				<div class="col-md-2">
					<div class="form-floating mb-3 mb-md-0">
						
						<select class="form-select" name='filtro_giac' id='filtro_giac' data-placeholder="Filtro giacenza" onchange="$('#frm_tab').submit()" >
							<option value='all'
							@if(strlen($filtro_giac)==0 || $filtro_giac=="all") selected @endif
							>[Tutti]</option>
							<option value='0'
							@if($filtro_giac=="0") selected @endif
							>Senza giacenza</option>
							<option value='1'
							@if($filtro_giac=="1") selected @endif
							>Con giacenza</option>
						</select>
						<label for="filtro_giac">Giacenza</label>
					</div>
			  </div>	

				<div class="col-md-3">
					<div class="form-floating mb-3 mb-md-0">
						
						<select class="form-select" name='filtro_iban' id='filtro_iban' data-placeholder="Filtro IBAN" onchange="$('#frm_tab').submit()" >
							<option value='all'
							@if(strlen($filtro_iban)==0 || $filtro_iban=="all") selected @endif
							>[Tutti]</option>
							<option value='0'
							@if($filtro_iban=="0") selected @endif
							>Senza IBAN</option>
							<option value='1'
							@if($filtro_iban=="1") selected @endif
							>Con IBAN</option>
						</select>
						<label for="filtro_iban">IBAN</label>
					</div>
			  </div>				  
			  
			  
			</div>  
			
			<div class='filtri mb-3' id='div_filtri'>
				<div class="alert alert-info p-1" role="alert">
				  <small>Filtri selettivi (Questi filtri disattivano gli altri filtri impostati)</small>
				</div>
				<div class="row">
					<?php
						$check="";
						if ($solo_contatti=="1") $check="checked";
						
					?>	
					<div class="col-lg-2 ml-4">
						<div class="form-check form-switch">
						  <input class="form-check-input" type="checkbox" id="solo_contatti" name="solo_contatti" onchange="$('#frm_tab').submit()" {{$check}}>
						  <label class="form-check-label" for="solo_contatti">Solo contattati</label>
						</div>
					</div>
					
					<?php
						$check="";
						if ($solo_miei_contatti=="1") $check="checked";
						
					?>	
					<div class="col-lg-2">
						<div class="form-check form-switch">
						  <input class="form-check-input" type="checkbox" id="solo_miei_contatti" name="solo_miei_contatti" onchange="$('#frm_tab').submit()" {{$check}}>
						  <label class="form-check-label" for="solo_miei_contatti">Solo miei contattati</label>
						</div>
					</div>
					

				
					<?php
						$check="";
						if ($solo_non_contatti=="1") $check="checked";
						
					?>						
					<div class="col-lg-2">
						<div class="form-check form-switch">
						  <input class="form-check-input" type="checkbox" id="solo_non_contatti" name="solo_non_contatti" onchange="$('#frm_tab').submit()" {{$check}}>
						  <label class="form-check-label" for="solo_non_contatti">Solo non contattati</label>
						</div>
					</div>					



					<?php
						$check="";
						if ($solo_fillea=="1") $check="checked";
						
					?>						
					<!--
					<div class="col-lg-2">
						<div class="form-check form-switch">
						  <input class="form-check-input" type="checkbox" id="solo_fillea" name="solo_fillea" onchange="$('#frm_tab').submit()" {{$check}}>
						  <label class="form-check-label" for="solo_fillea">Fillea altrove</label>
						</div>
					</div>
					!-->


					<div class="col-md-2">
					  <div class="form-floating mb-3 mb-md-0">
						<select class="form-select" id="solo_frt" aria-label="solo_frt" name='solo_frt' onchange="$('#frm_tab').submit()">
							<option value=0>Nessuna selezione</option>
							<option value=1
							@if ($solo_frt=="1") selected @endif
							>Solo FRT</option>
							<option value=2 
							@if ($solo_frt=="2") selected @endif
							>Solo FRT RM</option>
							<option value=3 
							@if ($solo_frt=="3") selected @endif
							>Solo FRT altrove</option>
						</select>
						<label for="solo_frt">Opzioni FRT</label>
						</div>
					</div>					
					
				</div>

				
				<div class="row">

					<?php
						$check="";
						if ($filtro_sele=="1") $check="checked";
					?>					
					<div class="col-lg-2 ml-4">
						<div class="form-check form-switch">
						  <input class="form-check-input" type="checkbox" id="filtro_sele" name="filtro_sele" onchange="$('#elem_sele').val(localStorage.elem_sele);$('#frm_tab').submit()" {{$check}}>
						  <label class="form-check-label" for="filtro_sele">Filtra selezionati</label>
							
							<div id='div_alert_sele' style='display:none' class='mb-3'>
								<span class='ml-3'>
									<font color='blue'>
										Selezione attiva
									</font>
									<button type="button" class="btn btn-primary ml-3" onclick="dele_sele()">Annulla selezione</button>
								</span>
							</div>
						
						
						</div>
					</div>	

					<?php
						$check="";
						if ($view_null=="1") $check="checked";
						
					?>	
					<div class="col-lg-2">
						<div class="form-check form-switch">
						  <input class="form-check-input" type="checkbox" id="view_null" name="view_null" onchange="$('#frm_tab').submit()" {{$check}}>
						  <label class="form-check-label" for="view_null">No righe nulle</label>
						</div>
					</div>
					<?php
						$check="";
						if ($tipo_ord=="on") $check="checked";
					?>	
					<div class="col-lg-2">
						<div class="form-check form-switch">
						  <input class="form-check-input" type="checkbox" id="tipo_ord" name="tipo_ord" onchange="$('#frm_tab').submit()" {{$check}}>
						  <label class="form-check-label" for="tipo_ord">Ordinamento decrescente</label>
						</div>
					</div>
					

					
				</div>	
				
				<div class='row'>
					<div class='col-lg-6' >
						<div class="input-group mb-3">
						  <span class="input-group-text" id="basic-addon1">Ricerca rapida Nominativo</span>
						  <input type="text" name='c_all' id='c_all' class="form-control" placeholder="Cerca Nominativo globalmente">
						</div>
						
						<div id='resp_cerca_o'></div>			
					</div>
					<div class='col-lg-6' >
						<div class="input-group mb-3">
						  <span class="input-group-text" id="basic-addon1">Ricerca rapida Azienda</span>
						  <input type="text" name='a_all' id='a_all' class="form-control" placeholder="Cerca Azienda globalmente">
						</div>
						
						<div id='resp_cerca_a'></div>			
					</div>					
				</div>
				
			
			</div>
		</div>	
			

			
				<div id='div_main'>
				<?php
				
					$frt_info=$frt['dati'];
					
					if (strlen($cerca_nome)!=0 || strlen($cerca_denom)!=0) {
						$referer = $_SERVER['HTTP_REFERER'] ?? null;
						$uri_complete = request()->path();
						$referer="#";
						echo "<a href='$referer' onclick='history.back()' >";	
							echo "<button type='button' class='btn btn-success btn-sm'>Torna elenco precedente</button>";
						echo "</a>";
					}

				?>
				
				<?php
				$ord_col=array();
				for ($aa=0;$aa<=16;$aa++) {$ord_col[$aa]="";}
				if (isset($ref_ordine)) $ord_col[$ref_ordine]="active_th";
				?>

					{{ $tabulato->links() }} 
					<p class='p0 text-left'>
						<small>{{$num_rec}} nominativi filtrati</small>
					</p>
					<div class="form-check ml-2">
					  <input class="form-check-input" type="checkbox" value="" id="selall" onchange='sel_all()'>
					  <label class="form-check-label" for="selall">
						Seleziona tutti (nella pagina)
					  </label>
					</div>					

					
					
					<table id='tbl_list' class="display">
						<thead>
							<tr>
								<th style='min-width:80px'>Operazioni</th>
								<th class=''>FRT</th>
								<th class=''>Contatto</th>
								<th class='{{$ord_col[3]}}'>IBAN</th>
								<th class='{{$ord_col[4]}}'>Giacenza</th>
								<th class='{{$ord_col[5]}}'>Codice</th>
								<th class='{{$ord_col[6]}}'>Nominativo</th>
								<th class='{{$ord_col[7]}}'>Luogo nascita</th>
								<th class='{{$ord_col[8]}}'>Data nascita</th>
								<th class='{{$ord_col[9]}}'>CF</th>
								<th class='{{$ord_col[10]}}'>Località</th>
								<th class='{{$ord_col[11]}}'>Provincia</th>
								<th class=''>Telefoni</th>
								<th class='{{$ord_col[13]}}'>Sindacato</th>
								<th class='{{$ord_col[14]}}'>Azienda</th>
								<th class='{{$ord_col[15]}}'>Ente</th>
								<th class='{{$ord_col[16]}}'>Zona</th>
							</tr>
						</thead>
						<tbody>

							@foreach ($tabulato as $tab)
							<?php
								if ($solo_fillea=="1") {
									if (!array_key_exists($tab->ID_anagr,$iscr_altrove)) continue;
								}
							?>
							<tr>

								<td style='min-width:80px;text-align:center'>
									<input class="form-check-input selezione" type="checkbox" onclick="sele_anagr(this.value,$(this).is(':checked'))" value="{{$tab->ID_anagr}}">
								
									<a href="javascript:void(0)" onclick="edit_element({{$tab->ID_anagr}})">
										<i class="ml-2 fas fa-user-edit fa-lg" title="Imposta note"></i>
									</a>
									
									<!--
									<a href="javascript:void(0)" onclick="edit_element(0)">
										<i class="fas fa-list-alt fa-lg" style="color:#6385c5;" title="Visualizza note"></i>
									</a>
									!-->
									

									
								</td>
								<td id='frt_{{$tab->ID_anagr}}'>
									<?php 
									$altrove=$frt['altrove'];
									if ($altrove[$tab->ID_anagr]=="1")
										echo " <i class='fas fa-square fa-sm mb-2' style='color: #ff0000;' title='FRT altrove'></i>";
									
									if (!isset($frt_info[$tab->ID_anagr]))
										echo " <i class='fas fa-square fa-sm mb-2' style='color: green' title='Nessuna sottoscrizione FRT'></i>";
									?>
								
									
									<div class='class_view'>
									@if ($tab->SINDACATO!="1")
									<a href="javascript:void(0)"  onclick="insert_frt({{$tab->ID_anagr}})">
										<button type="button" class="btn btn-success btn-sm mb-2">Iscrivi FRT</button>
									</a>
									
									@endif
									
									
									<?php
										if (isset($frt_info[$tab->ID_anagr]))
											echo render_frt($frt,$tab,$user_frt);
									?>
									</div>
									
								</td>
								<td id='contact{{$tab->ID_anagr}}'>
									<?php
									if (isset($note[$tab->ID_anagr]))
										echo " <i class='fas fa-square fa-sm mb-2' style='color: green'></i>";
									
									?>
									<div class='class_view'>
									<?php
										if (isset($note[$tab->ID_anagr]))
											echo render_note($note,$tab,$utenti,$id_user);
									?>
									</div>
								
								</td>
								<td>
									<?php 
									echo "<span title='".$tab->data_iban."'>";
									echo renderview($campo_ord,$tab->iban,"iban");
									echo "</span>";
									?>
								</td>
								<td>
									<?php 
									echo renderview($campo_ord,$tab->giacenza,"giacenza");
									?>								
								</td>
								<td>
									<?php 
									echo renderview($campo_ord,$tab->codice,"codice");
									?>								
								</td>

								<?php 
									$backg="";
									if ($tab->C3 && $tab->C3=="1") {
										$backg="background-color:gold";
									}
									if ($tab->C3 && $tab->C3=="2") {
										$backg="background-color:lightsalmon";
									}
								?>

								<td style='{{$backg}}'>
									<?php 
									$dn="";
									if (isset($tab->DATANASC)) {
										$dn=substr($tab->DATANASC,0,10);
									
									$params="nome=".$tab->NOME."&comunenasc=".$tab->COMUNENASC."&pro=".$tab->PRO."&da_na=".$dn."&loc=".$tab->LOC."&via=".$tab->VIA."&denom=".$tab->DENOM."&codfisc=".$tab->CODFISC;
									
									echo "<a href='http://www.filleaoffice.it/FO/deleghe/LAZI/menu.php?$params' target='_blank'>";
										echo renderview($campo_ord,$tab->NOME,"nome");
									echo "</a>";
									?>
								</td>	

								<td>
									<?php 
									echo renderview($campo_ord,$tab->COMUNENASC,"comunenasc");
									?>
								</td>
							
								
								<td>
									<?php
											echo renderview($campo_ord,$dn,"datanasc");
										}	
									?>
								</td>

								<td>
									<?php 
									echo renderview($campo_ord,$tab->CODFISC,"codfisc");
									echo "<br>";
									if ($tab->inca=="1" || $tab->fisco=="1") {
										echo " <i class='fas fa-square fa-sm mb-2' style='color: #ff0000;' title='presenza servizi CGIL'></i>";
									}
									if (in_array($tab->ID_anagr,$disdette)){
										echo " <i class='fas fa-exclamation-triangle fa-sm mb-2' style='color: black;' title='Disdetta'></i>";
										
									}										
										
									
									if (isset($iscr_altrove[$tab->ID_anagr])) {
										echo lav_altrove($iscr_altrove,$tab->ID_anagr);
									}
									if (isset($iscr_enti[$tab->ID_anagr])) {

										echo enti_altrove($iscr_enti,$tab->ID_anagr,1);
									}
									if (isset($iscr_altri_rilasci[$tab->ID_anagr]) && !(isset($iscr_enti[$tab->ID_anagr]))) {

										echo enti_altrove($iscr_altri_rilasci,$tab->ID_anagr,2);
									}

									
									?>
									
								</td>	
								<td>
									<?php 
									echo renderview($campo_ord,$tab->LOC,"loc");
									?>
								</td>
								<td>
									<?php 
									echo renderview($campo_ord,$tab->PRO,"pro");
									?>
								</td>
								<td>
									<?php 
										$tel_all="";
										$telefoni=telefoni($tab);
										for ($all_t=0;$all_t<=count($telefoni)-1;$all_t++) {
											if ($all_t>0) echo "<hr>";
											echo $telefoni[$all_t];
											$tel_all.=$telefoni[$all_t]." ";
										}
										
									?>
								
								</td>
								<?php
									$sind=$tab->SINDACATO;
									$backg="color:white;background-color:gray";
									if ($sind=="0") $backg="background-color:yellow";
									if ($sind=="1") $backg="color:white;background-color:red";
									if ($sind=="2") $backg="color:white;background-color:green";
									if ($sind=="3") $backg="color:white;background-color:blue";
								?>
								<td style='{{$backg}}'>
									<?php 
									echo renderview($campo_ord,$tab->SINDACATO,"sindacato");
									?>
								
								</td>
								<td title="{{$tab->C2}}">
									<?php
									echo $tab->C2." ->";
									if (strlen($tab->C2)>0) 
									echo "<a href='https://www.filleaoffice.it/anagrafe/pages/consultazioni/consultazioni.php?tb_fo=t4_lazi_a&p_iva=".$tab->C2."' target='_blank'>";
										echo renderview($campo_ord,$tab->DENOM,"denom");
									if (strlen($tab->C2)>0) 
									echo "</a>";
									echo "<a href='javascript:void(0)' onclick=\"$('#a_all').val('".$tab->DENOM."');$( '#a_all' ).trigger( 'paste' );\">";
										echo "<i class='fas fa-sign-in-alt fa-xs ml-2'></i>";
									echo "</a>";	

									if (isset($fgo[$tab->ID_anagr])){
										$id_fiscale=$tab->C2;
										echo"<br><u><a href='https://www.filleaoffice.it/filleago/index.php/sito/organizza?cantiere=$id_fiscale&newa=1' target='_blank'><small><font color='green'>EdilConnect</font></small></a></u>";
																											}	
									?>
								
								</td>
								<td>
									<?php 
									$ente=renderview($campo_ord,$tab->ENTE,"ente");
									if ($tab->ENTE=="A") $ente=str_replace("A","edilcassa",$ente);
									if ($tab->ENTE=="C") $ente=str_replace("C","cassaedile",$ente);
									echo $ente;
									?>
								
								</td>
								<td>
								<?php 
								echo renderview($campo_ord,$tab->zona,"zona");
								?>								
								<span id='id_ref{{$tab->ID_anagr}}' data-nome='{{ $tab->NOME }}'
								data-datanasc='{{ $dn }}'
								data-codfisc='{{ $tab->CODFISC }}'
								data-sindacato='{{ $tab->SINDACATO }}'
								data-ente='{{ $tab->ENTE }}'
								data-telefoni='{{ $tel_all }}'
								data-id_azienda='{{ $tab->C2 }}'
								>
								</span>
								
								</td>
								
							</tr>
							@endforeach
						
						</tbody>
					
					</table>
					{{ $tabulato->links() }}	
				</div>
			</div><!-- div_main -->
		</form>
      
    </div><!-- /.container-fluid -->
    <!-- /.content -->
		<form method='post' action="" id='frm_tab1' name='frm_tab1' autocomplete="off">
			<input name="_token" type="hidden" value="{{ csrf_token() }}" id='token_csrf'>
			<!-- Modal for edit note/contatti -->
			<div class="modal fade bd-example-modal-lg" id="modal_edit" tabindex="-1" role="dialog" aria-labelledby="info" aria-hidden="true">
			  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
				<input type='hidden' name='ref_edit' id='ref_edit'>
				<input type='hidden' name='nome_edit' id='nome_edit'>
				<input type='hidden' name='datanasc_edit' id='datanasc_edit'>
				<input type='hidden' name='ente_edit' id='ente_edit'>
				
				  <div class="modal-header">
					<h5 class="modal-title" id="title_modal_edit">Note/Contatti</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body" id='body_modal_edit'>
					<div class='row mt-2'>
						<div class='col-sm-12'>
							<div class='form-floating'>					
							<textarea class='form-control' id='note' name='note' rows='6' style='height:100px'></textarea>
							<label for='note'>Note</label>
							</div>
						</div>
					</div>	
					<div class='row mt-2'>
						<input type='hidden' name='stato_nota' id='stato_nota'>
					
						Impostazione stato: 
						<a href='javascript:void(0)'>
						<i class="far fa-circle fa-lg mt-3 semaforo" style="color: #ff0000;" id="sem1" onclick='set_stato(1)'></i></a>
						<a href='javascript:void(0)'>
						<i class="far fa-circle fa-lg mt-3 semaforo" style="color: #FFD43B;" id="sem2" onclick='set_stato(2)'></i></a>
						<a href='javascript:void(0)'>
						<i class="far fa-circle fa-lg mt-3 semaforo" style="color: #00ca00;" id="sem3" onclick='set_stato(3)'></i></a>
					</div>
				  </div>
				  <div class="modal-footer">
					
					<button type="button" onclick='save_note()' class="btn btn-primary" id='btn_save'>Salva</button>
					
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
					
				  </div>
				</div>
			  </div>
			</div>
		</form>
		
		<!--MODAL FRT !-->
		<form method='post' action="" id='frm_tab2' name='frm_tab2' autocomplete="off"  class="needs-validation" novalidate>
			<input name="_token" type="hidden" value="{{ csrf_token() }}" id='token_csrf'>
			<!-- Modal for edit note/contatti -->
			<div class="modal fade bd-example-modal-lg" id="modal_frt" tabindex="-1" role="dialog" aria-labelledby="info" aria-hidden="true">
			  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			  <input type='hidden' name='ref_edit_frt' id='ref_edit_frt'>
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="title_modal_frt">Inserisci nominativo in FilleaRealTime</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
	
				 <div class="modal-body" id='body_modal_edit_frt'>
					<div class='row mb-2'>
						<input type='hidden' name='id_azienda' id='id_azienda'>
						<div class="col-md-8">
							<div class="form-floating">
								<input class="form-control" id="nome_frt" name='nome_frt' type="text" placeholder="Nominativo" maxlength=100 required />
								<label for="nome_frt">Nominativo*</label>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-floating">
								<input class="form-control" id="natoil_frt" name='natoil_frt' type="date" required />
								<label for="natoil_frt">Nato il*</label>
							</div>
						</div>
						
					</div>	
					
					<div class='row mb-2'>
						<div class="col-md-4">
							<div class="form-floating">
								<input class="form-control" id="codfisc_frt" name='codfisc_frt' type="text" placeholder="CF" maxlength=16 required />
								<label for="codfisc_frt">Codice Fiscale*</label>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-floating">
								<input class="form-control" id="tel_frt" name='tel_frt' type="text" placeholder="Telefono" maxlength=50 required />
								<label for="tel_frt">Telefono*</label>
							</div>
						</div>

						<div class="col-md-4">
						  <div class="form-floating mb-3 mb-md-0">
							<select class="form-select" id="sesso_frt" aria-label="Sesso" name='sesso_frt' required>
								<option value=''>Select...</option>
								<option value='M'
								>Maschile</option>
								<option value='F' 
								>Femminile</option>
							</select>
							<label for="sesso_frt">Sesso*</label>
							</div>
						</div>
						<input type='hidden' name='sind_frt' id='sind_frt'>
						<input type='hidden' name='ente_frt' id='ente_frt'>

					</div>
					<div class='row mb-2 ml-3'>
						<div class="form-check form-switch">
						  <input class="form-check-input" type="checkbox" id="confirm_frt" required>
						  <label class="form-check-label for="confirm_frt">Conferma operazione di iscrizione</label>
						</div>						
					</div>
				
					
				  </div>
				  
				  <div class="modal-footer">
					
					<button type="submit" class="btn btn-primary" id='btn_save_frt'>Inserisci in FRT</button>
					
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
					
				  </div>
				</div>
			  </div>
			</div>
		</form>		
		

			<!-- Modal for info strutture -->
			<div class="modal fade bd-example-modal-lg" id="modal_strutture" tabindex="-1" role="dialog" aria-labelledby="info" aria-hidden="true">
			  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
				
				  <div class="modal-header">
					<h5 class="modal-title" id="title_modal_stru">Contatti della struttura</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body" id='body_strutture'>
					
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
				  </div>
				</div>
			  </div>
			</div>		
		
		
  </div>
  <!-- /.content-wrapper -->
  
 @endsection
 
 <?php
	function enti_altrove($iscr_enti,$id_anagr,$from) {
		
		$view="";
		$ente=$iscr_enti[$id_anagr]['ente'];
		$sind=$iscr_enti[$id_anagr]['sindacato'];

		$d_sind="";$colo="";
		$ente_descr="";
		if ($ente=="C") $ente_descr="Cassa edile - ";
		if ($ente=="A") $ente_descr="Edilcassa - ";
		if ($sind=="" || $sind==" ") {$colo="#c0c0c0";$d_sind="Non Spec.";}
		if ($sind=="0") {$colo="#ffd700";$d_sind="Non iscritto";}
		if ($sind=="1") {$colo="#ff0000";$d_sind="Fillea";}
		if ($sind=="2") {$colo="green";$d_sind="Filca";}
		if ($sind=="3") {$colo="blue";$d_sind="Feneal";}
		
		$circle="fas";
		if ($from=="2") {
			$ente_descr="Presente in altro tabulato - ";

		}	
		
		$view.=" <i class='fas fa-star fa-sm mb-2' style='color: $colo;' title='$ente_descr $d_sind'></i>";
		
		return $view;
	}
	function lav_altrove($iscr_altrove,$id_anagr) {
		
		$view="";
		$lav=$iscr_altrove[$id_anagr];
		
		$info=explode(";",$lav);
		for ($sc=0;$sc<=count($info)-1;$sc++) {
			$altrove=$info[$sc];
			$info_a=explode("|",$altrove);
			$circle="far";
			if (count($info_a)>1) {
				$sind=$info_a[0];
				$pro=$info_a[1];
				$att=$info_a[2];
				$d_sind="";$colo="";
				if ($sind!="1" && $sind!="2" && $sind!="3") continue;

				if ($sind=="1") {$colo="#ff0000";$d_sind="Fillea";}
				if ($sind=="2") {$colo="green";$d_sind="Filca";}
				if ($sind=="3") {$colo="blue";$d_sind="Feneal";}
				
				if ($att=="S") $circle="fas";
				
				$view.=" <i class='$circle fa-circle fa-sm mb-2' style='color: $colo;' title='Iscrizione altrove $pro - $d_sind'></i>";
			}
		}
		
		return $view;
	}
 
	function render_note($note,$tab,$utenti,$id_user) {
		$view=null;
		
		$view.="<table class='table table-bordered'>";
			$view.="<thead>";
				$view.="<tr>";
					$view.="<th>Utente</th>";
					$view.="<th>Data</th>";
					$view.="<th>Nota</th>";
					$view.="<th>#</th>";
				$view.="</tr>";
			$view.="</thead>";

			foreach($note[$tab->ID_anagr] as $note_dati) {
				$view.="<tr>";
					$view.="<td>";
						if (isset($utenti[$note_dati['id_user']])) {
							$view.=$utenti[$note_dati['id_user']]['tessera'];
							$view.="<br><small><i>";
							$view.=$utenti[$note_dati['id_user']]['name'];
							$view.="</i></small>";
						}	
						else
							$view.=$note_dati['id_user'];
					$view.="</td>";	

					
					$view.="<td>";
						$view.=$note_dati['data'];
					$view.="</td>";	

					$view.="<td>";
						$view.="<br><small><i>".$note_dati['note']."</i></small>";
						if (($note_dati['stato_nota'])!=null) {
							if ($note_dati['note']!=null)
								$view.="<br>";
							$stato_nota=$note_dati['stato_nota'];
							if ($stato_nota==1) 
								$view.="<i class='fas fa-circle fa-lg mt-3' style='color: #ff0000;'></i>";
							if ($stato_nota==2) 
								$view.="<i class='fas fa-circle fa-lg mt-3' style='color: #FFD43B;'></i>";
							if ($stato_nota==3) 
								$view.="<i class='fas fa-circle fa-lg mt-3' style='color: #00ca00;' ></i>";
						}

					$view.="</td>";
					$view.="<td style='vertical-align: middle;'>";
					if (count($note[$tab->ID_anagr])>1 && ($note_dati['id_user']==$id_user || $id_user==1)) {
						$id_nota=$note_dati['id_nota'];
						$view.="<a href='javascript:void(0)' onclick='dele_nota($id_nota,".$tab->ID_anagr.")'>";
							$view.="<i class='far fa-trash-alt'></i>";
						$view.="</a>";	
					}
					$view.="</td>";
											
				$view.="</tr>";
				
				
			}
			
		$view.="</table>";
		
		return $view;
	}
	function render_frt($frt_info,$tab,$user_frt) {
		$view=null;
		
		$view.="<table class='table table-bordered'>";
			$view.="<thead>";
				$view.="<tr>";
					$view.="<th>Utente</th>";
					$view.="<th>Data</th>";
				$view.="</tr>";
			$view.="</thead>";
			$frt=$frt_info['dati'];


			foreach($frt[$tab->ID_anagr] as $frt_dati) {
				$view.="<tr>";
					$view.="<td>";
						$view.=$frt_dati['utente'];
						if (isset($user_frt[$frt_dati['utente']])) {
							$view.="<br><small><i>".$user_frt[$frt_dati['utente']]['utentefillea']."</i></small>";
							
							$view.="(".$user_frt[$frt_dati['utente']]['sigla_pr'];
							$view.="<a href='javascript:void(0)' onclick=\"info_stru(".$user_frt[$frt_dati['utente']]['id_prov_associate'].")\">";
								$view.= "<i class='fas fa-sign-in-alt fa-xs ml-2'></i>";
							$view.= "</a>";	
							$view.=")";							
							
			
						}
					$view.="</td>";
					
					
					$view.="<td>";
						$view.=$frt_dati['data_update'];
					$view.="</td>";	
											
				$view.="</tr>";
			}
		$view.="</table>";

		
		return $view;
	}
	
	function renderview($campo_ord,$campo,$field) {
		if ($field=="sindacato") {
			$c=$campo;
			$campo="Non Spec.";
			if ($c=="0") $campo="Libero";
			if ($c=="1") $campo="FilleaCGIL";
			if ($c=="2") $campo="FilcaCISL";
			if ($c=="3") $campo="FenealUIL";
		}
		if($campo_ord==$field)
			return "<b>$campo</b>";
		else
			return $campo;
	}
	
	function telefoni($info) {
		$tel="";$arr_tel=array();
		for ($t=1;$t<=5;$t++) {
			if ($t==1) $tel=$info->C1;
			if ($t==2) $tel=$info->tel_ce;
			if ($t==3) $tel=$info->tel_gps;
			if ($t==4) $tel=$info->tel_sin;
			if ($t==5) $tel=$info->tel_altro;
			$tel=parse_tel($tel);
			if (!in_array($tel,$arr_tel) && strlen($tel)>0) 
				$arr_tel[]=$tel;
		}
		return $arr_tel;
	}
	function get_numerics($str) {
		preg_match_all('/\d+/', $str, $matches);
		$str=implode("",$matches[0]);
		return $str;
	}	
	
	function parse_tel($tel) {
		$t=get_numerics($tel);
		if (substr($t,0,2)=="39") $t="+".$t;
		$telok=true;
		if ($t!="") {
			$t=str_replace("+39","",$t);
			if (strlen($t)>1) {
				if (substr($t,0,1)!="3") $telok=false;
			}
			$t="+39".$t;
		}
		$t=trim($t);
		if ((strlen($t)==12 || strlen($t)==13) && $telok==true) 
			return $t;
		else
			return "";
	}
	
?>
 
 @section('content_plugin')
	<!-- jQuery -->
	<script src="{{ URL::asset('/') }}plugins/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="{{ URL::asset('/') }}plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- AdminLTE App -->
	<script src="{{ URL::asset('/') }}dist/js/adminlte.min.js"></script>

	<!-- Select2 -->
	<script src="{{ URL::asset('/') }}plugins/select2/js/select2.full.min.js"></script>
	<!-- inclusione standard
		per personalizzare le dipendenze DataTables in funzione delle opzioni da aggiungere: https://datatables.net/download/
	!-->
	
	<!-- dipendenze DataTables !-->
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.css"/>
		 
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.js"></script>
	<!-- fine DataTables !-->


	<script src="{{ URL::asset('/') }}dist/js/main.js?ver=1.162"></script>

@endsection

