import React from 'react';
import {TextInput, SimpleForm, Edit} from 'react-admin';
import { ListProps } from 'react-admin';

export const VideoEdit: React.FC<ListProps> = (props) => (
    <Edit {...props}>
        <SimpleForm>
            <TextInput source="name" label="Název" />
        </SimpleForm>
    </Edit>
);

export default VideoEdit;
