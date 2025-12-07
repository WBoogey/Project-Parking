import { cn } from "cn-utility";
import TextInput from "../atoms/TextInput";
import type { TextInputVariant } from "@/types/ComponentTypes";
import type { InputHTMLAttributes } from "react";

interface InputCompleteProps extends InputHTMLAttributes<HTMLInputElement> {
  id: string;
  label: string;
  placeholder?: string;
  variant?: TextInputVariant;
  className?: string;
  labelClassName?: string;
  inputClassName?: string;
}

const InputComplete = ({
  id,
  label,
  placeholder,
  variant = "md",
  className,
  labelClassName,
  inputClassName,
  ...props
}: InputCompleteProps) => {
  return (
    <div
      className={cn(
        "flex flex-col gap-1.5 font-inter",
        { "w-full": variant === "full" },
        className,
      )}
    >
      <label
        htmlFor={id}
        className={cn("font-semibold text-sm", labelClassName)}
      >
        {label}
      </label>
      <TextInput
        id={id}
        placeholder={placeholder}
        className={cn(inputClassName)}
        variant={variant}
        {...props}
      />
    </div>
  );
};

export default InputComplete;
