import type { WeeklySlot } from "@/types/SubscriptionTypes";
import { cn } from "cn-utility";

const DAYS = [
  { value: 1, label: "Lundi" },
  { value: 2, label: "Mardi" },
  { value: 3, label: "Mercredi" },
  { value: 4, label: "Jeudi" },
  { value: 5, label: "Vendredi" },
  { value: 6, label: "Samedi" },
  { value: 0, label: "Dimanche" },
];

const HOURS = Array.from({ length: 24 }, (_, i) => {
  const hour = i.toString().padStart(2, "0");
  return `${hour}:00`;
});

interface DaySlot {
  enabled: boolean;
  startHour: string;
  endHour: string;
}

interface WeeklySlotPickerProps {
  value: WeeklySlot[];
  onChange: (slots: WeeklySlot[]) => void;
  className?: string;
}

const WeeklySlotPicker = ({
  value,
  onChange,
  className,
}: WeeklySlotPickerProps) => {
  const getDaySlot = (dayOfWeek: number): DaySlot => {
    const slot = value.find((s) => s.dayOfWeek === dayOfWeek);
    return {
      enabled: !!slot,
      startHour: slot?.startHour ?? "08:00",
      endHour: slot?.endHour ?? "18:00",
    };
  };

  const updateDaySlot = (dayOfWeek: number, updates: Partial<DaySlot>) => {
    const currentSlot = getDaySlot(dayOfWeek);
    const newSlot = { ...currentSlot, ...updates };

    let newSlots: WeeklySlot[];

    if (updates.enabled === false) {
      newSlots = value.filter((s) => s.dayOfWeek !== dayOfWeek);
    } else if (updates.enabled === true || currentSlot.enabled) {
      const existingIndex = value.findIndex((s) => s.dayOfWeek === dayOfWeek);
      const slotData: WeeklySlot = {
        dayOfWeek,
        startHour: newSlot.startHour,
        endHour: newSlot.endHour,
      };

      if (existingIndex >= 0) {
        newSlots = [...value];
        newSlots[existingIndex] = slotData;
      } else {
        newSlots = [...value, slotData];
      }
    } else {
      newSlots = value;
    }

    onChange(newSlots);
  };

  return (
    <div className={cn("flex flex-col gap-4", className)}>
      <label className="font-semibold text-secondary font-inter">
        Créneaux personnalisés
      </label>
      <div className="flex flex-col gap-3">
        {DAYS.map((day) => {
          const daySlot = getDaySlot(day.value);
          return (
            <div
              key={day.value}
              className={cn(
                "flex flex-wrap items-center gap-4 p-4 rounded-xl border transition-colors",
                daySlot.enabled
                  ? "border-accent/30 bg-accent/5"
                  : "border-tertiary/20",
              )}
            >
              <label className="flex items-center gap-3 min-w-32 cursor-pointer">
                <input
                  type="checkbox"
                  checked={daySlot.enabled}
                  onChange={(e) =>
                    updateDaySlot(day.value, { enabled: e.target.checked })
                  }
                  className="w-5 h-5 accent-accent cursor-pointer"
                />
                <span
                  className={cn(
                    "font-medium",
                    daySlot.enabled ? "text-secondary" : "text-tertiary",
                  )}
                >
                  {day.label}
                </span>
              </label>

              {daySlot.enabled && (
                <div className="flex items-center gap-2 flex-wrap">
                  <select
                    value={daySlot.startHour}
                    onChange={(e) =>
                      updateDaySlot(day.value, { startHour: e.target.value })
                    }
                    className="px-3 py-2 rounded-xl border border-tertiary/30 text-secondary bg-white cursor-pointer"
                  >
                    {HOURS.map((hour) => (
                      <option key={hour} value={hour}>
                        {hour}
                      </option>
                    ))}
                  </select>
                  <span className="text-tertiary">à</span>
                  <select
                    value={daySlot.endHour}
                    onChange={(e) =>
                      updateDaySlot(day.value, { endHour: e.target.value })
                    }
                    className="px-3 py-2 rounded-xl border border-tertiary/30 text-secondary bg-white cursor-pointer"
                  >
                    {HOURS.map((hour) => (
                      <option key={hour} value={hour}>
                        {hour}
                      </option>
                    ))}
                  </select>
                </div>
              )}
            </div>
          );
        })}
      </div>
      {value.length === 0 && (
        <p className="text-sm text-tertiary italic">
          Sélectionnez au moins un jour pour votre abonnement.
        </p>
      )}
    </div>
  );
};

export default WeeklySlotPicker;
