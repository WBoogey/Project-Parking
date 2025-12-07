import type { RadioOption } from "@/types/ComponentTypes";
import { cn } from "cn-utility";
import RadioInput from "../atoms/RadioInput";

interface RadioGroupProps {
  name: string;
  options: RadioOption[];
  value: string;
  onChange: (value: string) => void;
  label?: string;
  required?: boolean;
  className?: string;
}

const RadioGroup = ({
  name,
  options,
  value,
  onChange,
  label,
  required = false,
  className,
}: RadioGroupProps) => {
  return (
    <div className={cn("flex flex-col gap-3", className)}>
      {label && (
        <label className="font-semibold text-secondary font-inter">
          {label}
          {required && " *"}
        </label>
      )}
      <div className="flex flex-wrap gap-6">
        {options.map((option) => (
          <RadioInput
            key={option.value}
            id={`${name}-${option.value}`}
            name={name}
            value={option.value}
            label={option.label}
            checked={value === option.value}
            onChange={() => onChange(option.value)}
            required={required}
          />
        ))}
      </div>
    </div>
  );
};

export default RadioGroup;

