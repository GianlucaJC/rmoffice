// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        } else {
			var cf=$("#codfisc_frt").val()
			var valida=validaCodiceFiscale(cf);
			if (valida==false) {
			  $("#codfisc_frt").removeClass('is-valid').addClass('is-invalid');
			  event.preventDefault()
			  event.stopPropagation()
			  alert("Codice fiscale non valido!")
			} else {
				$("#codfisc").removeClass('is-invalid').addClass('is-valid');			
				save_frt()
			}
			
		}	
        form.classList.add('was-validated')
      }, false)
    })
})()

let arr_sele=[]
$(document).ready( function () {
    /*
	$('#tbl_list tfoot th').each(function () {
        var title = $(this).text();
		if (title.length!=0) {
			w="200px"
			if (title=="Stato") w="60px"
			if (title=="ID") w="40px"
			$(this).html('<input style="width:'+w+'" type="text" placeholder="' + title + '" />');
		}	
    });
	*/
	//$('body').addClass("sidebar-collapse");
    var table=$('#tbl_list').DataTable({
		
		"paging": false,
		dom: 'Bfrtip',
		buttons: [
			'excel'
		],		
        language: {
			"search": "Cerca nella pagina:",
            lengthMenu: 'Visualizza _MENU_ records per pagina',
            zeroRecords: 'Nessun nominativo trovato',
            infoEmpty: 'Non sono disponibili nominativi',
            infoFiltered: '(Filtrati da _MAX_ nominativi totali)',
        },
    });	

	$('#tbl_list thead').on('click', 'th', function () {
	  var index = table.column(this).index();
	  $("#ref_ordine").val(index)
	  window.location = "?ref_ordine="+index+"&page=1";
	  //$("#frm_tab").submit();
	});
	
	var timer, delay = 500;	
	$( "#c_all" ).on( "keydown keyup  paste", function(event) {
		value=this.value
		clearTimeout(timer);
		timer = setTimeout(function() {
			html=""
			html+="<div class='spinner-grow' role='status'>";
				html+="<fspan class='sr-only'>Loading...</span>";
			html+="</div>";
			
			$("#resp_cerca_o").html(html)
			$("#resp_cerca_o").show(400);
			cerca_fo(value);
		}, delay );	
	} );		
		
	
	if( typeof localStorage.elem_sele != 'undefined' )  {
		elem_sele=localStorage.elem_sele.split(";")
		elem=localStorage.elem_sele
		$("#elem_sele").val(elem)
		if (elem.length!=0) $("#div_alert_sele").show()
		else  $("#div_alert_sele").hide()
		for (sca=0;sca<=elem_sele.length-1;sca++) {
			arr_sele.push(elem_sele[sca])
		}	
		
		$('.selezione').each(function(index, obj){
			if (elem_sele.includes($(this).val())) {
				$(this).prop('checked', true)
			}
		});
		
	}
	
		
} );

function dele_sele() {
	if (!confirm("Sicuri di annullare la selezione in corso?")) return false;
	$("#elem_sele").val('')
	$('.selezione').each(function(index, obj){
		$(this).prop('checked', false)
	});	
	localStorage.elem_sele=""
	$("#div_alert_sele").hide(150)
	
}

function sele_anagr(id_anagr,stato) {
	if (stato==true) 
		arr_sele.push(id_anagr)	
	else {
		arr_sele = arr_sele.filter(item => item !== id_anagr)
	}
	elem_sele="";
	for (sca=0;sca<=arr_sele.length-1;sca++) {
		if (sca>0) elem_sele+=";"
		elem_sele+=arr_sele[sca]
	}
	localStorage.elem_sele=elem_sele
	if (elem_sele.length!=0) $("#div_alert_sele").show(150)
	else  $("#div_alert_sele").hide(150)
}	
	
function insert_frt(id_anagr) {
	$("#confirm_frt").prop('checked', false)
	$("#btn_save_frt").html('Inserisci in FRT'); 
	$("#btn_save_frt").prop('disabled', false);
	
	ref=$("#id_ref"+id_anagr)
	$("#ref_edit_frt").val(id_anagr)
	nome=ref.data('nome')
	datanasc=ref.data('datanasc')
	codfisc=ref.data('codfisc')
	sindacato=ref.data('sindacato')
	ente=ref.data('ente')
	telefoni=ref.data('telefoni')
	sesso="M"
	if (codfisc.length>0) {
		if (parseInt(codfisc.substr(9,2))>31) sesso="F"
	}
	
	//precompilazione delega in funzione della scelta
	$("#nome_frt").val(nome)
	$("#natoil_frt").val(datanasc)
	$("#codfisc_frt").val(codfisc)
	$("#sesso_frt").val(sesso)
	$("#sind_frt").val(sindacato)
	$("#ente_frt").val(ente)
	$("#tel_frt").val(telefoni)
	
	$('#modal_frt').modal('toggle')
	$("#title_modal_frt").html("Inserisci <b>"+nome+"</b> in FilleaRealTime<b>")
}	

