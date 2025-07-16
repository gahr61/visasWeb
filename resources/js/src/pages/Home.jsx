import { useEffect,  useState } from "react";
import { Button, Col, Divider, Grid, Progress, Row } from "rsuite";
import $ from 'jquery';
import Slider from "../components/Slider";
import {smile} from 'react-icons-kit/icomoon/smile';
import Icon from "react-icons-kit";
import {clockO} from 'react-icons-kit/fa/clockO';
import {lock} from 'react-icons-kit/fa/lock';
import {userCircleO} from 'react-icons-kit/fa/userCircleO'
import { useNavigate } from "react-router-dom";
import { findInStorage } from "../libs/functions";
import { FaClock, FaLock, FaRegFaceSmile } from "react-icons/fa6";
import { FaUserCircle } from "react-icons/fa";

const Home = ({quotes})=>{
    const navigate = useNavigate();
    const [honestidad, setHonestidad] = useState(0);

    const getWidthCard = ()=>{
        let height = $('.red-card').height();
        let infoContainer = $('.info-container-img').height();
        let percentages = $('.percentages').height();
        let person = $('.person-main').height();

        $('.blue-card').height(height);
        $('.info-container').height(infoContainer);
        $('.info-tramites').height(percentages+1);
        $('.person').height(person+1);
    };

    window.addEventListener('resize', ()=>{
        getWidthCard();
    });

    var isInViewport = function(elem) {
        var distance = elem.getBoundingClientRect();
        return (
            distance.top >= 0 &&
            distance.left >= 0 &&
            distance.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            distance.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
     };
    // read the link on how above code works
    const setPercentages = ()=>{
        var findMe = document.querySelectorAll('.percentages');
    
        let i = 0;
        function increment(){
            setTimeout(()=>{
                while(i<=100){                    
                    i++;
                    setHonestidad(i);
                    increment();
                }
            }, 500);
        }

        window.addEventListener('scroll', function(event) {
    
        // add event on scroll
            findMe.forEach(element => {            
                if (isInViewport(element)) {
                    if(honestidad <= 100){
                        increment();
                    }
                }
            });
        }, false);
    }
      
    useEffect(()=>{
        setTimeout(()=>{
            getWidthCard();
            setPercentages();
        },300);
        
    },[]);

    
    useEffect(()=>{
        let tmpClientVar = findInStorage('clients');
        if(tmpClientVar !== ''){
            sessionStorage.removeItem(tmpClientVar);
        }
        
    },[]);

    return(
        <>   
            <Slider quotes={quotes} />
            
            <Grid fluid>
                <Col xs={24} md={20} mdOffset={2} lg={16} lgOffset={4} className="mt-4 mb-4">
                    <Grid fluid className="mb-2">
                        <Col xs={24} md={8} lg={8}>
                            <div className="rounded shadow blue-card">
                                <Grid fluid>
                                    <Col xs={24} md={8} className="p-2">
                                        <img src="/images/services/passport.png" className="img-fluid"/>
                                    </Col>
                                </Grid>
                                <Grid fluid>
                                    <Col xs={24} className="p-2">
                                        <h5>Gestión de trámites de pasaporte</h5>
                                        <p style={{textAlign:'justify'}}>
                                            Tramita tu permiso eTA con nosotros. Llama a nuestro equipo para más información.
                                        </p>
                                    </Col>
                                </Grid>
                            </div>                            
                        </Col>
                        <Col xs={24} md={8} lg={8}>
                            <div className="rounded shadow red-card">
                                <Grid fluid>
                                    <Col xs={24} md={8} className="p-2">
                                        <img src="/images/services/visa.png" className="img-fluid"/>
                                    </Col>
                                </Grid>
                                <Grid fluid>
                                    <Col xs={24} className="p-2">
                                        <h5>Trámite de visas y permisos fronterisos </h5>
                                        <p style={{textAlign:'justify'}}>
                                            Tramita tu visa y permiso fronterizo I-94 con nosotros. Llama a nuestro equipo para más información.
                                        </p>
                                    </Col>
                                </Grid>            
                            </div>      
                        </Col>
                        <Col xs={24} md={8} lg={8}>
                            <div className="rounded shadow blue-card">
                                <Grid fluid>
                                    <Col xs={24} md={8} className="p-2">
                                        <img src="/images/services/visa.png" className="img-fluid"/>
                                    </Col>
                                </Grid>
                                <Grid fluid>
                                    <Col xs={24} className="p-2">
                                        <h5>Servicios de transporte</h5>
                                        <p style={{textAlign:'justify'}}>
                                            Tramita tu documento oficial certificado con nosotros. Llama a nuestro equipo para más información.
                                        </p>
                                    </Col>
                                </Grid>            
                            </div>                            
                        </Col>
                    </Grid>

                    <Divider />
                    <Grid fluid className="mt-5 mb-5">
                        <Grid fluid>
                            <Col xs={24} md={12} lg={6}>
                                <div className="row justify-content-center">
                                    <div className="col-12 col-md-6 text-center valores">
                                        <div className="row justify-content-center align-items-center" style={{height:'100%'}}>
                                            <div className="col-12">
                                                <FaRegFaceSmile size={55} style={{color:'#fff'}} />                                                
                                            </div>                                            
                                        </div>
                                    </div>                                    
                                </div>
                                <div className="text-center mt-4">
                                    <h4>Atención profesional</h4>
                                </div>
                            </Col>
                            <Col xs={24} md={12} lg={6}>
                                <div className="row justify-content-center">
                                    <div className="col-12 col-md-6 text-center valores">
                                        <div className="row justify-content-center align-items-center" style={{height:'100%'}}>
                                            <div className="col-12">
                                                <FaClock size={50} style={{color:'#fff'}} />
                                            </div>                                            
                                        </div>
                                    </div>                                    
                                </div>
                                <div className="text-center mt-4">
                                    <h4>Ahorra tiempo</h4>
                                </div>
                            </Col>
                            <Col xs={24} md={12} lg={6}>
                                <div className="row justify-content-center">
                                    <div className="col-12 col-md-6 text-center valores">
                                        <div className="row justify-content-center align-items-center" style={{height:'100%'}}>
                                            <div className="col-12">
                                                <FaLock size={50} style={{color:'#fff'}} />
                                            </div>                                            
                                        </div>
                                    </div>                                    
                                </div>
                                <div className="text-center mt-4">
                                    <h4>Servicio seguro</h4>
                                </div>
                            </Col>
                            <Col xs={24} md={12} lg={6}>
                                <div className="row justify-content-center">
                                    <div className="col-12 col-md-6 text-center valores">
                                        <div className="row justify-content-center align-items-center" style={{height:'100%'}}>
                                            <div className="col-12">
                                                <FaUserCircle  size={55} style={{color:'#fff'}} />
                                            </div>                                            
                                        </div>
                                    </div>                                    
                                </div>
                                <div className="text-center mt-4">
                                    <h4>Asesoría y Soporte</h4>
                                </div>
                            </Col>
                        </Grid>
                    </Grid>
                    
                    <Grid fluid>
                        <div className="row justify-content-center align-items-center tramites rounded" >
                            <div className="col-12 text-center m-5 p-4">
                                <h1>Trámites</h1>
                                <h5>Te apoyamos con tu trámite migratorio de forma rápida y segura con el apoyo de todo un equipo de profesionales.</h5>
                            </div>
                            
                        </div>
                    </Grid>

                    <Grid fluid>
                        <div className="row justify-content-center align-items-center consultoria rounded" >
                            <div className="col-12 text-center m-5 p-4">
                                <h1>Consultoría Migratoria Profesional</h1>
                                <h5>No pongas en riesgo la aceptación de tus trámites. Con ayuda del equipo de profesionales de Visas Servicios llevarás con éxito todos tus trámites migratorios.</h5>
                            </div>
                        </div>
                    </Grid>

                    <Grid fluid style={{backgroundColor:'#eef1f5'}}>
                        <Col xs={24} md={12} lg={12} xl={12}>
                            <div className="info p-3 mt-3 mb-3 info-container">
                                <h4 className="m-3">Nos enfocamos en la completa satisfacción de nuestros clientes</h4>
                                <Row className="info">
                                    <Col xs={24} xl={8}>
                                        <div className="row justify-content-center align-items-center height-content pt-3 pb-3">
                                            <div className="col-12 col-md-12 col-lg-10 text-center">
                                                <h1>89%</h1>
                                                <h4>Trámites aprobados</h4>
                                            </div>
                                        </div>
                                    </Col>
                                    <Col xs={24} xl={8}>
                                        <div className="row justify-content-center align-items-center rounded height-content blue pt-3 pb-3">
                                            <div className="col-12 col-md-10 text-center">
                                                <h1>5000+</h1>
                                                <h4>Clientes satisfechos</h4>
                                            </div>
                                        </div>
                                    </Col>
                                    <Col xs={24} xl={8}>
                                        <div className="row justify-content-center align-items-center height-content pt-3 pb-3">
                                            <div className="col-12 col-md-10 text-center">
                                                <h1>15+</h1>
                                                <h4>Años de experiencia</h4>
                                            </div>
                                        </div>
                                    </Col>
                                </Row>
                            </div>
                            
                        </Col>
                        <Col xs={24} md={12} lg={12}>
                            <div className="info p-3 mt-3 mb-3 info-container-img">
                                <Row>
                                    <Col xs={24} xl={12} className="p-2">
                                        <h4>Visita nuestras oficinas ó realiza tu trámite a distancia</h4>
                                        <p style={{textAlign:'justify'}}>
                                            Visas Premier tiene como sede la ciudad de San Luis Potosí. Sin embargo si te encuentras en otra ciudad o estado 
                                            ¡podemos gestionar tu trámite a distancia para tu mayor comodidad! Llama a nuestro equipo y recibe la mejor asesoría.
                                        </p>
                                    </Col>
                                    <Col xs={24} xl={12} className="p-2">
                                        <img src="images/oficina.webp" className="img-fluid" alt="image" />
                                    </Col>
                                </Row>
                            </div>
                        </Col>
                        <Col xs={24} sm={12} md={12} lg={12} xl={6}>
                            <div className="info p-3 mt-3 mb-3 percentages">
                                <h4 className="pt-4 pb-4">Valores</h4>
                                <div className="mb-2">
                                    <label>Honestidad</label>
                                    <Progress.Line percent={honestidad} showInfo={false} strokeColor="var(--header-primary)" />
                                </div>
                                <div className="mb-2">
                                    <label>Colaboración</label>
                                    <Progress.Line percent={honestidad} showInfo={false} strokeColor="var(--header-primary)" />
                                </div>
                                <div className="mb-2">
                                    <label>Orientación al cliente</label>
                                    <Progress.Line percent={honestidad} showInfo={false} strokeColor="var(--header-primary)" />
                                </div>
                                <div className="mb-2">
                                    <label>Calidad</label>
                                    <Progress.Line percent={honestidad} showInfo={false} strokeColor="var(--header-primary)" />
                                </div>
                            </div>
                        </Col>
                        <Col xs={24} sm={12} md={12} lg={12} xl={6}>
                            <div className="info p-3 mt-3 mb-3 info-tramites">
                                <div className="p-2 height-content">
                                    <div className="row justify-content-center align-items-center height-content">
                                        <div className="col-12 col-sm-10 col-md-10">
                                            <img src="/images/logo.jpg" className="img-fluid" alt="logo" />
                                        </div>
                                    </div>                                    
                                </div>
                                <div className="mt-2 text">
                                    <label>!Tu mejor aliado en trámites migratorios¡</label>
                                </div>
                            </div>
                        </Col>
                        <Col xs={24} md={12} lg={12} xl={6}>
                            <div className="info p-3 mt-3 mb-3 info-tramites">
                                <div className="p-2 height-content">
                                    <div className="row justify-content-center align-items-center height-content">
                                        <div className="col-12 col-md-10">
                                            <p style={{textAlign:'justify'}}>Nuestro equipo está listo para atenderte! En Visas Servicios estaremos encantados de poder ayudarte. Llama ahora o agenda una cita con tu agente especializado.</p>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                        </Col>
                        <Col xs={24} md={12} lg={12} xl={6}>
                            <div className="info p-3 mt-3 mb-3 info-tramites info-contacto">
                                <div className="p-2 height-content">
                                    <div className="row justify-content-center align-items-center height-content">
                                        <div className="col-12 col-md-10">
                                            <h6>¿Tienes dudas?</h6>
                                            <p style={{textAlign:'justify'}}>No olvides que puedes llamarnos en cualquier momento y recibir ayuda de profesionales en trámites migratorios.</p>
                                            <div className="row">
                                                <div className="col-12 col-md-6">
                                                    <Button appearance="ghost" onClick={()=>navigate('/contact')}>Contactar</Button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                        </Col>
                    </Grid>

                    <Grid fluid className="mt-4 persons">
                        <Col xs={24}>
                            <div className="row mt-3">                                
                                <div className="col-12 col-md-4 text-center p-2">
                                    <img src="/images/avatar/Avatar-1.png" alt="person" className="img-fluid" />
                                </div>
                                    
                                <div className="col-12 col-md-8">
                                    <div className="row justify-content-center align-items-center height-content">
                                        <div className="col-12 col-md-10">
                                            <h5>Me ayudaron en mi trámite para obtener mi Visa Americana por primera vez. Definitivamente los mejores.</h5>
                                            <h6>Eduardo Rodríguez</h6>
                                            <h6>Visas Premier</h6>
                                        </div>                                        
                                    </div>
                                </div>                                
                            </div>
                            <Divider />
                        </Col>
                        
                        <Col xs={24}>
                            <div className="row mt-3">                                
                                <div className="col-12 col-md-4 text-center p-2">
                                    <img src="/images/avatar/Avatar-2.png" alt="person" className="img-fluid" />
                                </div>
                                    
                                <div className="col-12 col-md-8">
                                    <div className="row justify-content-center align-items-center height-content">
                                        <div className="col-12 col-md-10">
                                            <h5>La renovación de pasaporte más rápida en mi experiencia. ¡Gracias Visa Premier!</h5>
                                            <h6>Alejandro Flores</h6>
                                            <h6>Visas Premier</h6>
                                        </div>                                        
                                    </div>
                                </div>                                
                            </div>
                            <Divider />
                        </Col>
                        
                        <Col xs={24}>
                            <div className="row mt-3">                                
                                <div className="col-12 col-md-4 text-center p-2">
                                    <img src="/images/avatar/Avatar-7.png" alt="person" className="img-fluid" />
                                </div>
                                    
                                <div className="col-12 col-md-8">
                                    <div className="row justify-content-center align-items-center height-content">
                                        <div className="col-12 col-md-10">
                                            <h5>No tenia muy claro cómo obtener mi visa y en Visas Premier me ayudaron en todos los trámites sin ninguna complicación.</h5>
                                            <h6>Mónica González</h6>
                                            <h6>Visas Premier</h6>
                                        </div>                                        
                                    </div>
                                </div>                                
                            </div>
                            <Divider />
                        </Col>
                        
                        <Col xs={24}>
                            <div className="row mt-3">                                
                                <div className="col-12 col-md-4 text-center p-2">
                                    <img src="/images/avatar/Avatar-22.png" alt="person" className="img-fluid" />
                                </div>
                                    
                                <div className="col-12 col-md-8">
                                    <div className="row justify-content-center align-items-center height-content">
                                        <div className="col-12 col-md-10">
                                            <h5>¡Pude obtener mi Visa de trabajo! Estoy muy emocionado por esta oportunidad. Ampliamente recomendados.</h5>
                                            <h6>Eduardo Rodríguez</h6>
                                            <h6>Visas Premier</h6>
                                        </div>                                        
                                    </div>
                                </div>                                
                            </div>
                            <Divider />
                        </Col>
                    </Grid>
                </Col>
            </Grid>

        </>
    )
}

export default Home;