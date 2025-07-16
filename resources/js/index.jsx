import React from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";

import Application from "./src/Application";

import 'rsuite/dist/rsuite.min.css';
import 'bootstrap/dist/css/bootstrap.css';
import './src/assets/css/app.css';

if(document.getElementById('root')){
    createRoot(document.getElementById('root')).render(
        <BrowserRouter>
            <Application />
        </BrowserRouter>
        
    );
}