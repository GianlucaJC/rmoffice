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