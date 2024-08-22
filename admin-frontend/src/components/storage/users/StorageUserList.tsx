import * as React from 'react';
import {List, Datagrid, TextField, ListProps, TextInput} from 'react-admin';
import SizeField from "../../fields/storage/SizeField";
import FillBarField from "../../fields/storage/FillBarField";

const emailFilter = [
    <TextInput label="Email" source="email" alwaysOn />,
];

export const StorageUserList: React.FC<ListProps> = (props) => (
    <List {...props} filters={emailFilter}>
        <Datagrid bulkActionButtons={false}>
            <TextField source="id" label="ID" />
            <TextField source="email" label="Email" />
            <SizeField source="maxStorage" label="Maximální úložiště" />
            <SizeField source="usedStorage" label="Využité úložiště" />
            <FillBarField source="fillSize" label="Zaplněnost" />
        </Datagrid>
    </List>
);
