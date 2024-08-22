import React from 'react';
import { Edit } from 'react-admin';
import {VideoConversionForm} from "./VideoConversionForm";

export const VideoConversionEdit: React.FC = (props) => {
    return (
        <Edit {...props}>
            <VideoConversionForm />
        </Edit>
    );
};
