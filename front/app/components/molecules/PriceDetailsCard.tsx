import { PopiconsCarLine, PopiconsScooterLine } from "@popicons/react";
import PriceIndicator from "../atoms/PriceIndicator";
import SpotsStatus from "../atoms/SpotsStatus";
import { cn } from "cn-utility";
import type { FrequencyType, SpotVariant } from "@/types/SpotsTypes";

interface PriceDetailsCardProps {
  variant: SpotVariant;
  capacity?: number;
  price: number;
  frequency?: FrequencyType;
  className?: string;
}

const PriceDetailsCard = ({
  variant = "car",
  capacity,
  price,
  frequency,
  className,
}: PriceDetailsCardProps) => {
  const Icon = variant === "car" ? PopiconsCarLine : PopiconsScooterLine;

  return (
    <div
      className={cn(
        "w-93 flex items-center justify-between px-3 py-2 border rounded-xl",
        className,
      )}
    >
      <div className="flex items-center gap-3">
        <Icon
          data-testid={variant === "car" ? "car-icon" : "motorcycle-icon"}
        />
        <div className="flex flex-col gap-px">
          <p className="font-semibold text-secondary" data-testid="text-label">
            Place de {variant === "car" ? "voiture" : "moto"}
          </p>
          {typeof capacity === "number" && <SpotsStatus capacity={capacity} />}
        </div>
      </div>
      <PriceIndicator price={price} frequency={frequency} />
    </div>
  );
};

export default PriceDetailsCard;
