import { useMemo } from "react";

interface TimeSlot {
  hour: number;
  minute: number;
}

interface TimeSlotPickerProps {
  selectedStart: TimeSlot | null;
  selectedEnd: TimeSlot | null;
  onStartChange: (slot: TimeSlot) => void;
  onEndChange: (slot: TimeSlot) => void;
  minHour?: number;
  maxHour?: number;
  date: string;
}

const QUARTERS = [0, 15, 30, 45];

export default function TimeSlotPicker({
  selectedStart,
  selectedEnd,
  onStartChange,
  onEndChange,
  minHour = 6,
  maxHour = 23,
  date,
}: TimeSlotPickerProps) {
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
    const slotTime = new Date(selectedDate);
    slotTime.setHours(hour, minute, 0, 0);
    return slotTime <= now;
  };

  const isSlotInRange = (hour: number, minute: number): boolean => {
    if (!selectedStart || !selectedEnd) return false;
    const slotMinutes = hour * 60 + minute;
    const startMinutes = selectedStart.hour * 60 + selectedStart.minute;
    const endMinutes = selectedEnd.hour * 60 + selectedEnd.minute;
    return slotMinutes >= startMinutes && slotMinutes < endMinutes;
  };

  const isStartSlot = (hour: number, minute: number): boolean => {
    if (!selectedStart) return false;
    return selectedStart.hour === hour && selectedStart.minute === minute;
  };

  const isEndSlot = (hour: number, minute: number): boolean => {
    if (!selectedEnd) return false;
    const endMinutes = selectedEnd.hour * 60 + selectedEnd.minute;
    const slotMinutes = hour * 60 + minute + 15;
    return slotMinutes === endMinutes;
  };

  const handleSlotClick = (hour: number, minute: number) => {
    if (isSlotDisabled(hour, minute)) return;

    const clickedMinutes = hour * 60 + minute;

    if (!selectedStart || (selectedStart && selectedEnd)) {
      onStartChange({ hour, minute });
      const endMinute = minute + 15;
      const endHour = endMinute >= 60 ? hour + 1 : hour;
      onEndChange({ hour: endHour, minute: endMinute % 60 });
    } else {
      const startMinutes = selectedStart.hour * 60 + selectedStart.minute;
      if (clickedMinutes > startMinutes) {
        const endMinute = minute + 15;
        const endHour = endMinute >= 60 ? hour + 1 : hour;
        onEndChange({ hour: endHour, minute: endMinute % 60 });
      } else {
        onStartChange({ hour, minute });
      }
    }
  };

  const formatTime = (hour: number, minute: number): string => {
    return `${hour.toString().padStart(2, "0")}:${minute.toString().padStart(2, "0")}`;
  };

  return (
    <div className="w-full overflow-x-auto">
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
                const inRange = isSlotInRange(hour, minute);
                const isStart = isStartSlot(hour, minute);
                const isEnd = isEndSlot(hour, minute);

                return (
                  <button
                    key={minute}
                    type="button"
                    disabled={disabled}
                    onClick={() => handleSlotClick(hour, minute)}
                    className={`
                      flex-1 h-10 rounded-lg border-2 transition-all text-xs font-medium
                      ${disabled ? "bg-gray-100 border-gray-200 text-gray-300 cursor-not-allowed" : ""}
                      ${!disabled && !inRange && !isStart && !isEnd ? "bg-white border-gray-200 hover:border-blue-400 hover:bg-blue-50 cursor-pointer" : ""}
                      ${isStart ? "bg-blue-600 border-blue-600 text-white" : ""}
                      ${isEnd ? "bg-blue-600 border-blue-600 text-white" : ""}
                      ${inRange && !isStart && !isEnd ? "bg-blue-100 border-blue-300" : ""}
                    `}
                    title={formatTime(hour, minute)}
                  >
                    {isStart && "Début"}
                    {isEnd && "Fin"}
                  </button>
                );
              })}
            </div>
          ))}
        </div>
      </div>
      {selectedStart && selectedEnd && (
        <div className="mt-4 p-4 bg-blue-50 rounded-xl text-center">
          <p className="text-blue-800 font-medium">
            Créneau sélectionné :{" "}
            {formatTime(selectedStart.hour, selectedStart.minute)} →{" "}
            {formatTime(selectedEnd.hour, selectedEnd.minute)}
          </p>
        </div>
      )}
    </div>
  );
}

