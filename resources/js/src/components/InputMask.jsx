import { useEffect, useState } from "react"
import { MaskedInput } from "rsuite"
import { showCtrlError } from "../libs/functions";

const InputMask = ({
    id = "",
    value = "",
    type = "",
    required = false,
    handleChange = ()=>{}
})=>{
    const [mask, setMask] = useState([]);
    const [placeholder, setPlaceholder] = useState('');

    useEffect(()=>{
        if(mask.length === 0){
            if(type === 'date'){
                setMask([/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/])
                setPlaceholder('DD/MM/YYYY');
            }

            if(type === 'phone'){
                setMask([/\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/]);
                setPlaceholder('000-000-0000');
            }
        }
    },[type]);

    const onChange = (e)=>{
        let item = {
            target:{
                name: id,
                value: e
            }
        }

        handleChange(item);

        if(e !== ""){
            showCtrlError(id);
        }
    }

    return(
        <MaskedInput
            value={value}
            mask={mask}
            guide
            keepCharPositions
            placeholder={placeholder}
            placeholderChar={'_'}
            className="form-control form-control-sm"
            onChange={(e)=>onChange(e)}
            id={id}
            name={id}
            required={required}
        />
    )
}

export default InputMask