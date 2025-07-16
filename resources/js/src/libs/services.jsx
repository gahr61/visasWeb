import moment from "moment";
import { decript, encript } from "./functions";

import countries from './jsonCountries/countries.json';
import states from './jsonCountries/states.json';

const api =  import.meta.env.VITE_HOST;
const countriesKey = import.meta.env.VITE_COUNTRIES_KEY;
const countriesEmail = import.meta.env.VITE_COUNTRIES_EMAIL;

export const fetchRequest = ({
    url,
    method = 'GET',
    obj = null, 
    requireToken = false,
    sendFile = false
})=>{
    let token = decript('token');

    return fetch(api+url,{
        method:method,
        body: obj !== null ? sendFile ? obj : JSON.stringify(obj) : null,
        headers: new Headers(
            requireToken ?
                
                    {
                        'Authorization' : 'Bearer '+token,
                        'Content-Type'  : 'application/json',
                        'Accept'        : 'application/json',
                        'Access-Control-Allow-Origin': '*',
                    }
            : sendFile ? 
                { }
            :
                {
                    'Content-Type'  : 'application/json',
                    'Accept'        : 'application/json',
                    'Access-Control-Allow-Origin': '*',
                }
        )
    }).then(res => {
        if(res.ok){
            return res.json();
        }else{
            res.text().then(msg => console.log(msg));
        }
    }).then(response => {
        if(response){
            return response;
        }
    })
}

export const importData = (obj)=>{
    return fetchRequest({url:'importing', method:'POST', obj:obj});
}

/*Services*/
export const billingStore = (obj)=>{
    return fetchRequest({url:'sales/billing', method:'POST', obj:obj, requireToken:true});
}

export const contactForm = (obj)=>{
    return fetchRequest({
        url:'web/contact',
        method:'POST',
        obj:obj
    });
};

export const clientsBySale = (id)=>{
    return fetchRequest({url:'clients/sale/'+id, requireToken:true});
}

export const customerDelete = (obj)=>{
    return fetchRequest({url:'customer/delete', method:'POST', obj:obj, requireToken:true});
}

export const customerStore = (obj)=>{
    return fetchRequest({
        url:'customer/create',
        method:'POST',
        obj:obj,
        requireToken: true
    });
};

const getAccessToken = ()=>{
    return fetch('https://www.universal-tutorial.com/api/getaccesstoken', {
        method:'GET',
        headers: new Headers({
            "Accept": "application/json",
            "api-token": countriesKey,
            "user-email": countriesEmail
        })
    }).then( res => res.json())
    .then(response => response )
}

export const getCountries = async ()=>{
    return countries;
}

export const getExchageType = ()=>{    
    let date = moment().subtract('1', 'day').format('YYYY-MM-DD');
    let today = moment().format('YYYY-MM-DD');
    
    return fetch("https://www.banxico.org.mx/SieAPIRest/service/v1/series/SF43718/datos/"+date+"/"+today+
        '?token=f7b566054b7c7fa984b84de6611c71c454afa4b98e8f517c957bcfb0a7139bf0')
        .then(respuesta => respuesta.json())
        .then(respuestaDecodificada => {
            return respuestaDecodificada;
        });
};

export const getStates = async (value)=>{
    let country = countries.countries.find(obj => obj.name.toUpperCase() === value.toUpperCase());
    
    return states.states.filter((state)=> state.id_country === country.id);
}

export const getCities = async (value)=>{
    let token = sessionStorage.getItem('token');

   return fetch('https://www.universal-tutorial.com/api/cities/'+value,{
       method:'GET',
       headers: new Headers({
           "Authorization": "Bearer "+token,
           "Accept": "application/json"
       })
   }).then( res => res.json())
   .then(response => response )
   
}

export const paymentCancel = (obj)=>{
    return fetchRequest({url:'paymentIntent/cancel', method:'POST', obj:obj, requireToken:true});
}

export const paymentGenerate = (obj)=>{
    return fetchRequest({
        url:'paymentIntent/generate',
        method:'POST',
        obj:obj,
        requireToken:true
    });
}

export const paymentUpdate = (obj)=>{
    return fetchRequest({url:'paymentIntent/update', method:'PUT', obj:obj, requireToken:true});
}

export const login = (obj)=>{
    return fetchRequest({
        url:'auth/login',
        method:'POST',
        obj:obj
    });
};

export const logout = ()=>{
    return fetchRequest({
        url:'auth/logout',
        requireToken:true
    });
};

export const salesConfirmTicket = (obj)=>{
    return fetchRequest({url:'sales/confirm', method:'POST', obj:obj, sendFile:true});
}

export const salesDelete = (id)=>{
    return fetchRequest({
        url:'sales/'+id,
        method:'DELETE',
        requireToken:true
    });
};

export const salesStore = (obj)=>{
    return fetchRequest({
        url:'sales',
        method:'POST',
        obj:obj,
        requireToken:true
    });
};

export const salesUpdate = (obj)=>{
    return fetchRequest({
        url:'sales/web',
        method:'PUT',
        obj: obj,
        requireToken:true
    });
};

export const startSessionWeb = ()=>{
    return fetchRequest({
        url:'startSession',
        method:'POST'
    });
}

