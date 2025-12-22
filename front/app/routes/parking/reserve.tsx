import { useState, useMemo } from "react";
import { useParams, useNavigate } from "react-router";
import Button from "@/components/atoms/Button";
import InputComplete from "@/components/molecules/InputComplete";
import TimeSlotPicker, {
  type TimeSlot,
} from "@/components/molecules/TimeSlotPicker";
import { useParking } from "@/hooks/useParkings";
import { useCreateReservation } from "@/hooks/useReservation";

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

  const hourlyRate = useMemo(() => {
    if (!parking?.rates) return null;
    const hourly = parking.rates.find((r) => r.type === "hourly");
    return hourly || null;
  }, [parking?.rates]);

  const calculatedPrice = useMemo(() => {
    if (!startSlot || !endSlot || !hourlyRate) return null;

    const startMinutes = startSlot.hour * 60 + startSlot.minute;
    const endMinutes = endSlot.hour * 60 + endSlot.minute;
    const durationHours = (endMinutes - startMinutes) / 60;

    if (durationHours <= 0) return null;

    const basePrice = hourlyRate.price * durationHours;
    const discount = hourlyRate.hourlyDiscount || 0;
    const finalPrice = basePrice * (1 - discount);

    return {
      basePrice: basePrice.toFixed(2),
      discount: discount * 100,
      finalPrice: finalPrice.toFixed(2),
      durationHours: durationHours.toFixed(2),
    };
  }, [startSlot, endSlot, hourlyRate]);

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

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <form
          onSubmit={handleSubmit}
          className="lg:col-span-2 bg-white p-8 rounded-3xl border border-gray-200 flex flex-col gap-6"
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
              Sélectionnez votre créneau
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
              type="submit"
              disabled={createMutation.isPending || !startSlot || !endSlot}
            >
              {createMutation.isPending ? "Réservation..." : "Confirmer"}
            </Button>
          </div>
        </form>

        <div className="bg-white p-6 rounded-3xl border border-gray-200 h-fit sticky top-8">
          <h3 className="text-lg font-bold text-secondary mb-4">
            Récapitulatif
          </h3>

          {hourlyRate ? (
            <div className="space-y-3">
              <div className="flex justify-between text-sm">
                <span className="text-gray-600">Tarif horaire</span>
                <span className="font-medium">{hourlyRate.price}€/h</span>
              </div>

              {calculatedPrice ? (
                <>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Durée</span>
                    <span className="font-medium">
                      {calculatedPrice.durationHours}h
                    </span>
                  </div>

                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Sous-total</span>
                    <span className="font-medium">
                      {calculatedPrice.basePrice}€
                    </span>
                  </div>

                  {calculatedPrice.discount > 0 && (
                    <div className="flex justify-between text-sm text-green-600">
                      <span>Réduction ({calculatedPrice.discount}%)</span>
                      <span>
                        -
                        {(
                          parseFloat(calculatedPrice.basePrice) -
                          parseFloat(calculatedPrice.finalPrice)
                        ).toFixed(2)}
                        €
                      </span>
                    </div>
                  )}

                  <div className="border-t border-gray-200 pt-3 mt-3">
                    <div className="flex justify-between">
                      <span className="font-bold text-secondary">Total</span>
                      <span className="font-bold text-secondary text-xl">
                        {calculatedPrice.finalPrice}€
                      </span>
                    </div>
                  </div>
                </>
              ) : (
                <p className="text-gray-400 text-sm italic">
                  Sélectionnez un créneau pour voir le prix
                </p>
              )}
            </div>
          ) : (
            <p className="text-gray-400 text-sm italic">
              Aucun tarif horaire configuré
            </p>
          )}
        </div>
      </div>
    </div>
  );
}
