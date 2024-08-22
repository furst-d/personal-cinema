import React from 'react';
import {Edit} from 'react-admin';
import {SettingForm} from "./SettingForm";

const SettingEdit: React.FC = (props) => (
    <Edit {...props}>
        <SettingForm />
    </Edit>
);

export default SettingEdit;
