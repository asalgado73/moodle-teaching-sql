comprobar=function(num){
	$.ajax({
      type:"POST",
      url: "rq.php",
      dataType: "json",
      data:{
          comprobar:$("#respuesta").val(),
		  pregunta:num
      },
      beforeSend: function(){
          //alert( "enviado antes de..." );
      },
      success: function( json ){
		  $("#resultado").val(json.prueba);
          //$("#tabla").html( json.prueba); 
          //$("#tabla").val(json.tabla)
  
      },
      error: function( a, b, c){
          alert( "a: "+a );
          alert( "b: "+b );
          alert( "c: "+c );
      }
      
  });
};