import type { SubscriptionType } from "@/types/SubscriptionTypes";
import { cn } from "cn-utility";

interface SubscriptionTypeOption {
  value: SubscriptionType;
  label: string;
  description: string;
}

const subscriptionTypes: SubscriptionTypeOption[] = [
  {
    value: "total",
    label: "Total",
    description: "Accès illimité 24h/24, 7j/7",
  },
  {
    value: "weekend",
    label: "Week-end",
    description: "Du vendredi 18h au lundi 10h",
  },
  {
    value: "evening",
    label: "Soir",
    description: "Tous les soirs de 18h à 8h",
  },
  {
    value: "custom",
    label: "Personnalisé",
    description: "Choisissez vos créneaux",
  },
];

interface SubscriptionTypeSelectorProps {
  value: SubscriptionType;
  onChange: (value: SubscriptionType) => void;
  className?: string;
}

const SubscriptionTypeSelector = ({
  value,
  onChange,
  className,
}: SubscriptionTypeSelectorProps) => {
  return (
    <div className={cn("flex flex-col gap-3", className)}>
      <label className="font-semibold text-secondary font-inter">
        Type d&apos;abonnement
      </label>
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
        {subscriptionTypes.map((option) => (
          <button
            key={option.value}
            type="button"
            onClick={() => onChange(option.value)}
            className={cn(
              "p-4 rounded-2xl border-2 text-left transition-all cursor-pointer",
              value === option.value
                ? "border-accent bg-accent/5"
                : "border-tertiary/30 hover:border-tertiary",
            )}
          >
            <div className="flex items-center gap-3">
              <div
                className={cn(
                  "w-5 h-5 rounded-full border-2 flex items-center justify-center",
                  value === option.value ? "border-accent" : "border-tertiary",
                )}
              >
                {value === option.value && (
                  <div className="w-2.5 h-2.5 rounded-full bg-accent" />
                )}
              </div>
              <span className="font-semibold text-secondary">
                {option.label}
              </span>
            </div>
            <p className="text-sm text-tertiary mt-2 ml-8">
              {option.description}
            </p>
          </button>
        ))}
      </div>
    </div>
  );
};

export default SubscriptionTypeSelector;
