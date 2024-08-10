import React from "react";
import {Box, Button, List, ListItem, ListItemText, Typography} from "@mui/material";

interface ShareListProps {
    shared: { id: string; email: string; createdAt: string }[];
    onUnShare: (shareId: string) => void;
}

const ShareList: React.FC<ShareListProps> = ({ shared, onUnShare }) => {
    return (
        <List>
            {shared.length > 0 && (
                <Typography variant="subtitle1" gutterBottom>
                    Sdíleno s uživateli
                </Typography>
            )}
            {shared.map((shared) => (
                <ListItem key={shared.id}>
                    <Box
                        sx={{
                            display: 'flex',
                            flexDirection: { xs: 'column', sm: 'row' },
                            width: '100%',
                            justifyContent: 'space-between',
                        }}
                    >
                        <ListItemText
                            primary={shared.email}
                            secondary={`Sdíleno: ${new Date(shared.createdAt).toLocaleString()}`}
                            sx={{ flex: '1 1 auto' }}
                        />
                        <Button
                            variant="outlined"
                            color="secondary"
                            onClick={() => onUnShare(shared.id)}
                            sx={{ alignSelf: { xs: 'flex-start', sm: 'center' } }}
                        >
                            Zrušit sdílení
                        </Button>
                    </Box>
                </ListItem>
            ))}
        </List>
    );
}

export default ShareList;