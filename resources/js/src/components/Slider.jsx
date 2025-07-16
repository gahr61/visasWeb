import 'animate.css';
import React, { useState, useEffect } from "react";
import { Button, Carousel, Col, Grid } from "rsuite";
import $ from 'jquery';
import { useNavigate } from 'react-router-dom';

let interval;

const Slider = ({quotes})=>{
    const navigate = useNavigate();

    const actionSlide = (index, el, animate, option)=>{
        let slide = $('.slide').eq(index);
        let element = slide.find(el);

        if(option === 'add'){
            element.css('visibility', '');
            element.addClass(animate);

        }else if(option === 'hide'){
            element.css('visibility', 'hidden');

        }else{
            element.removeClass(animate);
        } 
    }

    const addAnimation = (i, animation, btns)=>{
        if(i === 0){
            setTimeout(()=>{
                actionSlide(i, 'h6', 'animate__animated '+animation, 'add');
            }, 500);
        }

        setTimeout(()=>{
            actionSlide(i, 'h1', 'animate__animated '+animation, 'add');
        }, 600);
        setTimeout(()=>{
            actionSlide(i, 'h5', 'animate__animated '+animation, 'add');
        }, 900);

        btns.forEach((btn, j)=>{
            setTimeout(()=>{
                actionSlide(i, btn.class, 'animate__animated '+btn.animation, 'add');
            }, j === 0 ? 1200 : 1500);
        })
    }

    const removeAnimation = (i, animation, btns)=>{
        if(i === 0){
            actionSlide(i, 'h6', 'animate__animated '+animation, 'remove');
        }
        
        actionSlide(i, 'h1', 'animate__animated '+animation, 'remove');
        actionSlide(i, 'h5', 'animate__animated '+animation, 'remove');

        btns.forEach((btn)=>{
            actionSlide(i, btn.class, 'animate__animated '+btn.animation, 'remove');    
        });
    }

    const onStart = (i)=>{
        for(let x = 0; x < 4; x++){
            if(x !== i){
                actionSlide(x, 'h6', '', 'hide');
                actionSlide(x, 'h1', '', 'hide');
                actionSlide(x, 'h5', '', 'hide');
                actionSlide(x, '.welcome-btn-service', '', 'hide');
                actionSlide(x, '.welcome-btn-contact', '', 'hide');
                actionSlide(x, '.service-btn-cita', '', 'hide');
                actionSlide(x, '.citas-btn-cita', '', 'hide');
                actionSlide(x, '.permiso-btn-cita', '', 'hide');
            }    
            
        }   

        if(i === 0){
            addAnimation(i, 'animate__fadeInLeft', [{class:'.welcome-btn-service', animation:'animate__fadeInUp'}, {class:'.welcome-btn-contact', animation:'animate__fadeInUp'}]);
        }

        if(i === 1){
            addAnimation(i, 'animate__fadeInRight', [{class:'.service-btn-cita', animation:'animate__fadeInUp'}]);
        }

        if(i === 2){
            addAnimation(i, 'animate__fadeInDown', [{class:'.citas-btn-cita', animation:'animate__fadeInUp'}]);
        }

        if(i === 3){
            addAnimation(i, 'animate__zoomInLeft', [{class:'.permiso-btn-cita', animation:'animate__zoomInUp'}]);
        }

    }

    const onEnd = (i)=>{        
        
        if(i === 0){
            removeAnimation(i, 'animate__fadeInLeft', [{class:'.welcome-btn-service', animation:'animate__fadeInUp'}, {class:'.welcome-btn-contact', animation:'animate__fadeInUp'}]);   
        }

        if(i === 1){
            removeAnimation(i, 'animate__fadeInRight', [{class:'.service-btn-cita', animation:'animate__fadeInUp'}]);
        }

        if(i === 2){
            removeAnimation(i, 'animate__fadeInDown', [{class:'.citas-btn-cita', animation:'animate__fadeInUp'}]);
        }

        if(i === 3){
            removeAnimation(i, 'animate__zoomInLeft', [{class:'.permiso-btn-cita', animation:'animate__zoomInUp'}]);
        }
    }

    useEffect(()=>{
        addAnimation(0, 'animate__fadeInLeft', [{class:'.welcome-btn-service', animation:'animate__fadeInUp'}, {class:'.welcome-btn-contact', animation:'animate__fadeInUp'}]);
    }, []);

    const actionBtn = (url)=>{
        if(url === 'quotes'){
            quotes.current.handleShow();
        }else{
            navigate(url);
        }
        
    }

    const slide = [
        {
            welcome:'Bienvenido a Visas Premier',
            h1:'Seriedad y Confianza',
            h5:[{text:'Comiensa tu trámite hoy'}],
            btns:[
                {mainClass:'welcome-btn-service', class:'btn-services', text:'Servicios', url:'/services'},
                {mainClass:'welcome-btn-contact', class:'btn-contact', text:'Contacto', url:'/contact'},
            ],
            image:"url('/images/slide/am-flag-scaled.jpg')",
            right:false,
            visibility:false
        },
        {
            welcome:'',
            h1:'Servicios de Transporte',
            h5:[{text:'Traslado al consulado, transporte vacacional'},{text:'y de personal, renta de vehículos y más..'}],
            btns:[
                {mainClass:'service-btn-cita', class:'btn-cita', text:'Agendar cita', url:'quotes'},
            ],
            image:"url('/images/slide/transporte-slide-scaled.jpg')",
            right:true,
            visibility:false
        },
        {
            welcome:'',
            h1:'Citas de Pasaportes',
            h5:[{text:'Preparate para nuevos horizontes. '},{text:' Nuestro equipo te ayudará a neter un trámite exitoso'}],
            btns:[
                {mainClass:'citas-btn-cita', class:'btn-cita', text:'Agendar cita', url:'quotes'},
            ],
            image:"url('/images/slide/airmgff-scaled.jpg')",
            right:false,
            visibility:false
        },
        {
            welcome:'',
            h1:'Permiso ETA',
            h5:[{text:'No batalles con tus trámites.'},{text:'Recibe asesiría de expertos'}],
            btns:[
                {mainClass:'permiso-btn-cita', class:'btn-cita', text:'!Llama ahora¡'},
            ],
            image:"url('/images/slide/cnd-slide-scaled.jpg')",
            right:false,
            visibility:false
        }
    ]
    
    return(
        <Carousel autoplay={true}
            onSlideStart={(i)=> onStart(i) }
            onSlideEnd={(i)=> onEnd(i) }
            onSelect={(i)=>{
                
            }}
            className="custom-slider"
        >
            {slide.map((s, i)=>
                <div style={{backgroundImage:s.image}} className="slide" key={i}>
                    <div className={'row align-items-center '+(s.right ? 'justify-content-center' : '')} style={{height:'100%'}}>
                        <div className={'p-5 col-12 col-lg-6 col-md-8 '+(s.right ? 'offset-lg-6 offset-md-7' : 'offset-lg-1 offset-md-1')}>
                            {s.welcome !== '' &&
                                <h6 style={{visibility:'hidden'}}>{s.welcome}</h6>
                            }
                            <h1 style={{visibility:'hidden'}}>{s.h1}</h1>
                            {s.h5.map((h5, j)=>
                                <h5 key={j} style={{visibility:'hidden'}}>{h5.text}</h5>
                            )}
                            <Grid fluid className='mt-4'>
                                {s.btns.map((btn, j)=>
                                    <Col xs={12} lg={8} key={j} className={btn.mainClass} style={{visibility:'hidden'}}>
                                        <Button appearance={(s.btns.length > 1 && j === 1) ? 'ghost' : 'default'} 
                                            className={'full-width '+btn.class}
                                            onClick={(e)=>btn.url !== undefined ? actionBtn(btn.url) : e.preventDefault()}
                                        >
                                            {btn.text}
                                        </Button>
                                    </Col>
                                    
                                )} 
                            </Grid>
                        </div>
                    </div>
                </div>
            )}
        </Carousel>
    )
    
}

export default Slider;