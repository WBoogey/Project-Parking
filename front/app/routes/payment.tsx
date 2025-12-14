import { useState } from "react";
import { useNavigate } from "react-router";
import Button from "@/components/atoms/Button";
import InputComplete from "@/components/molecules/InputComplete";
import React from "react";

export default function Payment() {
  const navigate = useNavigate();
  const [isSuccess, setIsSuccess] = useState(false);

  const handlePayment = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSuccess(true);
    setTimeout(() => {}, 2000);
  };

  if (isSuccess) {
    return (
      <div className="container mx-auto py-16 px-4 text-center">
        <div className="bg-white p-12 rounded-3xl max-w-lg mx-auto shadow-sm border border-gray-100">
          <div className="text-5xl mb-6">🎉</div>
          <h1 className="text-2xl font-bold text-secondary mb-4">
            Réservation confirmée !
          </h1>
          <p className="text-gray-600 mb-8">
            Votre paiement a été accepté (simulé). Vous recevrez un email de
            confirmation.
          </p>
          <Button onClick={() => navigate("/owner/dashboard")}>
            Voir mes réservations
          </Button>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-2xl mx-auto">
      <h1 className="text-2xl font-bold text-secondary mb-8">Paiement</h1>

      <form
        onSubmit={handlePayment}
        className="bg-white p-8 rounded-3xl border border-gray-200 flex flex-col gap-6"
      >
        <div className="bg-blue-50 p-4 rounded-xl text-secondary text-sm mb-2">
          Ceci est une simulation. Aucune somme ne sera débitée.
        </div>

        <div className="grid grid-cols-2 gap-4">
          <InputComplete
            id="firstName"
            label="Prénom"
            placeholder="John"
            variant="full"
            required
          />
          <InputComplete
            id="lastName"
            label="Nom"
            placeholder="Doe"
            variant="full"
            required
          />
        </div>

        <InputComplete
          id="card"
          label="Numéro de carte"
          placeholder="0000 0000 0000 0000"
          variant="full"
          required
        />

        <div className="grid grid-cols-2 gap-4">
          <InputComplete
            id="expiry"
            label="Date d'expiration"
            placeholder="MM/YY"
            variant="full"
            required
          />
          <InputComplete
            id="cvc"
            label="CVC"
            placeholder="123"
            variant="full"
            required
          />
        </div>

        <div className="border-t border-gray-100 pt-6 mt-2">
          <div className="flex justify-between items-center mb-6">
            <span className="font-semibold text-secondary">Total à payer</span>
            <span className="font-bold text-2xl text-secondary">15,00 €</span>
          </div>

          <Button type="submit" size="full" onClick={() => {}}>
            Payer 15,00 €
          </Button>
        </div>
      </form>
    </div>
  );
}
