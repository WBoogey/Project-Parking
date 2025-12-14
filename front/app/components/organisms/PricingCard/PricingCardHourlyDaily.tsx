import SelectInput from "@/components/molecules/SelectInput/SelectInput";
import type { AvailableSlot, SpotPricing } from "@/types/PricingCardTypes";

interface PricingCardHourlyDailyProps {
  spots: SpotPricing[];
  availableSlots: AvailableSlot[];
  selectedSpot: string | undefined;
  onSpotChange: (value: string) => void;
  startDay: string | undefined;
  onStartDayChange: (value: string) => void;
  startHour: string | undefined;
  onStartHourChange: (value: string) => void;
  endDay: string | undefined;
  onEndDayChange: (value: string) => void;
  endHour: string | undefined;
  onEndHourChange: (value: string) => void;
  price: number;
}

const PricingCardHourlyDaily = ({
  spots,
  availableSlots,
  selectedSpot,
  onSpotChange,
  startDay,
  onStartDayChange,
  startHour,
  onStartHourChange,
  endDay,
  onEndDayChange,
  endHour,
  onEndHourChange,
  price,
}: PricingCardHourlyDailyProps) => {
  const spotLabels = spots.map((spot) =>
    spot.variant === "car" ? "Voiture" : "Moto",
  );

  const availableDays = availableSlots.map((slot) => slot.day);

  const startHours =
    availableSlots.find((slot) => slot.day === startDay)?.hours ?? [];
  const endHours =
    availableSlots.find((slot) => slot.day === endDay)?.hours ?? [];

  return (
    <div className="flex flex-col gap-4">
      <div className="flex flex-col gap-2">
        <p className="text-secondary font-medium">
          Type de place <span className="text-accent">*</span>
        </p>
        {spots.length === 1 ? (
          <p className="text-secondary px-4 py-3 border border-tertiary rounded-xl">
            {spots[0].variant === "car" ? "Voiture" : "Moto"}
          </p>
        ) : (
          <SelectInput
            placeholder="Keyword"
            choices={spotLabels}
            variant="full"
            value={selectedSpot}
            onChange={onSpotChange}
          />
        )}
      </div>

      <div className="flex flex-col gap-2" data-testid="start-reservation">
        <p className="text-secondary font-medium">
          Début de réservation <span className="text-accent">*</span>
        </p>
        <div className="flex gap-3">
          <SelectInput
            placeholder="Jour"
            choices={availableDays}
            variant="full"
            value={startDay}
            onChange={onStartDayChange}
          />
          <SelectInput
            placeholder="00:00"
            choices={startHours}
            variant="sm"
            value={startHour}
            onChange={onStartHourChange}
            disabled={!startDay}
          />
        </div>
      </div>

      <div className="flex flex-col gap-2" data-testid="end-reservation">
        <p className="text-secondary font-medium">
          Fin de réservation <span className="text-accent">*</span>
        </p>
        <div className="flex gap-3">
          <SelectInput
            placeholder="Jour"
            choices={availableDays}
            variant="full"
            value={endDay}
            onChange={onEndDayChange}
          />
          <SelectInput
            placeholder="00:00"
            choices={endHours}
            variant="sm"
            value={endHour}
            onChange={onEndHourChange}
            disabled={!endDay}
          />
        </div>
      </div>

      <div className="bg-tertiary/30 rounded-xl py-4 px-6 flex flex-col items-center gap-1">
        <p className="text-tertiary font-medium">
          Ce créneau est disponible pour :
        </p>
        <p className="text-secondary font-bold text-xl">{price}EUR</p>
      </div>
    </div>
  );
};

export default PricingCardHourlyDaily;
