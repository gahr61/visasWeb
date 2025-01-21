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
                        <div class="container flex justify-content-center" style="padding:5px;">
                            <div class="col-lg-8">
                                <div class="container">
                                    <div class="col-lg-12 mb-2">
                                        <label><b>Folio: </b></label><span>{{$data['body']['sale']['folio']}}</span>
                                    </div>
                                    <div class="col-lg-12 mb-2">
                                        <label><b>Nombre: </b></label><span>{{$data['body']['sale']['fullName']}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p style="text-align:justify;">
                            Ha recibido la ficha de pago para trámite de {{$data['body']['procedure_type']}} realice el pago lo antes posible
                            para continuar con el proceso.
                            <br/>
                            Una vez que haya realizado el pago, debe esperar 24 horas para que el pago se refleje en nuestro sistema o puede confirmar su pago ingresando a 
                        </p>
                        <br/>
                        <p style="text-align:justify;">
                            <a href="https://visas-premier.com/procedures/confirm">https://visas-premier.com/procedures/confirm<a>.
                        </p>
                        <br/>
                        <p style="text-align:justify">
                            Pasadas las 24 Hrs. debe ponerse en contacto con nosotros para confirmar con el trámite.
                        </p>
                        <hr/>                        
                        <div class="mt-2">                            
                            <h3 class="center">
                                Si tienes dudas envianos un correo electrónico a tramites@visas-premier.com indicando en 
                                el asunto tu numero de folio ó envianos por whatsapp al número 
                                <a href="https://wa.me/14441431560" target="_blank">4441431560</a>
                            </h3>
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