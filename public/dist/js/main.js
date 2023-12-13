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
		
	/*
	if( typeof localStorage.elem_sele != 'undefined' )  {
		elem_sele=localStorage.elem_sele.split(";")
		$('.selezione').each(function(index, obj){
			if (elem_sele.includes($(this).val())) {
				$(this).prop('checked', true)
			}
		});		
	}
	*/
		
} );

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
	//localStorage.elem_sele=elem_sele
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
						<button type="button" class="btn  btn-primary btn-sm btn-block" onclick="$('#cerca_nome').val(`+id_anagr+`)">`+nome+`</button>
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