function save_frt() {
	event.preventDefault()
	if (!($('#confirm_frt').is(':checked'))) {
		alert("Confermare la richiesta di iscrizione in FRT")
		return false;
	}
		
	ref_edit_frt=$("#ref_edit_frt").val()
	nome_frt=$("#nome_frt").val()
	natoil_frt=$("#natoil_frt").val()
	codfisc_frt=$("#codfisc_frt").val()
	sesso_frt=$("#sesso_frt").val()
	sind_frt=$("#sind_frt").val()
	ente_frt=$("#ente_frt").val()
	tel_frt=$("#tel_frt").val()

	$("#btn_save_frt").html('Attendere...'); 
	$("#btn_save_frt").prop('disabled', true);
	
	var timer,delay = 800;	

	clearTimeout(timer);
	timer = setTimeout(function() {	
		base_path = $("#url").val();
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		let CSRF_TOKEN = $("#token_csrf").val();
		$.ajax({
			type: 'POST',
			url: base_path+"/ins_frt",
			data: {_token: CSRF_TOKEN,nome_frt:nome_frt,natoil_frt:natoil_frt,codfisc_frt:codfisc_frt,sesso_frt:sesso_frt,sind_frt:sind_frt,ente_frt:ente_frt,tel_frt:tel_frt},
			success: function (data) {
				html="";
				html+=`<a href='#' onclick="$('#frm_tab').submit()">
						Refresh pagina dopo inserimento FRT
					</a>`	
				$("#frt_"+ref_edit_frt).html(html)
				$('#modal_frt').modal('toggle')			
			}
		})	
	}, delay)	
}	
	
	
function edit_element(id_anagr) {
	$("#btn_save").html('Salva'); 
	$("#btn_save").prop('disabled', false);
	
	ref=$("#id_ref"+id_anagr)
	nome=ref.data('nome')
	datanasc=ref.data('datanasc')
	ente=ref.data('ente')
	$("#note").val('')
	$("#ref_edit").val(id_anagr)
	$("#nome_edit").val(nome)
	$("#datanasc_edit").val(datanasc)
	$("#ente_edit").val(ente)
	
	$('#modal_edit').modal('toggle')
	$("#title_modal_edit").html("Impostazione Note/Contatti per: <b>"+nome+"</b>")
	//$("#body_modal_edit").html("Caricamento informazioni in corso...")	
}

function save_note() {
	ref_edit=$("#ref_edit").val()
	nome_edit=$("#nome_edit").val()
	datanasc_edit=$("#datanasc_edit").val()
	ente_edit=$("#ente_edit").val()
	note=$("#note").val()
	
	if (note.length>0) 
		event.preventDefault()
	else return false;
	$("#btn_save").html('Attendere...'); 
	$("#btn_save").prop('disabled', true);
	
	var timer,delay = 800;	

	clearTimeout(timer);
	timer = setTimeout(function() {	
		base_path = $("#url").val();
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		let CSRF_TOKEN = $("#token_csrf").val();
		$.ajax({
			type: 'POST',
			url: base_path+"/save_note",
			data: {_token: CSRF_TOKEN,nome_edit:nome_edit,datanasc_edit:datanasc_edit,ente_edit:ente_edit,note:note},
			success: function (data) {
				html="";
				html+=`<a href='#' onclick="$('#frm_tab').submit()">
						Refresh pagina dopo inserimento
					</a>`	
				$("#contact"+ref_edit).html(html)
				$('#modal_edit').modal('toggle')			
			}
		})	
	}, delay)
}


function dele_element(value) {
	if(!confirm('Sicuri di eliminare l\'elemento?')) 
		event.preventDefault() 
	else 
		$('#dele_cand').val(value)	
}

function restore_element(value) {
	if(!confirm('Sicuri di ripristinare l\'elemento?')) 
		event.preventDefault() 
	else 
		$('#restore_cand').val(value)	
}

function push_appalti(value) {
	if(!confirm("Sicuri di sollecitare tutti i lavoratori dell'appalto (che non hanno risposto)?")) 
		event.preventDefault() 
	else 
		$('#push_appalti').val(value)	
}

