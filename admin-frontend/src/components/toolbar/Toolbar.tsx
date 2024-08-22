import {SaveButton, Toolbar} from "react-admin";
import React from "react";

export const UnDeletableToolbar = (props: any) => (
    <Toolbar {...props}>
        <SaveButton />
    </Toolbar>
);