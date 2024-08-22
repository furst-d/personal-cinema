import React from 'react';
import {Create} from 'react-admin';
import {VideoConversionForm} from "./VideoConversionForm";

export const VideoConversionCreate: React.FC = (props) => {
    return (
        <Create {...props}>
            <VideoConversionForm />
        </Create>
    )
};
