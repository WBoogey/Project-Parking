import { cn } from "cn-utility";

type FrequencyType = "weekly" | "monthly" | "yearly";

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
    weekly: "semaine",
    monthly: "mois",
    yearly: "an",
  };

  return (
    <div
      className={cn(
        "flex items-start font-semibold text-sm text-secondary",
        variant === "inline" ? "gap-1" : "flex-col justify-between h-full",
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
