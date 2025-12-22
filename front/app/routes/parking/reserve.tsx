import { useState } from "react";
import { useParams, useNavigate } from "react-router";
import Button from "@/components/atoms/Button";
import InputComplete from "@/components/molecules/InputComplete";
import { useParking } from "@/hooks/useParkings";
import { useCreateReservation } from "@/hooks/useReservation";

export default function ReserveParking() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { data: parking, isLoading: parkingLoading } = useParking(id);
  const createMutation = useCreateReservation();

  const today = new Date().toISOString().split("T")[0];
  const [startDate, setStartDate] = useState(today);
  const [startTime, setStartTime] = useState("09:00");
  const [endDate, setEndDate] = useState(today);
  const [endTime, setEndTime] = useState("18:00");

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!id) return;

    const startDateTime = `${startDate}T${startTime}:00`;
    const endDateTime = `${endDate}T${endTime}:00`;

    createMutation.mutate(
      {
        parkingId: id,
        startTime: startDateTime,
        endTime: endDateTime,
      },
      {
        onSuccess: (data) => {
          if (data.checkoutUrl) {
            window.location.href = data.checkoutUrl;
          } else {
            navigate("/customer");
          }
        },
      },
    );
  };

  if (parkingLoading) {
    return <div className="p-8 text-center">Chargement...</div>;
  }

  if (!parking) {
    return <div className="p-8 text-center">Parking non trouvé.</div>;
  }

  return (
    <div className="max-w-2xl mx-auto">
      <h1 className="text-2xl font-bold text-secondary mb-2">
        Réserver une place
      </h1>
      <p className="text-tertiary mb-8">{parking.location}</p>

      <form
        onSubmit={handleSubmit}
        className="bg-white p-8 rounded-3xl border border-gray-200 flex flex-col gap-6"
      >
        <div className="grid grid-cols-2 gap-4">
          <InputComplete
            id="startDate"
            label="Date de début"
            type="date"
            variant="full"
            value={startDate}
            onChange={(e) => setStartDate(e.target.value)}
            required
          />
          <InputComplete
            id="startTime"
            label="Heure de début"
            type="time"
            variant="full"
            value={startTime}
            onChange={(e) => setStartTime(e.target.value)}
            required
          />
        </div>

        <div className="grid grid-cols-2 gap-4">
          <InputComplete
            id="endDate"
            label="Date de fin"
            type="date"
            variant="full"
            value={endDate}
            onChange={(e) => setEndDate(e.target.value)}
            required
          />
          <InputComplete
            id="endTime"
            label="Heure de fin"
            type="time"
            variant="full"
            value={endTime}
            onChange={(e) => setEndTime(e.target.value)}
            required
          />
        </div>

        <div className="flex justify-end gap-4 mt-4">
          <Button
            variant="outline"
            onClick={() => navigate(`/parking/${id}`)}
            type="button"
          >
            Annuler
          </Button>
          <Button
            onClick={() => {}}
            type="submit"
            disabled={createMutation.isPending}
          >
            {createMutation.isPending ? "Réservation..." : "Confirmer la réservation"}
          </Button>
        </div>

        {createMutation.isError && (
          <p className="text-red-500 text-sm">
            Une erreur est survenue. Veuillez réessayer.
          </p>
        )}
      </form>
    </div>
  );
}

