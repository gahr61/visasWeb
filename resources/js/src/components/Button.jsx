import React from "react";
import { motion } from "framer-motion";

const Button = ({main, active, title, action, variable, disabled})=>{
    return(
        <motion.button
            className="rs-btn"
            animate={active ? 
                {width:'100%',padding:'7px 11px',background:'#1d3664', color:'#FFF', border:'1px solid #1d3664'} 
            : 
                {width:'100%',padding:'7px 11px',background:'#FFF', color:'#ce1135', border:'1px solid #ce1135'}
            }
            disabled={disabled}
            onClick={()=>action(variable)}
        >
            {main ?
                <h1>{title}</h1>    
            :   
                <h5>{title}</h5>
            }
        </motion.button>
    )
}

export default Button;

Button.defaultProps = {
    action: ()=>{},
    main:false
}