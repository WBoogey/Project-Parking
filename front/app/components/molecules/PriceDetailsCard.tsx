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
  onClick?: () => void;
  isSelected?: boolean;
}

const PriceDetailsCard = ({
  variant = "car",
  capacity,
  price,
  frequency,
  className,
  onClick,
  isSelected,
}: PriceDetailsCardProps) => {
  const Icon = variant === "car" ? PopiconsCarLine : PopiconsScooterLine;

  const Component = onClick ? "button" : "div";

  return (
    <Component
      onClick={onClick}
      type={onClick ? "button" : undefined}
      className={cn(
        "w-full flex items-center justify-between px-3 py-2 rounded-xl",
        onClick && "cursor-pointer border",
        isSelected ? "border-accent" : "border-tertiary",
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
    </Component>
  );
};

export default PriceDetailsCard;
