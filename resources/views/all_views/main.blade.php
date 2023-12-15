@extends('all_views.viewmaster.index')

@section('title', 'RM_Office')

@section('extra_style') 
<!-- x button export -->

<link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet">
<!-- -->
@endsection



<style>
.filtri {
  border: 2px outset blue;
  padding:10px;
  background-color: lightblue;
 
}
</style>

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
		

  
			<input type='hidden' name='ref_ordine' id='ref_ordine' value='{{$ref_ordine}}'>
			<input type="hidden" value="{{url('/')}}" id="url" name="url">
			<input type='hidden' name='cerca_nome' id='cerca_nome'>
			
			<input type='hidden' name='elem_sele' id='elem_sele' value='{{$elem_sele}}'>
			<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<a class="navbar-brand" href="#">Impostazioni</a>
			<button onclick="$('.class_view').toggle()" type="button" class="btn btn-outline-primary">Mostra/Nascondi informazioni extra</button>
			
			<button onclick="$('#div_speed').toggle(150)" type="button" class="btn btn-outline-primary ml-3">Mostra/Nascondi ricerca rapida</button>
						
			  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			  </button>

			  <div class="collapse navbar-collapse" id="navbarText">
				<ul class="navbar-nav mr-auto">
				  <li class="nav-item">
					<a class="nav-link" href="#" onclick="$('#div_filtri').show(150)">Filtri</a>
				  </li>

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


				</select>
			  </div>

			</nav>
			
			
			<div class='filtri mb-3' id='div_filtri'>

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
						if ($view_null=="1") $check="checked";
						
					?>	
					<div class="col-lg-3">
						<div class="form-check form-switch">
						  <input class="form-check-input" type="checkbox" id="view_null" name="view_null" onchange="$('#frm_tab').submit()" {{$check}}>
						  <label class="form-check-label" for="view_null">Non mostrare righe con campo di ricerca nullo</label>
						</div>
					</div>
					<?php
						$check="";
						if ($tipo_ord=="1") $check="checked";
					?>					
					<div class="col-lg-3">
						<div class="form-check form-switch">
						  <input class="form-check-input" type="checkbox" id="tipo_ord" name="tipo_ord" onchange="$('#frm_tab').submit()" {{$check}}>
						  <label class="form-check-label" for="tipo_ord">Ordinamento decrescente</label>
						</div>
					</div>
					
					<?php
						$check="";
						if ($filtro_sele=="1") $check="checked";
					?>					
					<div class="col-lg-3">
						<div class="form-check form-switch">
						  <input class="form-check-input" type="checkbox" id="filtro_sele" name="filtro_sele" onchange="$('#elem_sele').val(localStorage.elem_sele);$('#frm_tab').submit()" {{$check}}>
						  <label class="form-check-label" for="filtro_sele">Filtra selezionati</label>
							
							<div id='div_alert_sele' style='display:none'>
								<span class='ml-3'>
									<font color='blue'>
										Selezione attiva
									</font>
									<button type="button" class="btn btn-primary ml-3" onclick="dele_sele()">Annulla selezione</button>
								</span>
							</div>
						
						
						</div>
					</div>					
					
				</div>			
			
			</div>
			
				
			<div class='row mt-2' id='div_speed' style='display:none'>
				<div class="input-group mb-3">
				  <span class="input-group-text" id="basic-addon1">Ricerca rapida</span>
				  <input type="text" name='c_all' id='c_all' class="form-control" placeholder="Cerca Nominativo globalmente">
				</div>
				
				<div id='resp_cerca_o'></div>			
			</div>
			
			<div id='div_main'>
				<?php
					if (strlen($cerca_nome)!=0) {
						$referer = $_SERVER['HTTP_REFERER'] ?? null;
						$uri_complete = request()->path();
						$referer="#";
						echo "<a href='$referer' onclick='history.back()' >";	
							echo "<button type='button' class='btn btn-success btn-sm'>Torna elenco precedente</button>";
						echo "</a>";
					}

					
				?>
				<div class="row mt-2">
				  <div class="col-lg-12">
					{{ $tabulato->links() }}
					
					<table id='tbl_list' class="display">
						<thead>
							<tr>
								<th style='min-width:80px'>Operazioni</th>
								<th class='class_view'>FRT</th>
								<th class='class_view'>Contatto</th>
								<th>IBAN</th>
								<th>Giacenza</th>
								<th>Codice</th>
								<th>Nominativo</th>
								<th>Luogo nascita</th>
								<th>Data nascita</th>
								<th>CF</th>
								<th>Località</th>
								<th>Provincia</th>
								<th>Telefoni</th>
								<th>Sindacato</th>
								<th>Azienda</th>
								<th>Ente</th>
								<th>Zona</th>
							</tr>
						</thead>
						<tbody>

							@foreach ($tabulato as $tab)
							<tr>

								<td style='min-width:80px;text-align:center'>
									<input class="form-check-input selezione" type="checkbox" onclick="sele_anagr(this.value,$(this).is(':checked'))" value="{{$tab->ID_anagr}}">
								
									<a href="javascript:void(0)" onclick="edit_element({{$tab->ID_anagr}})">
										<i class="ml-2 fas fa-user-edit fa-lg" title="Imposta note"></i>
									</a>
									<a href="javascript:void(0)" onclick="edit_element(0)">
										<i class="fas fa-list-alt fa-lg" style="color:#6385c5;" title="Visualizza note"></i>
									</a>
								</td>
								<td class='class_view'  id='frt_{{$tab->ID_anagr}}'>
									<a href="javascript:void(0)"  onclick="insert_frt({{$tab->ID_anagr}})">
										<button type="button" class="btn btn-success btn-sm mb-2">Iscrivi FRT</button>
									</a>
									<div id='div_anagr{{$tab->ID_anagr}}' class='div_frt'>
									<div>
									<?php
										if (isset($frt[$tab->ID_anagr]))
											echo render_frt($frt,$tab,$user_frt);
									?>
									
								</td>
								<td  class='class_view' id='contact{{$tab->ID_anagr}}'>
									
									<?php
										if (isset($note[$tab->ID_anagr]))
											echo render_note($note,$tab,$utenti);
									?>
								
								</td>
								<td>IBAN</td>
								<td>Giacenza</td>
								<td>Codice</td>

								<?php 
									$backg="";
									if ($tab->C3 && $tab->C3=="1") {
										$backg="background-color:lightgoldenrodyellow";
									}
									if ($tab->C3 && $tab->C3=="2") {
										$backg="background-color:lightsalmon";
									}
								?>

								<td style='{{$backg}}'>
									<?php 
									echo renderview($campo_ord,$tab->NOME,"nome");
									?>
								</td>	

								<td>
									<?php 
									echo renderview($campo_ord,$tab->COMUNENASC,"comunenasc");
									?>
								</td>
							
								
								<td>
									<?php
										$dn="";
										if (isset($tab->DATANASC)) {
											$dn=substr($tab->DATANASC,0,10);
											echo renderview($campo_ord,$dn,"datanasc");
										}	
									?>
								</td>

								<td>
									<?php 
									echo renderview($campo_ord,$tab->CODFISC,"codfisc");
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
								<td>
									<?php 
									echo renderview($campo_ord,$tab->DENOM,"denom");
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
								<td>Zona
								
								<span id='id_ref{{$tab->ID_anagr}}' data-nome='{{ $tab->NOME }}'
								data-datanasc='{{ $dn }}'
								data-codfisc='{{ $tab->CODFISC }}'
								data-sindacato='{{ $tab->SINDACATO }}'
								data-ente='{{ $tab->ENTE }}'
								data-telefoni='{{ $tel_all }}'>
								</span>
								
								</td>
								
							</tr>
							@endforeach
						
						</tbody>
					
					</table>
					{{ $tabulato->links() }}	
				  </div>
				  
				  

				</div>
			</div>
			<!-- /.row -->


		</form>
      </div><!-- /.container-fluid -->
    </div>
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
							<textarea class='form-control' id='note' name='note' rows='6' style='height:100px' required></textarea>
							<label for='note'>Note</label>
							</div>
						</div>
					</div>						
				  </div>
				  <div class="modal-footer">
					
					<button type="submit" onclick='save_note()' class="btn btn-primary" id='btn_save'>Salva</button>
					
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
		
		
  </div>
  <!-- /.content-wrapper -->
  
 @endsection
 
 <?php
	function render_note($note,$tab,$utenti) {
		$view=null;
		
		$view.="<table class='table table-bordered'>";
			$view.="<thead>";
				$view.="<tr>";
					$view.="<th>Utente</th>";
					$view.="<th>Data</th>";
					$view.="<th>Nota</th>";
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
					$view.="</td>";
											
				$view.="</tr>";
				
				
			}
			
		$view.="</table>";
		
		return $view;
	}
	function render_frt($frt,$tab,$user_frt) {
		$view=null;
		
		$view.="<table class='table table-bordered'>";
			$view.="<thead>";
				$view.="<tr>";
					$view.="<th>Utente</th>";
					$view.="<th>Data</th>";
				$view.="</tr>";
			$view.="</thead>";
			foreach($frt[$tab->ID_anagr] as $frt_dati) {
				$view.="<tr>";
					$view.="<td>";
						$view.=$frt_dati['utente'];
						if (isset($user_frt[$frt_dati['utente']]))
							$view.="<br><small><i>".$user_frt[$frt_dati['utente']]."</i></small>";
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

	<!-- inclusione standard
		per personalizzare le dipendenze DataTables in funzione delle opzioni da aggiungere: https://datatables.net/download/
	!-->
	
	<!-- dipendenze DataTables !-->
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.css"/>
		 
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.js"></script>
	<!-- fine DataTables !-->


	<script src="{{ URL::asset('/') }}dist/js/main.js?ver=1.109"></script>

@endsection

