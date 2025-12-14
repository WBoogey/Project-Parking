import type { ButtonSize, ButtonVariant } from "@/types/ComponentTypes";
import { cn } from "cn-utility";
import type { ButtonHTMLAttributes, ReactNode } from "react";

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  children: ReactNode;
  size?: ButtonSize;
  variant?: ButtonVariant;
  onClick: () => void;
  className?: string;
  disabled?: boolean;
}

const Button = ({
  children,
  size = "sm",
  variant = "default",
  onClick,
  className,
  disabled,
  ...props
}: ButtonProps) => {
  return (
    <button
      className={cn(
        "whitespace-nowrap",
        disabled ? "cursor-not-allowed opacity-50" : "cursor-pointer",
        {
          "rounded-2xl py-3 flex items-center justify-center gap-2.5 transition-colors text-primary bg-accent":
            variant === "default",
          "hover:bg-accent/80": variant === "default" && !disabled,
          "text-secondary hover:underline": variant === "plain" && !disabled,
          "text-secondary": variant === "plain" && disabled,
        },
        variant === "default" && {
          "w-36": size === "sm",
          "w-96": size === "md",
          "w-120": size === "lg",
          "w-full": size === "full",
        },
        className,
      )}
      onClick={onClick}
      disabled={disabled}
      {...props}
    >
      {children}
    </button>
  );
};

export default Button;
