import React from 'react';
import { Admin, Resource } from 'react-admin';
import { Layout } from './Layout';
import dataProvider from "./providers/dataProvider";
import authProvider from "./providers/authProvider";
import LoginPage from './components/login/LoginPage';
import polyglotI18nProvider from 'ra-i18n-polyglot';
import { UserList } from './components/users/UserList';
import { UserEdit } from './components/users/UserEdit';
import { UserCreate } from './components/users/UserCreate';
import { VideoList } from './components/videos/VideoList';
import { ListGuesser } from 'react-admin';

import PeopleIcon from '@mui/icons-material/People';
import VideoLibraryIcon from '@mui/icons-material/VideoLibrary';
import ContactPageIcon from '@mui/icons-material/ContactPage';
import AttachMoneyIcon from '@mui/icons-material/AttachMoney';
import SettingsIcon from '@mui/icons-material/Settings';
import UpgradeIcon from '@mui/icons-material/Upgrade';
import RepeatIcon from '@mui/icons-material/Repeat';
import VideoEdit from "./components/videos/VideoEdit";
import {extendedCzechMessages} from "./lang/CzechMessages";
import {VideoConversionList} from "./components/videos/VideoConversionList";
import {VideoConversionEdit} from "./components/videos/VideoConversionEdit";
import {VideoConversionCreate} from "./components/videos/VideoConversionCreate";

const i18nProvider = polyglotI18nProvider(() => extendedCzechMessages, 'cs', { allowMissing: true });

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
            icon={PeopleIcon}
            options={{ label: 'Uživatelé' }}
        />
        <Resource
            name="videos"
            list={VideoList}
            edit={VideoEdit}
            icon={VideoLibraryIcon}
            options={{ label: 'Videa' }}
        />
        <Resource
            name="videos.conversions"
            list={VideoConversionList}
            edit={VideoConversionEdit}
            create={VideoConversionCreate}
            icon={RepeatIcon}
            options={{ label: 'Konverze' }}
        />
        <Resource
            name="storages.users"
            list={ListGuesser}
            icon={ContactPageIcon}
            options={{ label: 'Úložiště' }}
        />
        <Resource
            name="storages.prices"
            list={ListGuesser}
            icon={AttachMoneyIcon}
            options={{ label: 'Ceny' }}
        />
        <Resource
            name="storages.upgrades"
            list={ListGuesser}
            icon={UpgradeIcon}
            options={{ label: 'Vylepšení' }}
        />
        <Resource
            name="settings"
            list={ListGuesser}
            icon={SettingsIcon}
            options={{ label: 'Nastavení' }}
        />
    </Admin>
);

export default App;
