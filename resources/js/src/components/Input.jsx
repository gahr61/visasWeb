import { isEmail, showCtrlError } from "../libs/functions";

const Input = ({
    type = 'text',
    value = '',
    id,
    handleChange = ()=>{},
    disabled = false,
    required = false,
    setError = ()=>{},
    onEnter = ()=>{},
    placeholder = '',
    min = 1
})=>{

    /**
     * Function used to handle changes to fields on a form
     * Perform specific validations based on the field name and format the field value if neccessary
     * @param {object} e - Change event tha triggered the feature
     */
    const onChange = (e)=>{
        let name = e.target.name;
        let value = e.target.value;
      
        if(name === 'email'){
            // validate if the email field value is valid
            if(!isEmail(value) && value !== ''){
                setError('Invalid email address');
            }else{
                setError('');
            }
        }

        if(name === 'phone'){
            // Format value of phone field to XXX-XXX-XXXX
            let format = value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);

            value = !format[2] ? format[1] : `${format[1]}-${format[2]}${format[3] ? '-' + format[3] : ''}`;
        }

        // Call the handleChange function with the updated field name and value
        handleChange({
            target:{
                name: name,
                value: value
            }
        });

        if(value !== ''){
            showCtrlError(name);
        }
    }

    /**
     * Allows you to perform an action by pressing enter key
     * @param {object} e 
     */
    const onKeyPress = (e)=>{
        if(e.key === 'Enter'){
            onEnter();
        }
    }

    return(
        <input 
            type={type}
            value={value}
            name={id}
            id={id}
            className="form-control form-control-sm"
            onChange={onChange}
            onKeyDown={(e)=>onKeyPress(e)}
            placeholder={placeholder}
            disabled={disabled}
            required={required}
            min={min}
            autoComplete='off'
        />
    )
}

export default Input;
