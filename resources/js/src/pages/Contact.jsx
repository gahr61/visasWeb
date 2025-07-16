import {useEffect, useState, useRef} from "react";
import { Button, Col, Divider, Grid, Message, Row } from "rsuite";

import $ from 'jquery';
import { isValidForm, showCtrlError } from "../libs/functions";
import ReCAPTCHA from "react-google-recaptcha";
import { contactForm } from "../libs/services";
import { FaPhone } from "react-icons/fa6";

const Contact = ()=>{
    const captchaRef = useRef(null)
    const [data, setData] = useState({
        contact_name:'',
        contact_email:'',
        contact_phone:'',
        contact_message:''
    });
    const [error, setError] = useState('');
    const [message, setMessage] = useState('');

    const handleChange = (e)=>{
        let name = e.target.name;
        let value = e.target.value;

        let item = data;

        item = {
            ...item,
            [name]:value
        };

        setData(item);

        if(value !== ''){
            showCtrlError(name);
        }
    }

    const getWidthCard = ()=>{
        let height = $('.contact-phone').height();
        let info = $('.info-container').height();

        $('.info-contact').height(height+1);
    };

    window.addEventListener('resize', ()=>{
        getWidthCard();
    });

    const sendData = async ()=>{
        const token = captchaRef.current.getValue();

        if(isValidForm('div.contact-form')){
            setError('');
            if(token === ''){
                setError('Debe realizar la verificación');
            }else{
                const obj = {
                    name: data.contact_name,
                    email: data.contact_email,
                    phone: data.contact_phone,
                    message: data.contact_message
                };

                let response = await contactForm(obj);
                if(response){
                    captchaRef.current.reset();
                    setMessage('Gracias por ponerce en contacto con nosotros');
                    setData({
                        name:'',
                        email:'',
                        phone:'',
                        message:''
                    });
                }
            }
        }else{
            setError('Campos incompletos');
        }
    }

    useEffect(()=>{
        setTimeout(()=>{
            getWidthCard();
        }, 300);
    },[]);
    return(
        <Grid fluid className="page-contact">
            <Col xs={24} md={20} mdOffset={2} lg={20} xl={16} xlOffset={4} lgOffset={2} className="mt-4 mb-4">
                <Grid fluid className="m-2">
                    <Col xs={24} lg={12}>
                        <Grid fluid className="p-2">
                            <Col xs={24}>
                                <h2>Deja tus datos y un asesos se pondrá en contacto contigo en breve</h2>
                            </Col>
                            <Col xs={24} className="contact-form">
                                {error !== "" &&
                                    <Col xs={24} className="mb-2">
                                        <Message type="error">{error}</Message>
                                    </Col>
                                }

                                {message !== "" &&
                                    <Col xs={24} className="mb-2">
                                        <Message type="success">{message}</Message>
                                    </Col>
                                }
                                <div className="mb-2">
                                    <span>Nombre</span>
                                    <input className="form-control form-control-sm" name="contact_name" id="contact_name" value={data.contact_name} onChange={(e)=>handleChange(e)} required /> 
                                </div>
                                <div className="mb-2">
                                    <span>Email</span>
                                    <input type="email" className="form-control form-control-sm" name="contact_email" id="contact_email" value={data.contact_email} onChange={(e)=>handleChange(e)} required /> 
                                </div>
                                <div className="mb-2">
                                    <span>Número de teléfono</span>
                                    <input type="tel" className="form-control form-control-sm" name="contact_phone" id="contact_phone" value={data.contact_phone} onChange={(e)=>handleChange(e)} required /> 
                                </div>
                                <div className="mb-2">
                                    <span>Mensaje</span>
                                    <textarea className="form-control form-control-sm" style={{resize:'none'}} rows={4} name="contact_message" id="contact_message" value={data.contact_message} onChange={(e)=>handleChange(e)} required/> 
                                </div>
                                <div className="col-12 col-sm-6 col-md-6 col-lg-3">
                                    <Grid fluid style={{margin:5}}>
                                        <Row>
                                            <Col xs={24} sm={6}>
                                                <ReCAPTCHA
                                                    sitekey={import.meta.env.VITE_GOOGLE_KEY}
                                                    ref={captchaRef}
                                                />
                                            </Col>
                                        </Row>
                                    </Grid>

                                    <Button className="btn-services full-width" onClick={()=>sendData()}>Enviar</Button>
                                </div>
                            </Col>
                        </Grid>
                    </Col>
                    <Col xs={24} lg={12}>
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3695.4532137140727!2d-101.01803461499271!3d22.146813971875886!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x842a98d2f95b5311%3A0x594606b4dc2c63b0!2sR%C3%ADo%20Nazas%20195%2C%20Lomas%20los%20Filtros%2C%2078210%20San%20Luis%2C%20S.L.P.!5e0!3m2!1ses-419!2smx!4v1670009540489!5m2!1ses-419!2smx"  style={{border:0, width:'100%', height:300}} allowFullScreen="" loading="lazy" referrerPolicy="no-referrer-when-downgrade"></iframe>
                    </Col>
                </Grid>
                <Grid fluid className="m-2">
                    <Col xs={24}>                                
                        <Divider />
                        <p>
                            Deja tus datos en nuestro formulario y uno de nuestros asesores estará en contacto contigo a la brevedad.
                        </p>
                    </Col>
                    <Col xs={24}>
                        <Grid fluid className="m-2 p-2 info-container">                            
                            <Col xs={24} md={12} lg={12} xl={8} className="mb-3 info-contact">
                                <div className="row justify-content-center">
                                    <div className="col-12 col-md-6 text-center valores">
                                        <div className="row justify-content-center align-items-center" style={{height:'100%'}}>
                                            <div className="col-12">
                                                <FaPhone size={35} />
                                            </div>                                            
                                        </div>
                                    </div>                                    
                                </div>
                                <div className="text-center mt-4">
                                    <h4 style={{color:'#1d3664'}}>Email</h4>
                                    {/*<h6>premierservicios@outlook.com</h6>*/}
                                    <h6>contacto@visas-premier.com</h6>
                                </div>
                            </Col>
                            <Col xs={24} md={12} lg={12} xl={8} className="mb-3 info-contact">
                                <div className="row justify-content-center">
                                    <div className="col-12 col-md-6 text-center valores">
                                        <div className="row justify-content-center align-items-center" style={{height:'100%'}}>
                                            <div className="col-12">
                                                <FaPhone size={35} />
                                            </div>                                            
                                        </div>
                                    </div>                                    
                                </div>
                                <div className="text-center mt-4">
                                    <h4 style={{color:'#1d3664'}}>Oficina</h4>
                                    <h6>Rio Nazas #195, Col. Los Filtros</h6>
                                </div>
                            </Col>
                            <Col xs={24} md={12} lg={12} xl={8} className="mb-3 info-contact">
                                <div className="row justify-content-center">
                                    <div className="col-12 col-md-6 text-center valores">
                                        <div className="row justify-content-center align-items-center" style={{height:'100%'}}>
                                            <div className="col-12">
                                                <FaPhone size={35} />
                                            </div>                                            
                                        </div>
                                    </div>                                    
                                </div>
                                <div className="text-center mt-4">
                                    <h4 style={{color:'#1d3664'}}>Horario</h4>
                                    <h6>Lunes a Viernes de 8 a.m. a 4 p.m.</h6>
                                    <h6>Sábado con Previa cita</h6>
                                </div>
                            </Col>                            
                        </Grid>                        
                    </Col>
                </Grid>
            </Col>
        </Grid>
    )
}

export default Contact;