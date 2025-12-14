import PriceDetailsCard from "@/components/molecules/PriceDetailsCard";
import type { SpotPricing } from "@/types/PricingCardTypes";
import type { SpotVariant } from "@/types/SpotsTypes";

interface PricingCardMonthlyProps {
  spots: SpotPricing[];
  selectedVariant: SpotVariant | undefined;
  onSelect: (variant: SpotVariant) => void;
}

const PricingCardMonthly = ({
  spots,
  selectedVariant,
  onSelect,
}: PricingCardMonthlyProps) => {
  return (
    <div className="flex flex-col gap-4">
      <p className="text-secondary font-medium">Pour tout véhicule</p>
      <div className="flex flex-col gap-3">
        {spots.map((spot) => (
          <PriceDetailsCard
            key={spot.variant}
            variant={spot.variant}
            price={spot.price}
            frequency={spot.frequency}
            onClick={() => onSelect(spot.variant)}
            isSelected={selectedVariant === spot.variant}
          />
        ))}
      </div>
    </div>
  );
};

export default PricingCardMonthly;
