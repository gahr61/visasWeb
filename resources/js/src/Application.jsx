import React, { useRef } from "react";
import AppRoutes from "./AppRoutes";
import Loader from "./components/Loader";

const Application = ()=>{
    const loader = useRef(null);

    return(
        <>
            <AppRoutes loader={loader} />

            <Loader ref={loader} />
        </>
    )
}

export default Application;