import { useState } from "react";
import { cn } from "cn-utility";
import TextInput from "../atoms/TextInput";
import SeePassword from "../atoms/SeePassword";
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
  const [showPassword, setShowPassword] = useState(false);
  const isPassword = id === "password";

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
      {isPassword ? (
        <div className="flex items-center border border-tertiary rounded-xl overflow-hidden">
          <input
            id={id}
            type={showPassword ? "text" : "password"}
            placeholder={placeholder}
            className={cn(
              "flex-1 px-4 py-2.5 outline-none text-secondary",
              inputClassName,
            )}
            {...props}
          />
          <div className="border-l border-tertiary">
            <SeePassword visible={showPassword} setVisible={setShowPassword} />
          </div>
        </div>
      ) : (
        <TextInput
          id={id}
          placeholder={placeholder}
          className={cn(inputClassName)}
          variant={variant}
          {...props}
        />
      )}
    </div>
  );
};

export default InputComplete;
