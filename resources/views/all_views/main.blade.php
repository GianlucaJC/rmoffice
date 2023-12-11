@extends('all_views.viewmaster.index')

@section('title', 'RM_Office')

@section('extra_style') 
<!-- x button export -->

<link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet">
<!-- -->
@endsection



<style>
<!-- crea problemi con il footer di fine pagina !-->
<!-- 
	foot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
!-->	
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
			<?php
				$check="";
				if ($view_null=="1") $check="checked";
				
			?>		
			<div class='row mt-2'>
				<div class="input-group mb-3">
				  <span class="input-group-text" id="basic-addon1">Ricerca rapida</span>
				  <input type="text" name='c_all' id='c_all' class="form-control" placeholder="Cerca Nominativo globalmente">
				</div>
				<div id='resp_cerca_o'></div>			
			</div>
			
			<div id='div_main'>
				<div class="row">
					<div class="col-lg-3">
						<div class="form-check form-switch mt-3 ml-3">
						  <input class="form-check-input" type="checkbox" id="view_null" name="view_null" onchange="$('#frm_tab').submit()" {{$check}}>
						  <label class="form-check-label" for="view_null">Non mostrare righe con campo di ricerca nullo</label>
						</div>
					</div>
					<?php
						$check="";
						if ($tipo_ord=="1") $check="checked";
					?>					
					<div class="col-lg-3">
						<div class="form-check form-switch mt-3 ml-3">
						  <input class="form-check-input" type="checkbox" id="tipo_ord" name="tipo_ord" onchange="$('#frm_tab').submit()" {{$check}}>
						  <label class="form-check-label" for="tipo_ord">Ordinamento decrescente</label>
						</div>
					</div>				
				</div>



				<div class="row mt-2">
				  <div class="col-lg-12">
					{{ $tabulato->links() }}
					
					<table id='tbl_list' class="display">
						<thead>
							<tr>
								<th>Operazioni</th>
								<th>FRT</th>
								<th>Contatto</th>
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

								<td>
									<a href="#">
										<button type="button" class="btn btn-secondary" alt='Scheda'><i class="fas fa-edit" title="Visualizza tutti tutti i dati"></i></button>
									</a>
								<td>
									<?php
										if (isset($frt[$tab->ID_anagr]))
											echo render_frt($frt,$tab,$user_frt);
									?>
								</td>
								<td>Contatto</td>
								<td>IBAN</td>
								<td>Giacenza</td>
								<td>Codice</td>

								<td>
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
										if (isset($tab->DATANASC))
										echo renderview($campo_ord,substr($tab->DATANASC,0,10),"datanasc");
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
										$telefoni=telefoni($tab);
										for ($all_t=0;$all_t<=count($telefoni)-1;$all_t++) {
											if ($all_t>0) echo "<hr>";
											echo $telefoni[$all_t];
										}
										
									?>
								
								</td>
								<td>
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
								<td>Zona</td>
							</tr>
							@endforeach
						
						</tbody>
					
					</table>
					{{ $tabulato->links() }}	
				  </div>
				  
				  

				</div>
			</div>
			<!-- /.row -->


			<input type='hidden' id='dele_cand' name='dele_cand'>

		</form>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
 @endsection
 
 <?php
	function render_frt($frt,$tab,$user_frt) {
		
		
		$view=null;
		
		$view.="<table>";
			$view.="<tr>";
				$view.="<th>Utente</th>";
				$view.="<th>Data</th>";
			$view.="</tr>";
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


	<script src="{{ URL::asset('/') }}dist/js/main.js?ver=1.027"></script>

@endsection

