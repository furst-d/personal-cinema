import React from 'react';
import {
    Dialog,
    DialogActions,
    DialogContent,
    DialogTitle,
    Typography,
    Button
} from "@mui/material";

interface DeleteDialogProps {
    open: boolean;
    onClose: () => void;
    onDelete: () => void;
    deletingType: "folder" | "video" | null;
}

const DeleteDialog: React.FC<DeleteDialogProps> = ({ open, onClose, onDelete, deletingType }) => (
    <Dialog open={open} onClose={onClose}>
        <DialogTitle>Potvrdit smazání</DialogTitle>
        <DialogContent>
            <Typography>
                {deletingType === "folder"
                    ? "Opravdu chcete smazat tuto složku a všechny její podřízené složky a videa?"
                    : "Opravdu chcete smazat toto video? Tato akce je nevratná."}
            </Typography>
        </DialogContent>
        <DialogActions>
            <Button onClick={onClose} color="primary">
                Zrušit
            </Button>
            <Button onClick={onDelete} color="primary">
                Smazat
            </Button>
        </DialogActions>
    </Dialog>
);

export default DeleteDialog;
