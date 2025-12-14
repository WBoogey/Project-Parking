import { useState } from "react";
import { cn } from "cn-utility";
import Button from "@/components/atoms/Button";
import PricingCardToggle from "./PricingCardToggle";
import PricingCardMonthly from "./PricingCardMonthly";
import PricingCardHourlyDaily from "./PricingCardHourlyDaily";
import type { PricingCardData, PricingMode } from "@/types/PricingCardTypes";
import type { SpotVariant } from "@/types/SpotsTypes";

interface PricingCardProps {
  data: PricingCardData;
  onSubmit: () => void;
  className?: string;
}

const PricingCard = ({ data, onSubmit, className }: PricingCardProps) => {
  const [mode, setMode] = useState<PricingMode>("monthly");
  const [selectedMonthlyVariant, setSelectedMonthlyVariant] = useState<
    SpotVariant | undefined
  >(data.spots.length === 1 ? data.spots[0].variant : undefined);
  const [selectedSpot, setSelectedSpot] = useState<string | undefined>();
  const [startDay, setStartDay] = useState<string | undefined>();
  const [startHour, setStartHour] = useState<string | undefined>();
  const [endDay, setEndDay] = useState<string | undefined>();
  const [endHour, setEndHour] = useState<string | undefined>();

  const currentSpot = data.spots.find(
    (spot) => (spot.variant === "car" ? "Voiture" : "Moto") === selectedSpot,
  );
  const displayPrice = currentSpot?.price ?? data.spots[0]?.price ?? 0;

  const isMonthlyValid = selectedMonthlyVariant !== undefined;
  const isSpotSelected = data.spots.length === 1 || selectedSpot !== undefined;
  const isHourlyDailyValid =
    isSpotSelected &&
    startDay !== undefined &&
    startHour !== undefined &&
    endDay !== undefined &&
    endHour !== undefined;
  const isFormValid = mode === "monthly" ? isMonthlyValid : isHourlyDailyValid;

  return (
    <div
      className={cn(
        "w-124 bg-primary rounded-3xl p-5 flex flex-col gap-5",
        className,
      )}
    >
      <PricingCardToggle mode={mode} onChange={setMode} />

      {mode === "monthly" ? (
        <PricingCardMonthly
          spots={data.spots}
          selectedVariant={selectedMonthlyVariant}
          onSelect={setSelectedMonthlyVariant}
        />
      ) : (
        <PricingCardHourlyDaily
          spots={data.spots}
          availableSlots={data.availableSlots}
          selectedSpot={selectedSpot}
          onSpotChange={setSelectedSpot}
          startDay={startDay}
          onStartDayChange={setStartDay}
          startHour={startHour}
          onStartHourChange={setStartHour}
          endDay={endDay}
          onEndDayChange={setEndDay}
          endHour={endHour}
          onEndHourChange={setEndHour}
          price={displayPrice}
        />
      )}

      <Button onClick={onSubmit} size="full" disabled={!isFormValid}>
        J&apos;essaie ce parking
      </Button>
    </div>
  );
};

export default PricingCard;