function cerca_fo(value) {
	//$('#resp_cerca_o').empty()
	$('#resp_cerca_o').show()
	$("#div_main").hide();
	base_path = $("#url").val();
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	let CSRF_TOKEN = $("#token_csrf").val();
	$.ajax({
		type: 'POST',
		url: base_path+"/cerca_fo",
		data: {_token: CSRF_TOKEN,value:value},
		success: function (data) {
			render_all(data)
		}
	})	
		
}

function render_all(data) {
	obj=JSON.parse(data)
	console.log(obj)
	html="";
	html+=`<button type='button' class='btn btn-primary' onclick=\"$('#c_all').val('');$('#resp_cerca_o').hide(300);$('#div_main').show();\">Chiudi Elenco</button>
		<table id='tb_resp' class='table table-bordered table-striped'>
		<thead>
			<tr>
				<th>Nominativo</th>
				<th>Nato il</th>
				<th>Codice fiscale</th>
				<th>Località</th>
				<th>Pro</th>
				<th>Telefoni</th>
			</tr>
		</thead>
		<tbody>`
			for (sca=0;sca<=obj.length-1;sca++) {
				id_anagr=obj[sca].id_anagr
				nome="";datanasc="";codfisc="";loc="";pro="";tel="";
				if (obj[sca].nome) nome=obj[sca].nome
				
				if (obj[sca].datanasc) datanasc=obj[sca].datanasc
				if (obj[sca].codfisc) codfisc=obj[sca].codfisc
				if (obj[sca].loc) loc=obj[sca].loc
				if (obj[sca].pro) pro=obj[sca].pro
				if (obj[sca].c1) tel=obj[sca].c1

				html+=`<tr data-cognome=\"`+nome+`\">
					<td>
						<button type="submit" name='nome_speed' class="btn  btn-primary btn-sm btn-block" onclick="$('#cerca_nome').val(`+id_anagr+`)">`+nome+`</button>
					</td>
					<td>`+datanasc+`</td>
					<td>`+codfisc+`</td>
					<td>`+loc+`</td>
					<td>`+pro+`</td>
					<td>`+tel+`</td>
				</tr>`

			}
		html+="</tbody>";	
	html+="</table><hr>";  
	
	
	$("#resp_cerca_o").html(html);
	
	

	

	$("#tb_resp").DataTable({
		//"responsive": true, 
		"lengthChange": false, "autoWidth": false,
		"pageLength": 15,
		//, "colvis"
		"buttons": ["copy", "excel", "pdf"],
		"language": {
			"zeroRecords": "Non ci sono Nominativi",
			"info": "Pagina mostrata _PAGE_ di _PAGES_ di _TOTAL_ Nominativi",
			"infoEmpty": "Non risultano Nominativi con questo criterio",
			"infoFiltered": "(filtrati da _MAX_ record totali)",
			"search":         "Cerca:",
			"paginate": {
				"first":      "Prima",
				"last":       "Ultima",
				"next":       "Successiva",
				"previous":   "Precedente"
			}
		
		}	  
	}).buttons().container().appendTo('#tb_resp_wrapper .col-md-6:eq(0)');		
	
	table=$("#tb_resp").DataTable();
	
	//datatable on change event jquery 
	
	$('#tb_resp tbody').on('mouseover', 'td', function () {
		
		if ($(this).parents('tr').hasClass('corrente')) {
			$(this).parents('tr').removeClass('corrente');
		} else {
			table.$('tr.corrente').removeClass('corrente');                   
			$(this).parents('tr').addClass('corrente');                    
		}
	});		
	
	/*
	$('#tb_resp').on('click' ,'tr', function() {
		info_atleta(this)
	})
*/	
	
}

function validaCodiceFiscale(cf){
	  var validi, i, s, set1, set2, setpari, setdisp;
	  if( cf == '' )  return '';
	  cf = cf.toUpperCase();
	  if( cf.length != 16 )
		  return false;
	  validi = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	  for( i = 0; i < 16; i++ ){
		  if( validi.indexOf( cf.charAt(i) ) == -1 )
			  return false;
	  }
	  set1 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	  set2 = "ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ";
	  setpari = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	  setdisp = "BAKPLCQDREVOSFTGUHMINJWZYX";
	  s = 0;
	  for( i = 1; i <= 13; i += 2 )
		  s += setpari.indexOf( set2.charAt( set1.indexOf( cf.charAt(i) )));
	  for( i = 0; i <= 14; i += 2 )
		  s += setdisp.indexOf( set2.charAt( set1.indexOf( cf.charAt(i) )));
	  if( s%26 != cf.charCodeAt(15)-'A'.charCodeAt(0) )
		  return false;
	  return true;
}