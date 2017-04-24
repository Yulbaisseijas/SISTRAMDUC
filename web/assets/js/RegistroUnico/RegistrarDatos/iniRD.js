var idRegistro = 0;
var miniPersonal = true;
var miniRegistros = true;
var miniCargos = true;
var miniHijos = true;

$( window ).load(function() {
    
    var status = window.location.href.split("/");
    if(status[status.length-1] == "success")
        toastr.success("Datos registrados exitosamente!.", "Exito!", {
            "timeOut": "0",
            "extendedTImeout": "0"
         });
    else if(status[status.length-1] == "error")
        toastr.error("Error hubo problemas al subir los archivos!", "Error", {
            "timeOut": "0",
            "extendedTImeout": "0"
         });
    
    $("#miniPersonal").click();
    $("#miniRegistros").click();
    $("#miniCargos").click();
    $("#miniHijos").click();
    
    $("#tableUsers").DataTable( {
          "ajax": "/web/app_dev.php/registro/enviar-emails",
          "columns": [
		        { "data": "Email" },
		        { "data": "Estatus" },
		        { "data": "Registro Completo" }
	       ],
	       "language": {
            	"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
    });
    
    $.ajax({
        method: "POST",
        url:  "/web/app_dev.php/registro/obtener-datos",
        dataType: 'json',
        success: function(data){
            var estatus ="<option value='' selected='selected'>Seleccione una opción</option>";
            for(var i = 0; i < data["estatus"].length; i++)
                estatus = estatus+"<option value='"+data["estatus"][i]+"'>"+data["estatus"][i]+"</option>";
            var nivel ="<option value='' selected='selected'>Seleccione una opción</option>";
            for(var i = 0; i < data["nivel"].length; i++)
                nivel = nivel+"<option value='"+data["nivel"][i]+"'>"+data["nivel"][i]+"</option>";
            var tipo_registro ="<option value='' selected='selected'>Seleccione una opción</option>";
            for(var i = 0; i < data["tipo_registro"].length; i++)
                tipo_registro = tipo_registro+"<option value='"+data["tipo_registro"][i]+"'>"+data["tipo_registro"][i]+"</option>";
            var cargo ="<option value='' selected='selected'>Seleccione una opción</option>";
            for(var i = 0; i < data["cargo"].length; i++)
                cargo = cargo+"<option value='"+data["cargo"][i]+"'>"+data["cargo"][i]+"</option>";
            $("#EstatusDatos").html(estatus);
            $("#NivelDeEstudioDatos").html(nivel);
            $("#TipoDeRegistroDatos").html(tipo_registro);
            $("#cargosDatos").html(cargo);
        }
    });
    
     $.ajax({
        method: "POST",
        url:  "/web/app_dev.php/registro/enviar-lastid",
        dataType: 'json',
        success: function(data){
            if(data[0].lastId != null)
                idRegistro = data[0].lastId;
        }
    });
    $('#IdParticipanteRegistro').html("<option value='-1'>No existen registros</option>");
    $('#idRevistaRegistro').html("<option value='-1'>No existen registros</option>");
    $('#datetimepicker1').datetimepicker();
    $('#datetimepicker2').datetimepicker();
    $('#datetimepicker3').datetimepicker();
    $('#datetimepicker4').datetimepicker();
    $('#datetimepicker5').datetimepicker();
    $('#datetimepicker6').datetimepicker();
    $("#CedulaRifActaCargaDatos").fileinput({
        language: "es"
    });
    $("#ActaNacCargaHijoDatos").fileinput({
        language: "es"
    });
});

