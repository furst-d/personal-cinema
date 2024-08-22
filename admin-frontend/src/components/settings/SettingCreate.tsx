import React from 'react';
import {Create} from 'react-admin';
import {SettingForm} from "./SettingForm";

export const SettingCreate: React.FC = (props) => {
    return (
        <Create {...props}>
            <SettingForm />
        </Create>
    )
};
