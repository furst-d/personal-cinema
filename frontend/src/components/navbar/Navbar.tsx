import {
    AppBar,
    Avatar,
    Box,
    Button,
    Container,
    Divider,
    IconButton,
    Menu,
    MenuItem,
    Toolbar,
    Tooltip,
    Typography,
    ListItemIcon
} from "@mui/material";
import React from "react";
import MenuIcon from '@mui/icons-material/Menu';
import { NavLink } from 'react-router-dom';
import { useAuth } from "../providers/AuthProvider";
// @ts-ignore
import Logo from '/public/images/logo.svg?react';
import { useTheme } from "styled-components";
import AccountCircleIcon from '@mui/icons-material/AccountCircle';
import SettingsIcon from '@mui/icons-material/Settings';
import LogoutIcon from '@mui/icons-material/Logout';

const Navbar = () => {
    const theme = useTheme();
    const { user, logout } = useAuth();

    const pages = [
        { title: 'Vaše videa', path: '/' },
        { title: 'Správa videí', path: '/videos-management' },
        { title: 'Správa úložiště', path: '/storage' }
    ];

    const settings = [
        { title: 'Profil', path: '/profile', icon: <AccountCircleIcon /> },
        { title: 'Nastavení', path: '/settings', icon: <SettingsIcon /> },
        { isDivider: true },
        { title: 'Odhlásit se', path: '/login', color: theme.primary, weight: '700', icon: <LogoutIcon />, onClick: logout }
    ];

    const [anchorElNav, setAnchorElNav] = React.useState<null | HTMLElement>(null);
    const [anchorElUser, setAnchorElUser] = React.useState<null | HTMLElement>(null);

    const handleOpenNavMenu = (event: React.MouseEvent<HTMLElement>) => {
        setAnchorElNav(event.currentTarget);
    };
    const handleOpenUserMenu = (event: React.MouseEvent<HTMLElement>) => {
        setAnchorElUser(event.currentTarget);
    };

    const handleCloseNavMenu = () => {
        setAnchorElNav(null);
    };

    const handleCloseUserMenu = () => {
        setAnchorElUser(null);
    };

    const getInitial = (email: string) => {
        return email.charAt(0).toUpperCase();
    };

    return (
        <AppBar position="static" sx={{ color: theme.textLight }}>
            <Container maxWidth="xl">
                <Toolbar disableGutters>
                    <Box sx={{ display: { xs: 'none', md: 'flex' }, alignItems: 'center', marginRight: '1rem' }}>
                        <NavLink to="/" style={{ display: 'flex', alignItems: 'center', textDecoration: 'none', color: 'inherit' }}>
                            <Logo height="40px" />
                            <Typography
                                variant="h6"
                                noWrap
                                sx={{
                                    ml: 1,
                                    fontFamily: 'monospace',
                                    fontWeight: 700,
                                    letterSpacing: '.1rem',
                                    color: 'inherit',
                                    textDecoration: 'none',
                                }}
                            >
                                SoukromeKino
                            </Typography>
                        </NavLink>
                    </Box>

                    <Box sx={{ display: { xs: 'flex', md: 'none' }, flexGrow: 1, justifyContent: 'space-between', alignItems: 'center' }}>
                        <IconButton
                            size="large"
                            aria-label="account of current user"
                            aria-controls="menu-appbar"
                            aria-haspopup="true"
                            onClick={handleOpenNavMenu}
                            color="inherit"
                        >
                            <MenuIcon />
                        </IconButton>
                        <Box sx={{ display: 'flex', alignItems: 'center' }}>
                            <NavLink to="/" style={{ display: 'flex', alignItems: 'center', textDecoration: 'none', color: 'inherit' }}>
                                <Logo height="30px" />
                                <Typography
                                    variant="h6"
                                    noWrap
                                    sx={{
                                        ml: 1,
                                        flexGrow: 1,
                                        fontFamily: 'monospace',
                                        fontWeight: 700,
                                        letterSpacing: '.1rem',
                                        color: 'inherit',
                                        textDecoration: 'none',
                                    }}
                                >
                                    SoukromeKino
                                </Typography>
                            </NavLink>
                        </Box>
                        <Box sx={{ width: '40px' }} /> {/* Empty box to balance the logo and menu icon */}
                    </Box>

                    <Menu
                        id="menu-appbar"
                        anchorEl={anchorElNav}
                        anchorOrigin={{
                            vertical: 'bottom',
                            horizontal: 'left',
                        }}
                        keepMounted
                        transformOrigin={{
                            vertical: 'top',
                            horizontal: 'left',
                        }}
                        open={Boolean(anchorElNav)}
                        onClose={handleCloseNavMenu}
                        sx={{
                            display: { xs: 'block', md: 'none' }
                        }}
                    >
                        {pages.map((page: any, index: number) => (
                            page.isDivider
                                ? <Divider key={index} />
                                : <MenuItem
                                    key={page.title}
                                    component={NavLink}
                                    to={page.path}
                                    onClick={handleCloseNavMenu}
                                    sx={{
                                        color: page.color ? page.color : 'inherit',
                                    }}
                                >
                                    <Typography
                                        textAlign="center"
                                        sx={{
                                            fontWeight: page.weight ? page.weight : 'inherit',
                                        }}
                                    >{page.title}</Typography>
                                </MenuItem>
                        ))}
                    </Menu>

                    <Box sx={{ flexGrow: 1, display: { xs: 'none', md: 'flex' } }}>
                        {pages.map((page) => (
                            <Button
                                key={page.title}
                                component={NavLink}
                                to={page.path}
                                onClick={handleCloseNavMenu}
                                sx={{
                                    my: 1,
                                    color: 'inherit',
                                    display: 'block',
                                    fontWeight: 'bold',
                                    '&:hover': {
                                        backgroundColor: theme.primaryDarker
                                    },
                                    '&.active': {
                                        backgroundColor: theme.primaryDarker
                                    }
                                }}
                            >
                                {page.title}
                            </Button>
                        ))}
                    </Box>

                    <Box sx={{ flexGrow: 0 }}>
                        <Tooltip title="Profil">
                            <IconButton onClick={handleOpenUserMenu} sx={{ p: 0 }}>
                                <Avatar>{getInitial(user?.email || "")}</Avatar>
                            </IconButton>
                        </Tooltip>
                        <Menu
                            sx={{ mt: '45px' }}
                            id="menu-appbar"
                            anchorEl={anchorElUser}
                            anchorOrigin={{
                                vertical: 'top',
                                horizontal: 'right',
                            }}
                            keepMounted
                            transformOrigin={{
                                vertical: 'top',
                                horizontal: 'right',
                            }}
                            open={Boolean(anchorElUser)}
                            onClose={handleCloseUserMenu}
                        >
                            {settings.map((setting: any, index: number) => (
                                setting.isDivider
                                    ? <Divider key={index} />
                                    : <MenuItem
                                        key={setting.title}
                                        component={NavLink}
                                        to={setting.path}
                                        onClick={() => {
                                            handleCloseUserMenu();
                                            if (setting.onClick) setting.onClick();
                                        }}
                                        sx={{
                                            color: setting.color ? setting.color : 'inherit',
                                            display: 'flex',
                                            alignItems: 'center',
                                        }}
                                    >
                                        {setting.icon && (
                                            <ListItemIcon sx={{ color: setting.color ? setting.color : 'inherit' }}>
                                                {setting.icon}
                                            </ListItemIcon>
                                        )}
                                        <Typography
                                            textAlign="center"
                                            sx={{
                                                fontWeight: setting.weight ? setting.weight : 'inherit',
                                            }}
                                        >
                                            {setting.title}
                                        </Typography>
                                    </MenuItem>
                            ))}
                        </Menu>
                    </Box>
                </Toolbar>
            </Container>
        </AppBar>
    );
}

export default Navbar;
