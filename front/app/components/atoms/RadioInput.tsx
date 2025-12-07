import { cn } from "cn-utility";
import type { InputHTMLAttributes } from "react";

interface RadioInputProps extends InputHTMLAttributes<HTMLInputElement> {
  id: string;
  name: string;
  value: string;
  label: string;
  className?: string;
}

const RadioInput = ({
  id,
  name,
  value,
  label,
  className,
  ...props
}: RadioInputProps) => {
  return (
    <div className={cn("flex items-center gap-2", className)}>
      <input
        type="radio"
        id={id}
        name={name}
        value={value}
        className="w-4 h-4 text-accent border-tertiary focus:ring-accent cursor-pointer"
        {...props}
      />
      <label htmlFor={id} className="text-secondary font-inter cursor-pointer">
        {label}
      </label>
    </div>
  );
};

export default RadioInput;
