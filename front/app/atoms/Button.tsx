import { cn } from "cn-utility";

type ButtonVariant = "sm" | "md" | "lg" | "full";

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  children: React.ReactNode;
  variant?: ButtonVariant;
  onClick: () => void;
  className?: string;
}

const Button = ({
  children,
  variant = "sm",
  onClick,
  className,
  ...props
}: ButtonProps) => {
  return (
    <button
      className={cn(
        "bg-accent text-primary rounded-2xl px-9 py-3 flex items-center justify-center gap-2.5 cursor-pointer hover:bg-accent/80 transition-colors text-sm",
        {
          "w-32.5": variant === "sm",
          "w-96": variant === "md",
          "w-120": variant === "lg",
          "w-full": variant === "full",
        },

        className,
      )}
      onClick={onClick}
      {...props}
    >
      {children}
    </button>
  );
};

export default Button;
