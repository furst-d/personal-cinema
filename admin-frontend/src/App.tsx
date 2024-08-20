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
import dataProvider from "./providers/dataProvider";
import authProvider from "./providers/authProvider";
import LoginPage from './components/login/LoginPage';
import polyglotI18nProvider from 'ra-i18n-polyglot';
import czechMessages from "ra-language-czech";
import {UserEdit} from "./components/users/UserEdit";
import {UserCreate} from "./components/users/UserCreate";

const i18nProvider = polyglotI18nProvider(() => czechMessages, 'cs', { allowMissing: true });

export const App: React.FC = () => (
    <Admin
        layout={Layout}
        dataProvider={dataProvider}
        authProvider={authProvider}
        loginPage={LoginPage}
        i18nProvider={i18nProvider}
    >
        <Resource
            name="users"
            list={UserList}
            edit={UserEdit}
            create={UserCreate}
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
