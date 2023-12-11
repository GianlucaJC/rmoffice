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
	$('body').addClass("sidebar-collapse");
    var table=$('#tbl_list').DataTable({
		
		"paging": false,
		dom: 'Bfrtip',
		buttons: [
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
		
} );


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
	html+="<button type='button' class='btn btn-primary' onclick=\"$('#c_all').val('');$('#resp_cerca_o').hide(300);$('#div_main').show();\">Chiudi Elenco</button>";
	html+="<table id='tb_resp' class='table table-bordered table-striped'>";
		html+="<thead>";
			html+="<tr>";
				html+="<th>Nominativo</th>";
				html+="<th>Nato il</th>";
				html+="<th>Codice fiscale</th>";
				html+="<th>Località</th>";
				html+="<th>Pro</th>";
				html+="<th>Telefoni</th>";
			html+="</tr>";
		html+="</thead>";
		html+="<tbody>";
			for (sca=0;sca<=obj.length-1;sca++) {
				nome="";datanasc="";codfisc="";loc="";pro="";tel="";
				if (obj[sca].nome) nome=obj[sca].nome
				
				if (obj[sca].datanasc) datanasc=obj[sca].datanasc
				if (obj[sca].codfisc) codfisc=obj[sca].codfisc
				if (obj[sca].loc) loc=obj[sca].loc
				if (obj[sca].pro) pro=obj[sca].pro
				if (obj[sca].c1) tel=obj[sca].c1

				html+="<tr data-cognome=\""+nome+"\">"
					html+="<td>";
						html+="<b>"+nome+"</b>"
					html+="</td>"
					html+="<td>";
						html+=datanasc
					html+="</td>"
					html+="<td>";
						html+=codfisc
					html+="</td>"
					html+="<td>";
						html+=loc
					html+="</td>"					
					html+="<td>";
							html+=pro
					html+="</td>"					
					html+="<td>";
							html+=tel
					html+="</td>"					

				html+="</tr>"; 

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