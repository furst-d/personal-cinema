import React from 'react';
import { List, Datagrid, TextField } from 'react-admin';
import { ListProps } from 'react-admin';
import ResolutionField from "../fields/ResolutionField";

export const VideoList: React.FC<ListProps> = (props) => (
    <List {...props}>
        <Datagrid rowClick="edit">
            <TextField source="id" label="ID" />
            <TextField source="name" label="Název" />
            <TextField source="account.email" label="Email autora" />
            <TextField source="md5.md5" label="MD5" />
            <TextField source="hash" label="Hash" />
            <TextField source="extension" label="Formát" />
            <TextField source="codec" label="Kodek" />
            <TextField source="size" label="Velikost" />
            <TextField source="length" label="Délka" />
            <TextField source="cdnId" label="CDN ID" />
            <ResolutionField source="resolution" label="Rozlišení" />
        </Datagrid>
    </List>
);
