import React from 'react';
import {
    Dialog,
    DialogActions,
    DialogContent,
    DialogTitle,
    TextField,
    Button
} from "@mui/material";

interface EditDialogProps {
    open: boolean;
    onClose: () => void;
    onSave: (item: any) => void;
    isEditing: boolean;
    editingType: "folder" | "video" | null;
    newName: string;
    setNewName: (name: string) => void;
    nameError: string;
    setNameError: (error: string) => void;
}

const EditDialog: React.FC<EditDialogProps> = ({
   open,
   onClose,
   onSave,
   isEditing,
   editingType,
   newName,
   setNewName,
   nameError,
   setNameError
}) => (
    <Dialog open={open} onClose={onClose} fullWidth>
        <DialogTitle>{isEditing ? (editingType === "folder" ? "Upravit složku" : "Upravit video") : "Vytvořit novou složku"}</DialogTitle>
        <DialogContent>
            <TextField
                autoFocus
                margin="dense"
                label={editingType === "folder" ? "Název složky" : "Název videa"}
                fullWidth
                value={newName}
                onChange={(e) => {
                    setNewName(e.target.value);
                    setNameError("");
                }}
                error={Boolean(nameError)}
                helperText={nameError}
            />
        </DialogContent>
        <DialogActions>
            <Button onClick={onClose} color="primary">
                Zrušit
            </Button>
            <Button onClick={onSave} color="primary">
                {isEditing ? "Upravit" : "Vytvořit"}
            </Button>
        </DialogActions>
    </Dialog>
);

export default EditDialog;
