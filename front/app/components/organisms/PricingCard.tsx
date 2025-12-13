import {
  PopiconsCalendarLine,
  PopiconsChevronRightLine,
  PopiconsClockLine,
} from "@popicons/react";
import { cn } from "cn-utility";
import type { PricingCardItemData } from "@/types/ParkingTypes";
import PricingCardItem from "./PricingCardItem";

interface PricingCardProps {
  variant?: "monthly" | "hourly";
  price?: number;
  items?: PricingCardItemData[];
  className?: string;
  onClick: () => void;
}

const PricingCard = ({
  variant = "hourly",
  price,
  items,
  className,
  onClick,
}: PricingCardProps) => {
  const config = {
    Icon: variant === "hourly" ? PopiconsClockLine : PopiconsCalendarLine,
    text: variant === "hourly" ? "À l'heure" : "Au mois",
  };

  return (
    <button
      onClick={onClick}
      className={cn(
        "w-102 bg-primary border border-tertiary rounded-xl px-3 py-4 flex flex-col gap-3 cursor-pointer",
        className,
      )}
    >
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3 font-medium text-secondary">
          <config.Icon />
          {config.text}
        </div>

        <div className="flex items-center">
          {price && (
            <span className="text-secondary font-medium">{price}€</span>
          )}
          <PopiconsChevronRightLine color="black" className="size-5" />
        </div>
      </div>
      {items && items.length > 0 && (
        <ul className="flex flex-col border-t border-tertiary pt-3">
          {items.map((item) => (
            <PricingCardItem
              key={item.label}
              label={item.label}
              price={item.price}
            />
          ))}
        </ul>
      )}
    </button>
  );
};

export default PricingCard;

