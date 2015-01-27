consultar=function(num){
  	$.ajax({
      type:"POST",
      url: "rq.php",
      dataType: "json",
      data:{
          numero:num
      },
      beforeSend: function(){
          //alert( "enviado antes de..." );
      },
      success: function( json ){
		  $("#tabla").html("");
		  var aux=json;
          for (n in aux) {
			 	$("#tabla").append(json[n]+"<br>");
			}
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

