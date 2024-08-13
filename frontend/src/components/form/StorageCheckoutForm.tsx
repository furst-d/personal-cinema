import React, { useState } from "react";
import { useStripe, useElements, CardElement } from "@stripe/react-stripe-js";
import { Button, Typography } from "@mui/material";

interface StorageCheckoutFormProps {
    clientSecret: string;
    onSuccess: () => void;
    onCancel: () => void;
}

const StorageCheckoutForm: React.FC<StorageCheckoutFormProps> = ({ clientSecret, onSuccess, onCancel }) => {
    const stripe = useStripe();
    const elements = useElements();
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (event: React.FormEvent) => {
        event.preventDefault();

        if (!stripe || !elements) {
            return;
        }

        const result = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: elements.getElement(CardElement)!
            }
        });

        if (result.error) {
            setError(result.error.message || "Nastala chyba při zpracování platby.");
        } else {
            if (result.paymentIntent?.status === "succeeded") {
                onSuccess();
            }
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            <CardElement />
            {error && <Typography color="error">{error}</Typography>}
            <Button type="submit" variant="contained" disabled={!stripe}>
                Zaplatit
            </Button>
            <Button onClick={onCancel} variant="outlined">
                Zrušit
            </Button>
        </form>
    );
};

export default StorageCheckoutForm;
