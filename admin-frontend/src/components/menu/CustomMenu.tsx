import React from 'react';
import { Menu, useSidebarState } from 'react-admin';
import { Box, ListItem, ListItemIcon, Typography } from '@mui/material';
import { menuConfig } from './MenuConfig';

export const CustomMenu = () => {
    const [open] = useSidebarState();

    return (
        <Menu>
            {menuConfig.map((item) => {
                let menuItem;

                if (item.name) {
                    menuItem = (
                        <Menu.ResourceItem
                            key={item.name}
                            name={item.name}
                            icon={item.icon}
                            primaryText={item.label || item.name}
                        />
                    );
                } else {
                    menuItem = (
                        <ListItem key={item.label} sx={{ paddingLeft: '16px' }}>
                            {item.icon && (
                                <ListItemIcon sx={{ minWidth: '40px' }}>
                                    <item.icon />
                                </ListItemIcon>
                            )}
                            <Typography variant="body1" color="textSecondary">
                                {item.label}
                            </Typography>
                        </ListItem>
                    );
                }

                if (item.children) {
                    return [
                        menuItem,
                        <Box
                            key={`${item.label || item.name}-children`}
                            pl={open ? 2 : 0}
                            display="flex"
                            flexDirection="column"
                        >
                            {item.children.map((child) => (
                                <Menu.ResourceItem key={child.name} name={child.name} />
                            ))}
                        </Box>,
                    ];
                }

                return menuItem;
            })}
        </Menu>
    );
};
