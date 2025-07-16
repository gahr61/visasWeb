import React from "react";
import { Routes, Route } from "react-router-dom";
import Layout from "./components/Layout";
import Home from './pages/Home';
import About from "./pages/About";
import Services from "./pages/Services";
import Contact from './pages/Contact';

const AppRoutes = (props)=>{
    return(
        <Routes>
            <Route element={<Layout {...props} />}>
                <Route path="/" exact element={<Home {...props} />} />
                <Route path="/about" exact element={<About />} />
                <Route path="/services" exact element={<Services />} />
                <Route path="/contact" exact element={<Contact />} />
            </Route>            
            
            <Route path="*" element={<>??</>} />
        </Routes>
    )
}

export default AppRoutes;