import React, { useEffect } from 'react';
import { Col, Grid } from 'rsuite';
import $ from 'jquery';
import {check} from 'react-icons-kit/fa/check';
import {lightbulbO} from 'react-icons-kit/fa/lightbulbO';
import Icon from 'react-icons-kit';
import {lock} from 'react-icons-kit/fa/lock';
import { FaCheck, FaLightbulb, FaLock } from 'react-icons/fa6';

const About = ()=>{
    const getWidthCard = ()=>{
        let info = $('.page-about-img').height();

        $('.page-about-info').height(info);
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
            <Grid fluid>
                <Col xs={24} md={20} mdOffset={2} lg={16} lgOffset={4} className="mt-4 mb-4">
                    <Grid fluid className='p-2'>
                        <Col xs={24} sm={12} md={12} className="page-about-info mb-1">
                            <div className='rounded shadow row justify-content-center align-items-center height-content'>
                                <div className='col-12 col-md-10'>
                                    <p style={{textAlign:'justify'}}>
                                        El equipo de Visas Premier ofrece servicios en consultoría migratoria así como servicios de transporte como traslado al consulado y renta de vehículos.
                                    </p>
                                </div>                                
                            </div>
                        </Col>
                        <Col xs={24} sm={12} md={12}>
                            <div className='page-about-img mb-3'>
                                <img src='/images/about.jpeg' alt="about" className='img-fluid' />
                            </div>
                            
                        </Col>
                    </Grid>

                    <Grid fluid className="mt-5 mb-5 p-5 page-about-marks">
                        <Grid fluid>
                            <Col xs={24} lg={8} className="p-2">
                                <div className="row justify-content-center">
                                    <div className="col-12 col-md-6 text-center valores">
                                        <div className="row justify-content-center align-items-center" style={{height:'100%'}}>
                                            <div className="col-12">
                                                <FaCheck size={55} style={{color:'#fff'}} />
                                            </div>                                            
                                        </div>
                                    </div>                                    
                                </div>
                                <div className="text-center mt-4">
                                    <h4>Agilizamos tu trámite</h4>
                                    <h6>
                                        Evita complicaciones con el papeleo y de más. En Visas Premier te ayudaremos a realizar trámites exitosos sin complicaciones
                                    </h6>
                                </div>
                            </Col>
                            <Col xs={24} lg={8} className="p-2">
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
                                    <h4>Confidencialidad</h4>
                                    <h6>
                                        Conocemos la importancia de la información, por ello ofrecemos servicios 100% seguros y confidenciales
                                    </h6>
                                </div>
                            </Col>
                            <Col xs={24} lg={8} className="p-2">
                                <div className="row justify-content-center">
                                    <div className="col-12 col-md-6 text-center valores">
                                        <div className="row justify-content-center align-items-center" style={{height:'100%'}}>
                                            <div className="col-12">
                                                <FaLightbulb size={50} style={{color:'#fff'}} />
                                            </div>                                            
                                        </div>
                                    </div>                                    
                                </div>
                                <div className="text-center mt-4">
                                    <h4>Servicio inteligente</h4>
                                    <h6>
                                        Brindamos atención a los detalles desde el comienzo de tu trámite para evitar las complicaciones que puedan presentarse en el camino.
                                    </h6>
                                </div>
                            </Col>
                            
                        </Grid>
                    </Grid>
                </Col>
            </Grid>
        </>   
    )
}

export default About;