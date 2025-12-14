import type { TextInputVariant } from "@/types/ComponentTypes";
import { cn } from "cn-utility";
import type { InputHTMLAttributes } from "react";

interface TextInputProps extends InputHTMLAttributes<HTMLInputElement> {
  className?: string;
  id: string;
  variant?: TextInputVariant;
}

const TextInput = ({
  className,
  type = "text",
  id,
  variant = "md",
  ...props
}: TextInputProps) => {
  return (
    <input
      type={type}
      id={id}
      className={cn(
        "text-secondary border border-tertiary focus:outline-secondary rounded-xl p-2.5 placeholder:text-tertiary",
        {
          "w-64": variant === "md",
          "w-full": variant === "full",
        },
        className,
      )}
      {...props}
    />
  );
};

export default TextInput;
