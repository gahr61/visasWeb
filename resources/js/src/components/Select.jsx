import { useEffect, useState } from "react";
import Select from 'react-select';
import { addErrorToSelectedField } from "../libs/functions";
const SelectForm = ({
    value = {value:'', label:''},
    handleChange = ()=>{},
    required = false,
    disabled = false,
    id = '',
    options = [],
    multiple = false
})=>{
    const [valueField, setValueField] = useState({value:'', label:''});
    useEffect(()=>{
        let index = options.findIndex(obj => obj.value === '');
        if(index === -1){
            //options.unshift({value:'', label:'Select...'});
        }
    },[options]);
    
    const customStyles = {
        container:  (provided, state) => ({
            ...provided,
            padding: '0',
            border: '0',
            fontSize: '0.85rem',
        }),
        control:  (provided, state) => ({
            ...provided,
            //height: 'calc(1.5em + 0.5rem + 2.5px)',
            minHeight: 'calc(1.5em + 0.5rem + 2.6px)',
            fontSize: '0.85rem',
            borderColor: '#d8dbe0',
            boxShadow: 'inset 0 1px 1px rgba(0, 0, 0, 0.075)',
            transition: 'background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out',
            ':hover': {
                borderColor: state.isFocused ? '#66afe9' : '#d8dbe0',
                boxShadow: state.isFocused ? 
                'inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(102, 175, 233, 0.6)' : 
                'inset 0 1px 1px rgba(0, 0, 0, 0.075)',
            }
        }),
        valueContainer: (provided, state) => ({
            ...provided,
            marginTop: '0',
            marginLeft: '6px',
            padding: '0',
            border: '0',
        }),
        dropdownIndicator: (provided, state) => ({
            ...provided,
            marginTop: '0',
            padding: '0',
            border: '0',
            width: '16px',
        }),
        clearIndicator: (provided, state) => ({
            ...provided,
            marginTop: '0',
            padding: '0',
            border: '0',
            width: '16px',
        }),
        indicatorsContainer: (provided, state) => ({
            ...provided,
            paddingRight: '4px',
            border: '0',
        }),
    }

    /**
     * Update state variable 
     * @param {obj} e 
     */
    const onChangeValue = (e)=>{
        handleChange({
            target:{
                value: e === null ? '' : e,
                name: id
            }
        });

        setValueField(e);

        let value = e === null ? '' : e.length === 0 ? '' : e

        if(value !== ''){
            addErrorToSelectedField(id, value);
        }
    }

    /**
     * gets the value of the field depending on whether it is multiple or single
     */
    const onSetValue = ()=>{
        if(multiple){
            setValueField(value);
        }else{
            let val = options.find(obj => obj.value === value.value);

            setValueField(val || null);
        }
    }

    useEffect(()=>{
        onSetValue()
    },[])

    return (
        <Select    
            styles={customStyles} 
            id={id}
            name={id}
            options={options}
            required={required}
            isDisabled={disabled}
            isMulti={multiple}
            value={ value }
            placeholder="Select..."
            isClearable={true}
            onChange={(e)=>onChangeValue(e)}
        />
    )
}

export default SelectForm;


