import React, {useEffect} from "react";
import { Button, Col, Divider, Grid } from "rsuite";
import $ from 'jquery';
import {usd} from 'react-icons-kit/fa/usd';
import {checkCircle} from 'react-icons-kit/fa/checkCircle';
import {users} from 'react-icons-kit/fa/users';
import {handshakeO} from 'react-icons-kit/fa/handshakeO';
import Icon from "react-icons-kit";
import { useNavigate } from "react-router-dom";
import { FaDollarSign, FaHandshake, FaUsers } from "react-icons/fa6";
import { FaCheckCircle } from "react-icons/fa";

const Services = ()=>{
    const navigate = useNavigate();
    const getWidthCard = ()=>{
        let height = $('.red-card').height();
        let info = $('.service-info').height();

        $('.service-years').height(info);
        $('.blue-card').height(height);
    };

    window.addEventListener('resize', ()=>{
        getWidthCard();
    });

    useEffect(()=>{
        setTimeout(()=>{
            getWidthCard();
        }, 500);
    },[]);

    return(
        <>
            <Grid fluid className="page-services">
                <Col xs={24} md={20} mdOffset={2} lg={16} lgOffset={4} className="mt-4 mb-4">
                    <Grid fluid className="m-2">
                        <Col xs={24} md={12} lg={12}>
                            <Grid fluid className="m-2 p-2 service-info">
                                <Col xs={24} className="mb-3">
                                    <h1>Nuestros servicios</h1>
                                </Col>
                                <Col xs={24}>
                                    <p className="service-title">
                                        Te apoyamos a lograr con éxito los trámites migratorios necesarios para ti, ya sean por trabajo o vacaciones. Visas Premier es tu mejor opción.
                                    </p>
                                    <p>
                                        Contamos con más de 15 años de experiencia ayudando a las personas a llevar a cabo con éxito los trámites migratorios necesarios para cumplir sus sueños.
                                    </p>
                                    <p>
                                        Nuestra amplio conocimiento te permitirá llevar tus trámites de manera fácil y segura, ahorrando así tiempo, dinero y esfuerzo.
                                    </p>
                                    <p>
                                        Contacta con nuestros agentes y agenda tu cita hoy mismo.
                                    </p>
                                </Col>
                            </Grid>
                        </Col>
                        <Col xs={24} md={12} lg={12}>
                            <Grid fluid className="m-2 p-2">
                                <div className="service-years">
                                    <div className="row justify-content-center align-items-center height-content">
                                        <div className="col-12 text-center">
                                            <h1>+15</h1>
                                            <h2>Años de experiencia</h2>
                                        </div>
                                    </div>
                                </div>
                            </Grid>
                        </Col>
                    </Grid>

                    <Divider />

                    <Grid fluid className="m-2">
                        <Col xs={24} sm={12} md={12} lg={12} xl={6}>
                            <div className="row justify-content-center">
                                <div className="col-12 col-sm-8 rounded-circle service-icon">
                                    <div className="row justify-content-center align-items-center height-content">
                                        <div className="col-11 col-sm-11 rounded-circle service-icon-content">
                                            <div className="row justify-content-center align-items-center height-content">
                                                <div className="col-10 col-sm-10 text-center">
                                                    <FaDollarSign size={50} />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-12 text-center mt-3">
                                    <h5>Ahorra tiempo y dinero</h5>
                                </div>
                            </div>
                        </Col> 
                        <Col xs={24} sm={12} md={12} lg={12} xl={6}>
                            <div className="row justify-content-center">
                                <div className="col-12 col-sm-8 rounded-circle service-icon">
                                    <div className="row justify-content-center align-items-center height-content">
                                        <div className="col-11 col-sm-11 rounded-circle service-icon-content">
                                            <div className="row justify-content-center align-items-center height-content">
                                                <div className="col-10 col-sm-10 text-center">
                                                    <FaCheckCircle size={50} />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-12 text-center mt-3">
                                    <h5>Verificación de requisitos</h5>
                                </div>
                            </div>
                        </Col> 
                        <Col xs={24} sm={12} md={12} lg={12} xl={6}>
                            <div className="row justify-content-center">
                                <div className="col-12 col-sm-8 rounded-circle service-icon">
                                    <div className="row justify-content-center align-items-center height-content">
                                        <div className="col-11 col-sm-11 rounded-circle service-icon-content">
                                            <div className="row justify-content-center align-items-center height-content">
                                                <div className="col-10 col-sm-10 text-center">
                                                    <FaUsers size={50} />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-12 text-center mt-3">
                                    <h5>Soporte personalizado</h5>
                                </div>
                            </div>
                        </Col> 
                        <Col xs={24} sm={12} md={12} lg={12} xl={6}>
                            <div className="row justify-content-center">
                                <div className="col-12 col-sm-8 rounded-circle service-icon">
                                    <div className="row justify-content-center align-items-center height-content">
                                        <div className="col-11 col-sm-11 rounded-circle service-icon-content">
                                            <div className="row justify-content-center align-items-center height-content">
                                                <div className="col-10 col-sm-10 text-center">
                                                    <FaHandshake size={50} />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-12 text-center mt-3">
                                    <h5>Seriedad y confianza</h5>
                                </div>
                            </div>
                        </Col> 
                    </Grid>

                    <Divider />

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
                    
                    <Grid fluid className="mt-4 mb-4">
                        <Col xs={24} sm={12} md={8} lg={6} xl={4} lgOffset={10} mdOffset={8} smOffset={6}>
                            <Button className="full-width btn-cita" onClick={()=>navigate('/contact')}>Contactar ahora</Button>
                        </Col>
                    </Grid>
                </Col>
            </Grid>
        </>
    )
}

export default Services;