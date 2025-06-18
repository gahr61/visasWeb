<html>
    <head>
        <style type="text/css">
            .container{
                width: 100%;
                margin-right: auto;
                margin-left: auto;
            }

            .justify-content-center {
                left:15%;
                justify-content: center;
            }
            
            .flex{
                display:flex;
            }

            .border{
                border:1px solid;
            }

            .col-lg-1 {
                flex: 0 0 auto;
                width: 8.33333333%;
            }
            .col-lg-2 {
                flex: 0 0 auto;
                width: 16.66666667%;
            }
            .col-lg-3 {
                flex: 0 0 auto;
                width: 25%;
            }
            .col-lg-4 {
                flex: 0 0 auto;
                width: 33.33333333%;
            }
            .col-lg-5 {
                flex: 0 0 auto;
                width: 41.66666667%;
            }
            .col-lg-6 {
                flex: 0 0 auto;
                width: 50%;
            }
            .col-lg-7 {
                flex: 0 0 auto;
                width: 58.33333333%;
            }
            .col-lg-8 {
                flex: 0 0 auto;
                width: 66.66666667%;
            }
            .col-lg-9 {
                flex: 0 0 auto;
                width: 75%;
            }
            .col-lg-10 {
                flex: 0 0 auto;
                width: 83.33333333%;
            }
            .col-lg-11 {
                flex: 0 0 auto;
                width: 91.66666667%;
            }
            .col-lg-12 {
                flex: 0 0 auto;
                width: 100%;
            }

            .mt-2 {
                margin-top: 0.5rem !important;
            }
            .mb-2 {
                margin-bottom: 0.5rem !important;
            }



            img{
                width:100%;
            }
            body,
            .text{
                font-family: sans-serif;
            }
            .title{
                text-align: center;
                color:#053067;
                margin-bottom: 0;
                margin-top:8px;
            }
            .subtitle{
                text-align: center;
                margin-top:0;
                color:#0a437f;
            }
            .content{
                font-weight: 100;   
            }
            .center{
                text-align:center;
            }
            .number{
                color:#f01134;
                font-weight: bold;
            }
           
        </style>
    </head>
    <body>
        <div class="container flex justify-content-center">
            <div class="col-lg-8 ">
                <div class="container flex justify-content-center col-lg-12 mb-2">
                    <div class="col-lg-3">
                        <img src="https://visas-premier.com/logo.png" />
                    </div>
                </div>
                <div class="container">
                    <div class="col-lg-12 center">
                        <h1 class="text title">Visas Premier</h1>    
                    </div>   
                    <div class="col-lg-12">
                        <h3 class="subtitle center">Gracias por confiar en visas-premier.com!</h3>
                        <p style="text-align:justify;">
                            En este momento tu trámite ha sido registrado, pero está sujeto a confirmación con Consulado (Si el trámite es visa) 
                            o con SRE (Secretaria de Relaciones Exteriores) (si el trámite es pasaporte), 
                            <b> Espera a que nos contactemos contigo notificando el estatus de tu trámite.</b>
                        </p>
                        <hr/>
                        <div class="mt-2">                            
                            <h4 style="padding:5px;" class="subtitle center">Información de trámite</h4>
                            <div class="container flex justify-content-center" style="padding:5px;">
                                <div class="col-lg-8">
                                    <div class="container">
                                        <div class="col-lg-12 mb-2">
                                            <label><b>Fecha: </b></label><span>{{$data['body']['sale']['date']}}</span>
                                        </div>
                                        <div class="col-lg-12 mb-2">
                                            <label><b>Folio: </b></label><span>{{$data['body']['sale']['folio']}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="container flex justify-content-center" style="padding:5px">
                                <p>Te damos la bienvenida a nuestra plataforma. A continuación te compartimos tus datos de acceso temporal.</p>
                                <hr/>
                                <p><b>Usuario: </b> {{$data['body']['user']['email']}}</p>
                                <p><b>Contraseña: </b> @cc350.T3mp.2025</p>

                                <hr/>
                            </div>
                            <h4 style="padding:0px 0px 0px 5px;" class="subtitle center">Clientes</h4>
                            <div class="container flex justify-content-center" style="padding:5px;">
                                <div class="col-lg-8">
                                    <div class="container">
                                        <div class="col-lg-12">
                                            @foreach($data['body']['clients'] as $c)                                                    
                                                <div class="col-lg-12">                                                  
                                                    <label><b>Nombre: </b></label>
                                                    <span>
                                                        {{ $c['names'].' '.$c['lastname1'].(is_null($c['lastname2']) ? '' : ' '.$c['lastname2']) }}
                                                    </span>                                                    
                                                </div>
                                                <div class="col-lg-12">                                                    
                                                    <label><b>Trámite: </b></label>
                                                    <span>
                                                        {{
                                                            $c['type'].' - '.
                                                            $c['age_type'].' - '.
                                                            ($c['visa_type'] != null ? $c['visa_type'].' - ' : '').
                                                            $c['subtype'].
                                                            (($c['option_type'] != null && $c['option_type'] != '') ? ' - '.$data['cuerpo']['option_type'] : '')
                                                        }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-12">
                                                    <label><b>Documentos requeridos:</b></label>
                                                   <!-- <span>{{$c['documents']}}</span>-->
                                                </div>
                                                <hr/>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                        
                        <hr/>
                        <div class="mt-2">                            
                            <h4 style="padding:5px; color:red;" class="subtitle center">Información importante</h4>
                            <h3 class="center">Es importante que al acudir a nuestras oficinas presente la documentación mostrada arriba</h3>
                            <h3 class="center">
                                O si lo prefieres puedes enviarnos una foto de tu documentación al correo tramites@visas-premier.com indicando en 
                                el asunto tu número de folio o envianos por Whatsapp al número <a href="https://wa.me/14441431560" target="_blank">4441431560</a></h3>
                        </div>
                        <div class="mt-2">                            
                            <h3 class="center">Si tienes alguna duda puedes acudir a nuestas oficinas</h3>
                            <div class="container flex justify-content-center" style="padding:5px;">
                                <div class="col-lg-6">
                                    <div class="container center">
                                        <h4>Oficina 1</h4>
                                        <h5>Rio Nazas #195, Col. Los Filtros</h5>
                                        <h5>San Luis Potosí, México</h5>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="container center">
                                        <h4>Oficina 2</h4>
                                        <h5>Río Pánuco No 11, Col Los Filtros</h5>
                                        <h5>San Luis Potosí, México</h5>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>