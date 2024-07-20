import React from 'react';
import {BooleanField, BooleanFieldProps, useRecordContext} from 'react-admin';

interface CustomBooleanFieldProps {
    source: string;
    label: string;
}

const CustomBooleanField: React.FC<CustomBooleanFieldProps> = ({ source, label }) => {
    const record = useRecordContext();
    const value = record ? record[source] : false;

    return <span>{value ? 'Ano' : 'Ne'}</span>;
};

export default CustomBooleanField;
