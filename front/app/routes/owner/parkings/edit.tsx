import { useState, useEffect, type FormEvent } from "react";
import { useParams, useNavigate } from "react-router";
import Button from "@/components/atoms/Button";
import InputComplete from "@/components/molecules/InputComplete";
import { useOwnerParkings, useUpdateParking } from "@/hooks/useOwner";

export default function EditParking() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { data: parkings, isLoading } = useOwnerParkings();
  const updateMutation = useUpdateParking();

  const parking = parkings?.find((p) => p.id === id);

  const [location, setLocation] = useState("");
  const [capacity, setCapacity] = useState("");

  useEffect(() => {
    if (parking) {
      setLocation(parking.location);
      setCapacity(parking.capacity.toString());
    }
  }, [parking]);

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    if (!id || !location || !capacity) return;

    updateMutation.mutate({
      id,
      data: {
        location,
        capacity: parseInt(capacity, 10),
      },
    });
  };

  if (isLoading) {
    return <div className="p-8 text-center">Chargement...</div>;
  }

  if (!parking) {
    return <div className="p-8 text-center">Parking non trouvé.</div>;
  }

  return (
    <div className="max-w-2xl mx-auto">
      <h1 className="text-2xl font-bold text-secondary mb-8">
        Modifier le parking
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
          <Button type="submit" disabled={updateMutation.isPending}>
            {updateMutation.isPending ? "Enregistrement..." : "Enregistrer"}
          </Button>
        </div>

        {updateMutation.isError && (
          <p className="text-red-500 text-sm">
            Une erreur est survenue lors de la modification.
          </p>
        )}
      </form>
    </div>
  );
}
