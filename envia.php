<?php
//enviar.php
/*
 * RECIBIMOS LA RESPUESTA
 */
function enviar($recibido, $enviado, $idWA, $timestamp, $telefonoCliente)
{
    require_once './conexion.php';
    //CONSULTAMOS TODOS LOS REGISTROS CON EL ID DEL MANSAJE
    $sqlCantidad = "SELECT count(id) AS cantidad FROM registro WHERE id_wa='" . $idWA . "';";
    $resultCantidad = $conn->query($sqlCantidad);
    //OBTENEMOS LA CANTIDAD DE MENSAJES ENCONTRADOS (SI ES 0 LO REGISTRAMOS SI NO NO)
    $cantidad = 0;
    //SI LA CONSULTA ARROJA RESULTADOS
    if ($resultCantidad) {
        //OBTENEMOS EL PRIMER REGISTRO
        $rowCantidad = $resultCantidad->fetch_row();
        //OBTENEMOS LA CANTIDAD DE REGISTROS
        $cantidad = $rowCantidad[0];
    }
    //SI LA CANTIDAD DE REGISTROS ES 0 ENVIAMOS EL MENSAJE DE LO CONTRARIO NO LO ENVIAMOS PORQUE YA SE ENVIO
    if ($cantidad == 0) {
        //TOKEN QUE NOS DA FACEBOOK
        $token = 'EAAJON8eu5LUBAAkN5uFLWFOJ3ixZCM5GYXInSSGmpsmcHSiby7u2ORxf5zRJJq5Gm2xDJ0D54dZAUYQyHQHFtZCJQ8hZAdSOsHVBxlIwJ9ou5CCUnXQptAYCUZA7btZCsG7TeuH6ennM3pbob1hRfvWcKAw6AfiZCyaYfEZAUt5i3rt1JeZBVK8uqx4Qjqu8jr1lUVZCH2KGALxQZDZD';
        //NUESTRO TELEFONO
        $telefono = '527121461514';
        //IDENTIFICADOR DE NÚMERO DE TELÉFONO
        $telefonoID = '107197942400428';
        //URL A DONDE SE MANDARA EL MENSAJE
        $url = 'https://graph.facebook.com/v17.0/' . $telefonoID . '/messages';
        //CONFIGURACION DEL MENSAJE
        $mensaje = '' . '{' . '"messaging_product": "whatsapp", ' . '"recipient_type": "individual",' . '"to": "' . $telefono . '", ' . '"type": "text", ' . '"text": ' . '{' . '     "body":"' . $enviado . '",' . '     "preview_url": true, ' . '} ' . '}';
        //DECLARAMOS LAS CABECERAS
        $header = ['Authorization: Bearer ' . $token, 'Content-Type: application/json'];
        //INICIAMOS EL CURL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $mensaje);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //OBTENEMOS LA RESPUESTA DEL ENVIO DE INFORMACION
        $response = json_decode(curl_exec($curl), true);
        //OBTENEMOS EL CODIGO DE LA RESPUESTA
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //CERRAMOS EL CURL
        curl_close($curl);

        //INSERTAMOS LOS REGISTROS DEL ENVIO DEL WHATSAPP
        $sql = 'INSERT INTO registro ' . '(mensaje_recibido    ,mensaje_enviado   ,id_wa        ,timestamp_wa        ,     telefono_wa) VALUES ' . "('" . $recibido . "' ,'" . $enviado . "','" . $idWA . "','" . $timestamp . "','" . $telefonoCliente . "');";
        $conn->query($sql);
        $conn->close();
    }
}
