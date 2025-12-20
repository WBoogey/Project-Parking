import { useState } from "react";
import { useNavigate } from "react-router";
import Button from "@/components/atoms/Button";
import InputComplete from "@/components/molecules/InputComplete";
import { useAddParking } from "@/hooks/useOwner";
import React from "react";

export default function AddParking() {
  const navigate = useNavigate();
  const { mutate, isPending, isError } = useAddParking();

  const [location, setLocation] = useState("");
  const [capacity, setCapacity] = useState("");

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!location || !capacity) return;

    mutate({
      location,
      capacity: parseInt(capacity, 10),
    });
  };

  return (
    <div className="max-w-2xl mx-auto">
      <h1 className="text-2xl font-bold text-secondary mb-8">
        Ajouter un parking
      </h1>

      <form
        onSubmit={handleSubmit}
        className="bg-white p-8 rounded-3xl border border-gray-200 flex flex-col gap-6"
      >
        <InputComplete
          id="location"
          label="Adresse du parking"
          placeholder="ex: 12 Rue de la Paix, 75000 Paris"
          variant="full"
          value={location}
          onChange={(e) => setLocation(e.target.value)}
          required
        />

        <InputComplete
          id="capacity"
          label="Nombre de places"
          placeholder="ex: 10"
          type="number"
          variant="full"
          value={capacity}
          onChange={(e) => setCapacity(e.target.value)}
          required
        />

        <div className="flex justify-end gap-4 mt-4">
          <Button
            variant="outline"
            onClick={() => navigate("/owner")}
            type="button"
          >
            Annuler
          </Button>
          <Button onClick={() => {}} type="submit" disabled={isPending}>
            {isPending ? "Ajout..." : "Ajouter le parking"}
          </Button>
        </div>

        {isError && (
          <p className="text-red-500 text-sm">
            Une erreur est survenue lors de l&apos;ajout.
          </p>
        )}
      </form>
    </div>
  );
}
