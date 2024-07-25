import {
    AppBar,
    Avatar,
    Box,
    Button,
    Container, Divider,
    IconButton,
    Menu,
    MenuItem,
    Toolbar,
    Tooltip,
    Typography
} from "@mui/material";
import React from "react";
import MenuIcon from '@mui/icons-material/Menu';
import { useTheme } from 'styled-components';
import Logo from '/public/images/logo.svg?react';
import { NavLink } from 'react-router-dom';

const Navbar = () => {
    const theme = useTheme();

    const pages = [
        { title: 'Vaše videa', path: '/' },
        { title: 'Správa videí', path: '/videos' },
        { title: 'Správa disku', path: '/disc' }
    ];

    const settings = [
        { title: 'Profil', path: '/profile' },
        { title: 'Nastavení', path: '/settings' },
        { isDivider: true },
        { title: 'Odhlásit se', path: '/logout', color: theme.primary, weight: '700' }
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

    return (
        <AppBar position="static" sx={{ color: theme.text_light }}>
            <Container maxWidth="xl">
                <Toolbar disableGutters>
                    <Box sx={{ display: { xs: 'none', md: 'flex' }, marginRight: '1rem' }}>
                        <NavLink to="/" style={{ display: 'flex', alignItems: 'center', textDecoration: 'none' }}>
                            <Logo height="40px" />
                        </NavLink>
                    </Box>
                    <Typography
                        variant="h6"
                        noWrap
                        component={NavLink}
                        to="/"
                        sx={{
                            mr: 2,
                            display: { xs: 'none', md: 'flex' },
                            fontFamily: 'monospace',
                            fontWeight: 700,
                            letterSpacing: '.1rem',
                            color: 'inherit',
                            textDecoration: 'none',
                        }}
                    >
                        SoukromeKino
                    </Typography>

                    <Box sx={{ flexGrow: 1, display: { xs: 'flex', md: 'none' } }}>
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
                                            fontWeight: page.weight ? page.weight : 'normal',
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
                    </Box>
                    <Box sx={{ display: { xs: 'flex', md: 'none' }, marginRight: '1rem' }}>
                        <NavLink to="/" style={{ display: 'flex', alignItems: 'center', textDecoration: 'none' }}>
                            <Logo height="40px" />
                        </NavLink>
                    </Box>
                    <Typography
                        variant="h5"
                        noWrap
                        component={NavLink}
                        to="/"
                        sx={{
                            mr: 2,
                            display: { xs: 'flex', md: 'none' },
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
                                        backgroundColor: theme.primary_darker
                                    },
                                    '&.active': {
                                        backgroundColor: theme.primary_darker
                                    }
                                }}
                            >
                                {page.title}
                            </Button>
                        ))}
                    </Box>

                    <Box sx={{ flexGrow: 0 }}>
                        <Tooltip title="Nastavení">
                            <IconButton onClick={handleOpenUserMenu} sx={{ p: 0 }}>
                                <Avatar alt="Avatar" src="/static/images/avatar/2.jpg" />
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
                                        onClick={handleCloseUserMenu}
                                        sx={{
                                            color: setting.color ? setting.color : 'inherit',
                                        }}
                                    >
                                        <Typography
                                            textAlign="center"
                                            sx={{
                                                fontWeight: setting.weight ? setting.weight : 'inherit',
                                            }}
                                        >{setting.title}</Typography>
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
