import React from 'react';
import {List, Datagrid, TextField} from 'react-admin';
import { ListProps } from 'react-admin';

export const SettingList: React.FC<ListProps> = (props) => (
    <List {...props}>
        <Datagrid rowClick="edit">
            <TextField source="id" label="ID" />
            <TextField source="key" label="Klíč" />
            <TextField source="value" label="Hodnota" />
        </Datagrid>
    </List>
);
