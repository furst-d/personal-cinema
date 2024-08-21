import React from 'react';
import {List, Datagrid, NumberField} from 'react-admin';
import { ListProps } from 'react-admin';

export const VideoConversionList: React.FC<ListProps> = (props) => (
    <List {...props}>
        <Datagrid rowClick="edit">
            <NumberField source="id" label="ID" />
            <NumberField source="width" label="Šířka" />
            <NumberField source="height" label="Výška" />
            <NumberField source="bandwidth" label="Šířka pásma" />
        </Datagrid>
    </List>
);
