import React from 'react';
import {
    Admin,
    Resource,
    EditGuesser,
    ShowGuesser,
} from 'react-admin';
import { Layout } from './Layout';
import { UserList } from './components/users/UserList';
import { VideoList } from './components/videos/VideoList';
import dataProvider from "./dataProvider";

export const App: React.FC = () => (
    <Admin layout={Layout} dataProvider={dataProvider}>
        <Resource
            name="users"
            list={UserList}
            show={ShowGuesser}
            edit={EditGuesser}
            options={{ label: 'Uživatelé' }}
        />
        <Resource
            name="videos"
            list={VideoList}
            show={ShowGuesser}
            edit={EditGuesser}
            options={{ label: 'Videa' }}
        />
    </Admin>
);
