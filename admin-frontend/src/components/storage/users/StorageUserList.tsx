import * as React from 'react';
import {List, Datagrid, TextField, EditButton, ListProps} from 'react-admin';

export const UserStorageList: React.FC<ListProps> = (props) => (
    <List {...props}>
        <Datagrid>
            <TextField source="id" label="ID" />
            <TextField source="username" label="Uživatel" />
            <TextField source="storage_name" label="Úložiště" />
            <EditButton />
        </Datagrid>
    </List>
);
