import React from 'react';
import {List, Datagrid, TextField, TextInput, DateField} from 'react-admin';
import { ListProps } from 'react-admin';
import DurationField from "../../fields/video/DurationField";
import VideoInfoField from "../../fields/video/VideoInfoField";
import SizeField from "../../fields/storage/SizeField";

const VideoFilters = [
    <TextInput label="Název" source="name" alwaysOn />,
    <TextInput label="Email autora" source="email" alwaysOn />,
    <TextInput label="MD5" source="md5" alwaysOn />,
    <TextInput label="Hash" source="hash" alwaysOn />,
    <TextInput label="CDN ID" source="cdnId" alwaysOn />
];

export const VideoList: React.FC<ListProps> = (props) => (
    <List {...props} filters={VideoFilters}>
        <Datagrid rowClick="edit">
            <TextField source="id" label="ID" />
            <TextField source="name" label="Název" />
            <TextField source="email" label="Email autora" />
            <DateField showTime={true} source="createdAt" label="Vytvořeno" />
            <TextField source="md5" label="MD5" sortable={false} />
            <TextField source="hash" label="Hash" sortable={false} />
            <SizeField source="size" label="Velikost" />
            <DurationField source="length" label="Délka" />
            <TextField source="cdnId" label="CDN ID" sortable={false} />
            <VideoInfoField label="Technické údaje" />
        </Datagrid>
    </List>
);
