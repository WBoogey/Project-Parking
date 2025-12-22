import { useMemo, useState } from "react";

interface TimeSlot {
  hour: number;
  minute: number;
}

interface TimeSlotPickerProps {
  selectedStart: TimeSlot | null;
  selectedEnd: TimeSlot | null;
  onStartChange: (slot: TimeSlot | null) => void;
  onEndChange: (slot: TimeSlot | null) => void;
  minHour?: number;
  maxHour?: number;
  date: string;
}

const QUARTERS = [0, 15, 30, 45];

type SelectionMode = "start" | "end";

export default function TimeSlotPicker({
  selectedStart,
  selectedEnd,
  onStartChange,
  onEndChange,
  minHour = 6,
  maxHour = 23,
  date,
}: TimeSlotPickerProps) {
  const [mode, setMode] = useState<SelectionMode>("start");

  const now = new Date();
  const selectedDate = new Date(date);
  const isToday = selectedDate.toDateString() === now.toDateString();

  const hours = useMemo(() => {
    const result: number[] = [];
    for (let h = minHour; h <= maxHour; h++) {
      result.push(h);
    }
    return result;
  }, [minHour, maxHour]);

  const isSlotDisabled = (hour: number, minute: number): boolean => {
    if (!isToday) return false;
    const slotEnd = new Date(selectedDate);
    slotEnd.setHours(hour, minute + 15, 0, 0);
    return slotEnd <= now;
  };

  const isSlotBeforeStart = (hour: number, minute: number): boolean => {
    if (!selectedStart || mode !== "end") return false;
    const slotMinutes = hour * 60 + minute;
    const startMinutes = selectedStart.hour * 60 + selectedStart.minute;
    return slotMinutes < startMinutes;
  };

  const isSlotInRange = (hour: number, minute: number): boolean => {
    if (!selectedStart || !selectedEnd) return false;
    const slotMinutes = hour * 60 + minute;
    const startMinutes = selectedStart.hour * 60 + selectedStart.minute;
    const endMinutes = selectedEnd.hour * 60 + selectedEnd.minute;
    return slotMinutes > startMinutes && slotMinutes < endMinutes;
  };

  const isStartSlot = (hour: number, minute: number): boolean => {
    if (!selectedStart) return false;
    return selectedStart.hour === hour && selectedStart.minute === minute;
  };

  const isEndSlot = (hour: number, minute: number): boolean => {
    if (!selectedEnd) return false;
    return selectedEnd.hour === hour && selectedEnd.minute === minute;
  };

  const handleSlotClick = (hour: number, minute: number) => {
    if (isSlotDisabled(hour, minute)) return;
    if (mode === "end" && isSlotBeforeStart(hour, minute)) return;

    if (mode === "start") {
      onStartChange({ hour, minute });
      onEndChange(null);
      setMode("end");
    } else {
      const clickedMinutes = hour * 60 + minute;
      const startMinutes = selectedStart!.hour * 60 + selectedStart!.minute;
      
      if (clickedMinutes === startMinutes) {
        const endMinute = minute + 15;
        const endHour = endMinute >= 60 ? hour + 1 : hour;
        onEndChange({ hour: endHour, minute: endMinute % 60 });
      } else {
        const endMinute = minute + 15;
        const endHour = endMinute >= 60 ? hour + 1 : hour;
        onEndChange({ hour: endHour, minute: endMinute % 60 });
      }
      setMode("start");
    }
  };

  const handleReset = () => {
    onStartChange(null);
    onEndChange(null);
    setMode("start");
  };

  const formatTime = (hour: number, minute: number): string => {
    return `${hour.toString().padStart(2, "0")}:${minute.toString().padStart(2, "0")}`;
  };

  const getDurationMinutes = (): number => {
    if (!selectedStart || !selectedEnd) return 0;
    const startMinutes = selectedStart.hour * 60 + selectedStart.minute;
    const endMinutes = selectedEnd.hour * 60 + selectedEnd.minute;
    return endMinutes - startMinutes;
  };

  const formatDuration = (minutes: number): string => {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hours === 0) return `${mins} min`;
    if (mins === 0) return `${hours}h`;
    return `${hours}h${mins}`;
  };

  return (
    <div className="w-full">
      <div className="mb-4 flex items-center justify-between">
        <div className="flex items-center gap-2">
          <div
            className={`px-3 py-1 rounded-full text-sm font-medium ${
              mode === "start"
                ? "bg-blue-600 text-white"
                : "bg-gray-200 text-gray-600"
            }`}
          >
            1. Début
          </div>
          <div className="text-gray-400">→</div>
          <div
            className={`px-3 py-1 rounded-full text-sm font-medium ${
              mode === "end"
                ? "bg-blue-600 text-white"
                : "bg-gray-200 text-gray-600"
            }`}
          >
            2. Fin
          </div>
        </div>
        {(selectedStart || selectedEnd) && (
          <button
            type="button"
            onClick={handleReset}
            className="text-sm text-red-500 hover:text-red-700"
          >
            Réinitialiser
          </button>
        )}
      </div>

      <div className="overflow-x-auto">
        <div className="min-w-[600px]">
          <div className="flex gap-1">
            <div className="w-16 flex-shrink-0" />
            {QUARTERS.map((q) => (
              <div
                key={q}
                className="flex-1 text-center text-xs text-gray-500 font-medium"
              >
                :{q.toString().padStart(2, "0")}
              </div>
            ))}
          </div>
          <div className="flex flex-col gap-1 mt-2">
            {hours.map((hour) => (
              <div key={hour} className="flex gap-1 items-center">
                <div className="w-16 flex-shrink-0 text-sm font-medium text-gray-700">
                  {hour.toString().padStart(2, "0")}:00
                </div>
                {QUARTERS.map((minute) => {
                  const disabled = isSlotDisabled(hour, minute);
                  const beforeStart = isSlotBeforeStart(hour, minute);
                  const inRange = isSlotInRange(hour, minute);
                  const isStart = isStartSlot(hour, minute);
                  const isEnd = isEndSlot(hour, minute);
                  const isDisabledInEndMode = mode === "end" && beforeStart;

                  return (
                    <button
                      key={minute}
                      type="button"
                      disabled={disabled || isDisabledInEndMode}
                      onClick={() => handleSlotClick(hour, minute)}
                      className={`
                        flex-1 h-10 rounded-lg border-2 transition-all text-xs font-medium
                        ${disabled || isDisabledInEndMode ? "bg-gray-100 border-gray-200 text-gray-300 cursor-not-allowed" : ""}
                        ${!disabled && !isDisabledInEndMode && !inRange && !isStart && !isEnd ? "bg-white border-gray-200 hover:border-blue-400 hover:bg-blue-50 cursor-pointer" : ""}
                        ${isStart ? "bg-green-500 border-green-500 text-white" : ""}
                        ${isEnd ? "bg-red-500 border-red-500 text-white" : ""}
                        ${inRange ? "bg-blue-100 border-blue-300" : ""}
                      `}
                      title={formatTime(hour, minute)}
                    >
                      {isStart && "▶"}
                      {isEnd && "◼"}
                    </button>
                  );
                })}
              </div>
            ))}
          </div>
        </div>
      </div>

      {selectedStart && (
        <div className="mt-4 p-4 bg-blue-50 rounded-xl">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-blue-800 font-medium">
                {selectedEnd ? (
                  <>
                    {formatTime(selectedStart.hour, selectedStart.minute)} →{" "}
                    {formatTime(selectedEnd.hour, selectedEnd.minute)}
                  </>
                ) : (
                  <>
                    Début : {formatTime(selectedStart.hour, selectedStart.minute)} — Cliquez sur la case de fin
                  </>
                )}
              </p>
              {selectedEnd && (
                <p className="text-blue-600 text-sm mt-1">
                  Durée : {formatDuration(getDurationMinutes())}
                </p>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export type { TimeSlot };
