import { useState, useMemo } from "react";
import { useParams, useNavigate } from "react-router";
import Button from "@/components/atoms/Button";
import InputComplete from "@/components/molecules/InputComplete";
import TimeSlotPicker from "@/components/molecules/TimeSlotPicker";
import { useParking } from "@/hooks/useParkings";
import { useCreateReservation } from "@/hooks/useReservation";

interface TimeSlot {
  hour: number;
  minute: number;
}

export default function ReserveParking() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { data: parking, isLoading: parkingLoading } = useParking(id);
  const createMutation = useCreateReservation();

  const today = new Date().toISOString().split("T")[0];
  const [selectedDate, setSelectedDate] = useState(today);
  const [startSlot, setStartSlot] = useState<TimeSlot | null>(null);
  const [endSlot, setEndSlot] = useState<TimeSlot | null>(null);
  const [error, setError] = useState<string | null>(null);

  const minDate = useMemo(() => {
    return new Date().toISOString().split("T")[0];
  }, []);

  const formatTimeSlot = (slot: TimeSlot): string => {
    return `${slot.hour.toString().padStart(2, "0")}:${slot.minute.toString().padStart(2, "0")}`;
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);

    if (!id || !startSlot || !endSlot) {
      setError("Veuillez sélectionner un créneau horaire.");
      return;
    }

    const startDateTime = `${selectedDate}T${formatTimeSlot(startSlot)}:00`;
    const endDateTime = `${selectedDate}T${formatTimeSlot(endSlot)}:00`;

    const startDate = new Date(startDateTime);
    const endDate = new Date(endDateTime);
    const now = new Date();

    if (startDate < now) {
      setError("La date et heure de début ne peuvent pas être dans le passé.");
      return;
    }

    if (endDate <= startDate) {
      setError("L'heure de fin doit être après l'heure de début.");
      return;
    }

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
        onError: (err) => {
          setError(
            err instanceof Error
              ? err.message
              : "Une erreur est survenue. Veuillez réessayer.",
          );
        },
      },
    );
  };

  const handleDateChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const newDate = e.target.value;
    if (newDate < minDate) return;
    setSelectedDate(newDate);
    setStartSlot(null);
    setEndSlot(null);
  };

  if (parkingLoading) {
    return <div className="p-8 text-center">Chargement...</div>;
  }

  if (!parking) {
    return <div className="p-8 text-center">Parking non trouvé.</div>;
  }

  return (
    <div className="max-w-4xl mx-auto">
      <h1 className="text-2xl font-bold text-secondary mb-2">
        Réserver une place
      </h1>
      <p className="text-tertiary mb-8">{parking.location}</p>

      <form
        onSubmit={handleSubmit}
        className="bg-white p-8 rounded-3xl border border-gray-200 flex flex-col gap-6"
      >
        <InputComplete
          id="date"
          label="Date de réservation"
          type="date"
          variant="full"
          value={selectedDate}
          onChange={handleDateChange}
          min={minDate}
          required
        />

        <div>
          <label className="block text-sm font-medium text-secondary mb-4">
            Sélectionnez votre créneau (par tranches de 15 minutes)
          </label>
          <TimeSlotPicker
            selectedStart={startSlot}
            selectedEnd={endSlot}
            onStartChange={setStartSlot}
            onEndChange={setEndSlot}
            date={selectedDate}
            minHour={6}
            maxHour={22}
          />
        </div>

        {error && (
          <div className="p-4 bg-red-50 border border-red-200 rounded-xl">
            <p className="text-red-600 text-sm">{error}</p>
          </div>
        )}

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
            disabled={createMutation.isPending || !startSlot || !endSlot}
          >
            {createMutation.isPending ? "Réservation..." : "Confirmer"}
          </Button>
        </div>
      </form>
    </div>
  );
}
