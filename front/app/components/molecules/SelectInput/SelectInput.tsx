import { useState, useRef, useEffect } from "react";
import SelectInputChoice from "./SelectInputChoice";
import SelectInputTrigger from "./SelectInputTrigger";
import type { SelectVariantType } from "@/types/ComponentTypes";
import { cn } from "cn-utility";

interface SelectInputProps {
  placeholder: string;
  choices: string[];
  variant?: SelectVariantType;
  value?: string;
  onChange?: (value: string) => void;
  disabled?: boolean;
}

const SelectInput = ({
  placeholder,
  choices,
  variant = "md",
  value: controlledValue,
  onChange,
  disabled,
}: SelectInputProps) => {
  const [isOpen, setIsOpen] = useState(false);
  const [internalValue, setInternalValue] = useState<string | null>(null);

  const isControlled = onChange !== undefined;
  const selectedValue = isControlled ? controlledValue : internalValue;
  const dropdownRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (
        dropdownRef.current &&
        !dropdownRef.current.contains(event.target as Node)
      ) {
        setIsOpen(false);
      }
    };

    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  const handleSelect = (choice: string) => {
    if (isControlled && onChange) {
      onChange(choice);
    } else {
      setInternalValue(choice);
    }
    setIsOpen(false);
  };

  return (
    <div
      ref={dropdownRef}
      className={cn("relative", {
        "w-36": variant === "sm",
        "w-60": variant === "md",
        "w-96": variant === "lg",
        "w-full": variant === "full",
      })}
    >
      <SelectInputTrigger
        onClick={() => !disabled && setIsOpen(!isOpen)}
        text={selectedValue || placeholder}
        isOpen={isOpen}
        disabled={disabled}
      />

      <div
        className={cn(
          "absolute min-w-52 w-2/3 overflow-hidden transition-all duration-75 flex flex-col",
          {
            "left-0": ["sm", "md"].includes(variant),
            "right-0": ["lg", "full"].includes(variant),
          },
          isOpen
            ? "max-h-96 opacity-100 top-full z-100"
            : "max-h-0 opacity-0 pointer-events-none top-1/2",
        )}
      >
        <div className="flex flex-col bg-primary divide-y divide-tertiary border border-tertiary rounded-xl px-3 py-1.5">
          {choices.map((choice: string) => (
            <SelectInputChoice
              key={choice}
              choice={choice}
              onSelect={() => handleSelect(choice)}
            />
          ))}
        </div>
      </div>
    </div>
  );
};

export default SelectInput;
