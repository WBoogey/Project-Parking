import { cn } from "cn-utility";
import type { PricingMode } from "@/types/PricingCardTypes";

interface PricingCardToggleProps {
  mode: PricingMode;
  onChange: (mode: PricingMode) => void;
}

const PricingCardToggle = ({ mode, onChange }: PricingCardToggleProps) => {
  return (
    <div className="flex w-full rounded-xl overflow-hidden">
      <button
        type="button"
        onClick={() => onChange("monthly")}
        className={cn(
          "flex-1 py-3 font-medium transition-colors cursor-pointer",
          mode === "monthly"
            ? "bg-accent text-primary"
            : "bg-tertiary/30 text-secondary",
        )}
      >
        Au mois
      </button>
      <button
        type="button"
        onClick={() => onChange("hourlyDaily")}
        className={cn(
          "flex-1 py-3 font-medium transition-colors cursor-pointer",
          mode === "hourlyDaily"
            ? "bg-accent text-primary"
            : "bg-tertiary/30 text-secondary",
        )}
      >
        Heure/Jour
      </button>
    </div>
  );
};

export default PricingCardToggle;
