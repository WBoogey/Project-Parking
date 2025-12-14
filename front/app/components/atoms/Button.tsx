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
        "whitespace-nowrap transition-colors",
        disabled ? "cursor-not-allowed opacity-50" : "cursor-pointer",
        {
          // Default Variant (Filled)
          "rounded-2xl py-3 flex items-center justify-center gap-2.5 text-primary bg-accent":
            variant === "default",
          "hover:bg-accent/80": variant === "default" && !disabled,

          // Plain Variant (Text only)
          "text-secondary hover:underline": variant === "plain" && !disabled,
          "text-secondary": variant === "plain" && disabled,

          // Outline Variant
          "rounded-2xl py-3 flex items-center justify-center gap-2.5 bg-transparent border border-current":
            variant === "outline",
          "text-secondary border-secondary hover:bg-secondary/5":
            variant === "outline" && !disabled,
        },
        // Apply sizing to default and outline variants
        (variant === "default" || variant === "outline") && {
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
