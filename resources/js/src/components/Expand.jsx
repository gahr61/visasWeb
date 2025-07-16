import React from "react";
import { motion } from "framer-motion";

const Expand = ({value, children})=>{
    return(
        <motion.div
            layout
            animate={!value ?
                {display:'none', height:'0px', width:'100%'}
            :
                {display:'block', height:'100%', width:'100%'}
            }
        >
            {children}
        </motion.div>
    )
}

export default Expand;