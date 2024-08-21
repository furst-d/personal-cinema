import * as React from 'react';
import {List, Datagrid, TextField, NumberField, EditButton, ListProps} from 'react-admin';

export const StoragePriceList: React.FC<ListProps> = (props) => (
    <List {...props}>
        <Datagrid>
            <TextField source="id" label="ID" />
            <TextField source="storage_name" label="Úložiště" />
            <NumberField source="price" label="Cena" />
            <EditButton />
        </Datagrid>
    </List>
);