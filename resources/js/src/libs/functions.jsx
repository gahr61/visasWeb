import cryptoJs  from "crypto-js";
import moment from "moment";
import Swal from "sweetalert2";

const strk = import.meta.env.VITE_STRK;

/**
 * Add error highlighting to select component
 * @param {*} id field identifier 
 * @param {*} value field value
 */
export const addErrorToSelectedField = (id, value)=>{
	let field = document.getElementById(id);
	
	if(field !== null){
		if(value.length === 0){
			field.classList.add('error');
			return;
		}

		field.classList.remove('error');
		
	}
}

export const isValidForm = (element)=>{
	var ctrls = [];
	const select = document.querySelector(element);

	if(select !== null){
		ctrls = select.querySelectorAll('input, select, textarea');
   	
	    let isFormValid = true;
    	 ctrls.forEach(ctrl => {
	    	if(ctrl.required){
		      	const isInputValid = showCtrlError(ctrl.id);
		      	if (!isInputValid) {
		        	isFormValid = false;
		    	}
		  	}
	    });
	   
	    return isFormValid;
	}

	return true;

};



export const isAlpha = (inputtxt)=>{
    return new RegExp(/^[A-Za-záéíóúÁÉÍÓÚ´. ]+$/).test(inputtxt);
}

/**
 * verify if field is email
 * @param {string} value 
 * @returns boolean
 */
export const isEmail = (value)=>{
    // Regular expression to validate an email address
    var patron = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    //Check if the email matches the pattern
    return patron.test(value);
}


export const isNumber = (value)=>{
	return new RegExp(/[0-9]/).test(value);
}

//Validar que coincida el dígito verificador
const digitoVerificador = (curp17)=>{
	//Fuente https://consultas.curp.gob.mx/CurpSP/
	var diccionario  = "0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ",
		lngSuma      = 0.0,
		lngDigito    = 0.0;

	for(var i=0; i<17; i++)
		lngSuma = lngSuma + diccionario.indexOf(curp17.charAt(i)) * (18 - i);

	lngDigito = 10 - lngSuma % 10;

	if (lngDigito === 10) return '0';

	return lngDigito.toString();
}

export const isValidCurp = (curp)=>{
    let re = /^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/;
    let validado = curp.match(re);
	
    if (!validado)  //Coincide con el formato general?
    	return false;
  
    if (validado[2] !== digitoVerificador(validado[1])) 
    	return false;
        
    return true; //Validado
}

/** decript */


export const encript = (name, value, type = 'session')=>{
    let found = findInStorage(name, type);

    let encriptedValue = cryptoJs.AES.encrypt(value, strk).toString();
    let encriptedName = cryptoJs.AES.encrypt(name, strk).toString();

    if(found !== ''){
        encriptedName = found;
    }

	if(type === 'session'){
		sessionStorage.setItem(encriptedName, encriptedValue);
	}else{
		localStorage.setItem(encriptedName, encriptedValue);
	}
    
}

export const findInStorage = (name, type = 'session')=>{
    let value = '';
	let sessions = type === 'session' ? Object.keys(sessionStorage) : Object.keys(localStorage);
	if(sessions.length > 0){
		sessions.forEach((key)=>{
			let bytes = cryptoJs.AES.decrypt(key, strk);
			let decriptedName = bytes.toString(cryptoJs.enc.Utf8);

			if(decriptedName === name){
				value = key;
			}        
		});
	}
    return value;
}

export const decript = (name, type = 'session')=>{
    let value = '';
	let sessions = type === 'session' ? Object.keys(sessionStorage) : Object.keys(localStorage);
	if(sessions.length > 0){
		sessions.forEach((key)=>{
			let bytes = cryptoJs.AES.decrypt(key, strk);
			let decriptedName = bytes.toString(cryptoJs.enc.Utf8);

			if(decriptedName === name){
				let encriptedValue = type === 'session' ? sessionStorage.getItem(key) : localStorage.getItem(key);            
				let bytesValue = cryptoJs.AES.decrypt(encriptedValue, strk);
				
				value = bytesValue.toString(cryptoJs.enc.Utf8); 
			}        
		});
	}
    return value;
};

export const CryptoJSAesJson = {
    stringify: function (cipherParams) {
        var j = {ct: cipherParams.ciphertext.toString(cryptoJs.enc.Base64)};
        if (cipherParams.iv) j.iv = cipherParams.iv.toString();
        if (cipherParams.salt) j.s = cipherParams.salt.toString();
        return JSON.stringify(j).replace(/\s/g, '');
    },
    parse: function (jsonStr) {
        var j = JSON.parse(jsonStr);
        var cipherParams = cryptoJs.lib.CipherParams.create({ciphertext: cryptoJs.enc.Base64.parse(j.ct)});
        if (j.iv) cipherParams.iv = cryptoJs.enc.Hex.parse(j.iv);
        if (j.s) cipherParams.salt = cryptoJs.enc.Hex.parse(j.s);
        return cipherParams;
    }
}

/**
 * Calcular edad de paciente
 * @param {string} date fecha de nacimiento format YYYY-MM-DD
 */
 export const calculateAge = (date)=>{
    let birth = moment(date);
    let today = moment();

    let age = today.diff(birth, "years");

    return age;
}

export const transformAmount = (amount)=>{
	let separator = ',';
	let sepDecimal = '.';

	let num = amount;
	num += '';

	let splitStr = num.split('.');
	let splitLeft = splitStr[0];
	let splitRight = splitStr.length > 1 ? sepDecimal + splitStr[1] : '';
	let regx = /(\d+)(\d{3})/;

	while (regx.test(splitLeft)) {
		splitLeft = splitLeft.replace(regx, '$1' + separator + '$2');
	}

	return splitLeft  +splitRight;
}

export const swalAction = (obj)=>{
	Swal.fire({
		title 				: obj.title,
		text 				: obj.text,
		icon 				: obj.icon,
		showConfirmButton	: true,
		showCancelButton	: true,
		confirmButtonText	: obj.textConfirm,
		cancelButtonColor	: obj.colorCancel,
		cancelButtonText	: obj.textcancel
	}).then(result => {
		if(result.isConfirmed){
			obj.fn(obj.values);
		}else{
			if(obj.fnCancel !== undefined){
				obj.fnCancel(obj.values);
			}
		}
	});
}

export const showCtrlError = (id)=>{
	var res = null;
	var control = document.getElementById(id);

	if(control !== null){
		if(control.value !== undefined){
			if (control.value.trim() === "") {
				if(control !== null){
					control.classList.add('error');
				}
				res = false;
			} else{
				if(control !== null){
					if(control.required && control.className.includes('error')){
						control.classList.remove('error');
					}
				}
				res = true;
			}
		}
		
	}

	return res;
};
