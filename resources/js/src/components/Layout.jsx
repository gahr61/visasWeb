import { useEffect, useState } from "react";
import { Outlet, useLocation, useNavigate } from "react-router-dom";
import { Button, Col, Drawer, Grid, IconButton, Nav, Navbar } from "rsuite";

import MenuIcon from '@rsuite/icons/Menu';
import Swal from "sweetalert2";
import { startSessionWeb } from "../libs/services";
import { decript, encript } from "../libs/functions";

import { FaEnvelope, FaInstagram, FaSquareFacebook } from "react-icons/fa6";
import { FaMapMarker } from "react-icons/fa";

const Layout = ({procedure})=>{
    const navigate = useNavigate();
    const location = useLocation();

    const [fullMenu, setFullMenu] = useState(true);
    const [openMenu, setOpenMenu] = useState(false);
    const [menu, setMenu] = useState([
        {title:'Inicio', active:true, url:'/'},
        {title:'Nosotros', active:false, url:'/about'},
        {title:'Servicios', active:false, url:'/services'},
        {title:'Contacto', active:false, url:'/contact'},
    ])

    const onChangeMenu = (url)=>{
        navigate(url);
    }

    const resizeWindow = ()=>{        
        let width = window.innerWidth;

        if(width <= 600){
            setFullMenu(false);
        }else{
            setFullMenu(true);
        }
    }

    useEffect(()=>{
        window.addEventListener('resize', ()=>{
            resizeWindow();   
        });

        resizeWindow();
    });

    const startSession = async ()=>{ 
        let token = decript('token');
        
        if(token === ''){
            let response = await startSessionWeb();
            if(response){
                encript('token', response.token);
            }        
        }
    }

    const openProcedure = ()=>{   
        Swal.fire({
            title:'Atención',
            //text:'Las citas para el trámite de visa se encuentran retrasadas y hay una espera de <b>1 año a 1 año y medio</b>. Desea iniciar el trámite con nosotros?',
            icon:'warning',
            html:'Las citas para el trámite de visa se encuentran retrasadas y hay una espera de 6 meses a 1 año y medio. <span style="font-size:24px;">Desea iniciar el trámite con nosotros?</span>',
            showConfirmButton	: true,
            showCancelButton	: true,
            confirmButtonText	: 'Si, Iniciar trámite',
            confirmButtonColor  : '#ce1135',
            //cancelButtonColor	: obj.colorCancel,
            cancelButtonText	: 'No, Cancelar'
        }).then((result)=>{    
            if(result.isConfirmed){
                startSession();
                navigate('/procedures/register');
            }
        });     
        
    }

    useEffect(()=>{
        let menus = menu.map((m)=>{            
            if(m.url === location.pathname){
                m.active = true;
            }else{
                m.active = false;
            }

            return m;
        });

        setOpenMenu(false);

        setMenu(menus);
    },[location])

    
    return (<>
        {/*HEADER*/}
            <Grid fluid className="header-info" >
                <Col xsHidden smHidden md={22} lg={19} mdOffset={1} lgOffset={3}>
                    <Col md={15} lg={14} className="p-2">
                        <a href="mailto:premierservicios@outlook.com"> Envíanos un correo electrónico</a>
                    </Col>
                    <Col mdOffset={2} md={3} lgOffset={4} lg={3} className="p-2 text-end">
                        <a href="https://www.facebook.com/visaspremier" target="_blank" className="brand"><FaSquareFacebook /></a>
                        <a href="https://www.instagram.com/premservslp/" target="_blank" className="brand"><FaInstagram /></a>
                        <a href="mailto:contacto@visas-premier.com" target="_blank" className="brand"><FaEnvelope /></a>                        
                    </Col>
                    <Col className="p-2" md={4} lg={3} style={{backgroundColor:'#ce1135'}}>
                        <a href="" style={{backgroundColor:'#ce1135', fontWeight:'bold', fontSize:'1em'}} onClick={()=>navigate('/contact')}>
                            <FaMapMarker /> Oficinas
                        </a>
                    </Col>
                </Col>
            </Grid>

            <Grid fluid>
                <Col md={22} mdOffset={1} lg={19} lgOffset={3}>
                    <Navbar appearance="subtle">
                        {fullMenu ?
                            <>
                                <Navbar.Brand href="#" style={{width:'15%', height:'100%'}}>
                                    <img src="/logo.png" alt="logo" className="img-fluid" />
                                </Navbar.Brand>
                                <Nav className="mt-3">
                                    {menu.map((m, i)=>
                                        <Nav.Item key={i} active={m.active} onClick={()=>onChangeMenu(m.url)}>{m.title}</Nav.Item>
                                    )}                 
                                </Nav>
                            </>
                        : 
                            <>
                                <Nav className="mt-2 me-2">
                                    <IconButton appearance="ghost" icon={<MenuIcon />} onClick={()=>setOpenMenu(true)} />
                                </Nav>
                                <Nav className="rs-navbar-brand" pullRight  style={{width:'15%', height:'100%'}}>
                                    <img src="/logo.png" alt="logo" className="img-fluid" />
                                </Nav>
                            </>
                        }                        
                    </Navbar>
                </Col>
            </Grid>
            <Grid fluid className="header-info">
                <Col xs={24} sm={23} smOffset={1} mdHidden lgHidden xlHidden xxlHidden>
                    <Col className="p-2 text-center full-width">
                        <a href="mailto:contacto@visas-premier.com"> Envíanos un correo electrónico</a>
                    </Col>
                </Col>
            </Grid>

            {/*CONTENT */}
            <Outlet />

            

            {/**FOOTER */}
            <Grid fluid className="footer">
                <Col md={22} lg={19} mdOffset={1} lgOffset={3}>
                    <Col md={12} lg={8} xl={6} className="p-4">
                        <img src="/images/logo-footer.png" className="img-fluid" />
                        <div style={{width:'100%', textAlign:'center'}} className="p-2">
                            <a href="https://www.facebook.com/visaspremier" target="_blank" className="brand"><FaSquareFacebook /></a>
                            <a href="https://www.instagram.com/premservslp/" target="_blank" className="brand"><FaInstagram /></a>
                            <a href="mailto:contacto@visas-premier.com" target="_blank" className="brand"><FaEnvelope /></a>
                        </div>
                    </Col>
                    <Col md={12} lg={8} xl={6} xlOffset={12} lgOffset={8} className="p-4">
                        <ul>
                            <li>Trámite de Visas</li>
                            <li>Trámite de Pasaportes</li>
                            <li>Permisos Fronterizos</li>
                            <li>Servicios de Transporte</li>
                            <li>Renta de Vehículos</li>
                        </ul>
                    </Col>
                </Col>
            </Grid>
            {!fullMenu &&
                <Drawer open={openMenu} onClose={()=>setOpenMenu(false)} placement="left" size="xs">
                    <Nav vertical>
                        {menu.map((m, i)=>
                            <Nav.Item key={i} active={m.active} onClick={()=>onChangeMenu(m.url)}>{m.title}</Nav.Item>
                        )}   
                    </Nav>
                </Drawer>
            }
{/* 
            <div className="start-procedure">
                <Button className="btn-procedure" appearance="primary" onClick={()=>openProcedure()}>Iniciar Trámite</Button>
            </div> */}
        </>
    )
}

export default Layout;