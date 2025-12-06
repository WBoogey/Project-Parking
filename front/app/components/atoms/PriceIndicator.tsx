import type { FrequencyType } from "@/types/SpotsTypes";
import { cn } from "cn-utility";

interface PriceIndicatorProps {
  price: number;
  frequency?: FrequencyType;
  variant?: "default" | "inline";
  className?: string;
}

const PriceIndicator = ({
  price,
  frequency = "monthly",
  variant = "default",
  className,
}: PriceIndicatorProps) => {
  const frequencyDisplay = {
    monthly: "mois",
    daily: "jour",
    hourly: "heure",
  };

  return (
    <div
      className={cn(
        "flex items-start font-semibold text-sm text-secondary",
        variant === "inline" ? "gap-1" : "flex-col justify-between",
        className,
      )}
    >
      <p>{price}EUR</p>
      <p className={cn(variant === "default" && "text-tertiary")}>
        {`${variant === "inline" ? "/" : "Par "}${frequencyDisplay[frequency]}`}
      </p>
    </div>
  );
};

export default PriceIndicator;
