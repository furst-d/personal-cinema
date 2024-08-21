import StorageIcon from "@mui/icons-material/Storage";

export const menuConfig = [
    {
        name: 'users',
    },
    {
        name: 'videos',
        children: [
            {
                name: 'videos.conversions',
            }
        ]
    },
    {
        name: 'storages.users',
        children: [
            {
                name: 'storages.prices',
            },
            {
                name: 'storages.upgrades',
            }
        ],
    },
    {
        name: 'settings',
    },
];